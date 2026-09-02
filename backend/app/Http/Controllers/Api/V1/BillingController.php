<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Services\BillingService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Invoice::with('customer.package', 'payments');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('billing_period')) {
            $query->where('billing_period', 'like', "%{$request->billing_period}%");
        }
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        $invoices = $query->orderBy('id', 'desc')->paginate($request->get('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $invoices,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $invoice = Invoice::with('customer.package', 'payments')->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $invoice,
        ]);
    }

    public function generateForCustomer(Request $request, BillingService $billingService): JsonResponse
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'period' => 'nullable|string',
            'force_prorata' => 'nullable|boolean',
        ]);

        $customer = Customer::findOrFail($request->customer_id);
        $invoice = $billingService->generateInvoiceForCustomer(
            $customer,
            $request->period,
            (bool) $request->get('force_prorata', false)
        );

        return response()->json([
            'status' => 'success',
            'message' => "Invoice baru {$invoice->invoice_no} berhasil diterbitkan.",
            'data' => $invoice->load('customer'),
        ], 201);
    }

    public function generateMonthly(BillingService $billingService): JsonResponse
    {
        $result = $billingService->generateMonthlyInvoices();
        return response()->json([
            'status' => 'success',
            'message' => "Proses penagihan massal selesai.",
            'data' => $result,
        ]);
    }

    public function pay(Request $request, int $id, BillingService $billingService): JsonResponse
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status === 'paid') {
            return response()->json([
                'status' => 'error',
                'message' => 'Invoice ini sudah lunas sebelumnya.',
            ], 422);
        }

        $payment = $billingService->recordPayment($invoice, [
            'payment_ref' => $request->get('payment_ref', 'PAY-' . time() . '-' . rand(100, 999)),
            'amount' => $request->get('amount', $invoice->total_amount),
            'payment_method' => $request->get('payment_method', 'QRIS Instant / VA'),
            'gateway_response' => 'SUCCESS_VERIFIED',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Pembayaran invoice {$invoice->invoice_no} berhasil dicatat dan diverifikasi.",
            'data' => [
                'invoice' => $invoice->fresh(),
                'payment' => $payment,
            ],
        ]);
    }

    public function sendReminder(int $id, NotificationService $notifService): JsonResponse
    {
        $invoice = Invoice::with('customer')->findOrFail($id);
        $sent = $notifService->sendInvoiceReminder($invoice);

        return response()->json([
            'status' => $sent ? 'success' : 'error',
            'message' => $sent ? "Notifikasi reminder WhatsApp berhasil dikirim ke {$invoice->customer->phone}." : "Gagal mengirim notifikasi.",
        ]);
    }

    public function calculateTaxSimulation(Request $request, BillingService $billingService): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'mode' => 'required|in:include,exclude',
        ]);

        $result = $billingService->calculatePpn((float) $request->amount, $request->mode);

        return response()->json([
            'status' => 'success',
            'data' => $result,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $invoice = Invoice::findOrFail($id);
        $no = $invoice->invoice_no;
        $invoice->delete();

        return response()->json([
            'status' => 'success',
            'message' => "Faktur {$no} berhasil dihapus dari sistem.",
        ]);
    }
}
