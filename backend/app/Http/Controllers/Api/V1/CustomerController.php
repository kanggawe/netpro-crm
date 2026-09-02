<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\Package;
use App\Services\BillingService;
use App\Services\RadiusCoaService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Customer::with('package', 'radiusUser');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('billing_type')) {
            $query->where('billing_type', $request->billing_type);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('cid', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('pppoe_user', 'like', "%{$s}%");
            });
        }

        $customers = $query->orderBy('id', 'desc')->paginate($request->get('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $customers,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $customer = Customer::with(['package', 'invoices', 'tickets', 'radiusUser'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $customer,
        ]);
    }

    public function store(Request $request, RadiusCoaService $radiusService, BillingService $billingService): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'nik' => 'required|string|max:50',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:100',
            'address' => 'required|string',
            'gps_lat' => 'nullable|numeric',
            'gps_lng' => 'nullable|numeric',
            'package_id' => 'required|exists:packages,id',
            'ppn_scheme' => 'nullable|in:include,exclude',
            'auth_method' => 'nullable|in:pppoe,hotspot,static',
            'pppoe_user' => 'nullable|string|max:100',
            'pppoe_password' => 'nullable|string|max:100',
            'billing_type' => 'nullable|in:prepaid,postpaid',
            'billing_cycle_type' => 'nullable|in:anniversary,fixed_date',
        ]);

        $cid = 'CID-' . rand(100000, 999999);
        $rawNik = preg_replace('/[^0-9]/', '', $validated['nik']);
        $nikPrefix = strlen($rawNik) >= 8 ? substr($rawNik, 0, 8) : (!empty($rawNik) ? $rawNik : '32750101');
        $nameParts = explode(' ', trim($validated['name']));
        $firstName = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $nameParts[0] ?? 'USER'));
        $defaultPppoeUser = $nikPrefix . '-' . $firstName;

        $customer = Customer::create([
            'cid' => $cid,
            'name' => $validated['name'],
            'nik' => $validated['nik'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'],
            'gps_lat' => $validated['gps_lat'] ?? -6.289100,
            'gps_lng' => $validated['gps_lng'] ?? 106.918200,
            'package_id' => $validated['package_id'],
            'ppn_scheme' => $validated['ppn_scheme'] ?? 'include',
            'auth_method' => $validated['auth_method'] ?? 'pppoe',
            'pppoe_user' => $validated['pppoe_user'] ?? $defaultPppoeUser,
            'pppoe_password' => $validated['pppoe_password'] ?? (string) rand(100000, 999999),
            'billing_type' => $validated['billing_type'] ?? 'postpaid',
            'billing_cycle_type' => $validated['billing_cycle_type'] ?? 'anniversary',
            'status' => 'inactive',
        ]);

        // Auto sync with RADIUS
        $radiusService->syncUser($customer);

        AuditLog::log(auth()->user()->username ?? 'system', 'CUSTOMER_REGISTER', "Registered customer CID {$customer->cid}");

        return response()->json([
            'status' => 'success',
            'message' => 'Pelanggan berhasil didaftarkan.',
            'data' => $customer->load('package', 'radiusUser'),
        ], 201);
    }

    public function update(Request $request, int $id, RadiusCoaService $radiusService): JsonResponse
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:150',
            'phone' => 'sometimes|required|string|max:50',
            'email' => 'nullable|email|max:100',
            'address' => 'sometimes|required|string',
            'gps_lat' => 'nullable|numeric',
            'gps_lng' => 'nullable|numeric',
            'package_id' => 'sometimes|required|exists:packages,id',
            'ppn_scheme' => 'nullable|in:include,exclude',
            'billing_type' => 'nullable|in:prepaid,postpaid',
            'billing_cycle_type' => 'nullable|in:anniversary,fixed_date',
            'status' => 'nullable|in:active,inactive,isolated,terminated',
            'pppoe_password' => 'nullable|string|max:100',
        ]);

        $customer->update($validated);
        $radiusService->syncUser($customer);

        AuditLog::log(auth()->user()->username ?? 'system', 'CUSTOMER_UPDATE', "Updated customer CID {$customer->cid}");

        return response()->json([
            'status' => 'success',
            'message' => 'Data pelanggan berhasil diperbarui.',
            'data' => $customer->load('package'),
        ]);
    }

    public function setOnline(int $id, BillingService $billingService, RadiusCoaService $radiusService): JsonResponse
    {
        $customer = Customer::findOrFail($id);
        $now = Carbon::now();

        $updateData = ['status' => 'active'];
        if ($customer->billing_type === 'prepaid') {
            $updateData['expired_at'] = $now->copy()->addMinutes(config('isp.prepaid_grace_minutes', 30));
        }

        $customer->update($updateData);
        $radiusService->restoreUser($customer->pppoe_user);

        // Generate initial invoice if not exists
        if ($customer->invoices()->count() === 0) {
            $billingService->generateInvoiceForCustomer($customer);
        }

        return response()->json([
            'status' => 'success',
            'message' => "Pelanggan {$customer->name} telah diaktifkan dan terhubung.",
            'data' => $customer->load('package', 'invoices'),
        ]);
    }

    public function isolate(int $id, RadiusCoaService $radiusService): JsonResponse
    {
        $customer = Customer::findOrFail($id);
        $customer->update(['status' => 'isolated']);
        $radiusService->isolateUser($customer->pppoe_user);

        return response()->json([
            'status' => 'success',
            'message' => "Pelanggan {$customer->name} telah diisolir dan sesi diputus.",
        ]);
    }

    public function quickTopup(int $id, Request $request, BillingService $billingService): JsonResponse
    {
        $customer = Customer::findOrFail($id);
        $now = Carbon::now();

        $package = $customer->package;
        $amount = $package ? (float) $package->price : 150000;

        $invoice = $billingService->generateInvoiceForCustomer($customer, 'Top-Up Paket Prabayar 30 Hari');
        $payment = $billingService->recordPayment($invoice, [
            'payment_ref' => 'TOPUP-' . time(),
            'amount' => $amount,
            'payment_method' => $request->get('payment_method', 'QRIS Instant'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Top-up paket untuk {$customer->name} berhasil diproses. Masa aktif bertambah 30 hari.",
            'data' => [
                'customer' => $customer->fresh(),
                'invoice' => $invoice,
                'payment' => $payment,
            ],
        ]);
    }

    public function export(Request $request): JsonResponse
    {
        $customers = Customer::with('package')->orderBy('id', 'asc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $customers,
        ]);
    }

    public function import(Request $request, RadiusCoaService $radiusService): JsonResponse
    {
        $request->validate([
            'customers' => 'required|array|min:1',
            'customers.*.name' => 'required|string|max:150',
            'customers.*.phone' => 'required|string|max:50',
        ]);

        $imported = [];
        $errors = [];
        $allPackages = Package::all();

        foreach ($request->customers as $index => $row) {
            try {
                $rawNik = !empty($row['nik']) ? preg_replace('/[^0-9]/', '', (string)$row['nik']) : '';
                $nameParts = explode(' ', trim($row['name']));
                $firstName = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $nameParts[0] ?? 'USER'));
                $nikPrefix = strlen($rawNik) >= 8 ? substr($rawNik, 0, 8) : '32750101';
                $defaultPppoeUser = $nikPrefix . '-' . $firstName;

                // Match package by ID or name
                $packageId = 1;
                if (!empty($row['package_id']) && is_numeric($row['package_id'])) {
                    $pkg = $allPackages->firstWhere('id', (int)$row['package_id']);
                    if ($pkg) $packageId = $pkg->id;
                } elseif (!empty($row['package_name'])) {
                    $pkg = $allPackages->first(function($p) use ($row) {
                        return strcasecmp($p->name, trim($row['package_name'])) === 0 || str_contains(strtolower($p->name), strtolower(trim($row['package_name'])));
                    });
                    if ($pkg) $packageId = $pkg->id;
                }

                $cid = !empty($row['cid']) ? $row['cid'] : ('CID-' . rand(100000, 999999));
                $pppoeUser = !empty($row['pppoe_user']) ? $row['pppoe_user'] : $defaultPppoeUser;
                $pppoePassword = !empty($row['pppoe_password']) ? (string)$row['pppoe_password'] : (string)rand(100000, 999999);

                // Create or update customer
                $customer = Customer::updateOrCreate(
                    ['cid' => $cid],
                    [
                        'name' => $row['name'],
                        'nik' => !empty($row['nik']) ? (string)$row['nik'] : ($nikPrefix . rand(10000000, 99999999)),
                        'phone' => $row['phone'],
                        'email' => !empty($row['email']) ? $row['email'] : null,
                        'address' => !empty($row['address']) ? $row['address'] : 'Alamat belum diatur',
                        'gps_lat' => !empty($row['gps_lat']) ? (float)$row['gps_lat'] : -6.289100,
                        'gps_lng' => !empty($row['gps_lng']) ? (float)$row['gps_lng'] : 106.918200,
                        'package_id' => $packageId,
                        'ppn_scheme' => in_array(strtolower($row['ppn_scheme'] ?? ''), ['exclude', 'include']) ? strtolower($row['ppn_scheme']) : 'include',
                        'auth_method' => in_array(strtolower($row['auth_method'] ?? ''), ['pppoe', 'hotspot', 'static']) ? strtolower($row['auth_method']) : 'pppoe',
                        'pppoe_user' => $pppoeUser,
                        'pppoe_password' => $pppoePassword,
                        'billing_type' => in_array(strtolower($row['billing_type'] ?? ''), ['prepaid', 'postpaid']) ? strtolower($row['billing_type']) : 'postpaid',
                        'billing_cycle_type' => in_array(strtolower($row['billing_cycle_type'] ?? ''), ['anniversary', 'fixed_date']) ? strtolower($row['billing_cycle_type']) : 'anniversary',
                        'status' => in_array(strtolower($row['status'] ?? ''), ['active', 'inactive', 'isolated']) ? strtolower($row['status']) : 'active',
                    ]
                );

                // Auto-sync user to RADIUS core
                $radiusService->syncUser($customer);
                $imported[] = $customer->cid;
            } catch (\Exception $e) {
                $errors[] = "Baris " . ($index + 1) . " (" . ($row['name'] ?? 'Unknown') . "): " . $e->getMessage();
            }
        }

        AuditLog::log(auth()->user()->username ?? 'system', 'CUSTOMER_BULK_IMPORT', 'Bulk imported ' . count($imported) . ' customers from spreadsheet.');

        return response()->json([
            'status' => 'success',
            'message' => 'Proses import spreadsheet selesai. Berhasil mengimpor ' . count($imported) . ' pelanggan.',
            'imported_count' => count($imported),
            'errors_count' => count($errors),
            'errors' => $errors,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $customer = Customer::findOrFail($id);
        $name = $customer->name;
        $customer->delete();
        AuditLog::log(auth()->user()->username ?? 'system', 'CUSTOMER_DELETE', "Deleted customer {$name}");

        return response()->json([
            'status' => 'success',
            'message' => "Pelanggan {$name} berhasil dihapus dari sistem.",
        ]);
    }
}
