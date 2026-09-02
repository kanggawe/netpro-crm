<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CashTransaction;
use App\Models\CoaAccount;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\OpexExpense;
use App\Models\TaxRecord;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function coaList(): JsonResponse
    {
        $accounts = CoaAccount::orderBy('code', 'asc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $accounts,
        ]);
    }

    public function journals(Request $request): JsonResponse
    {
        $journals = JournalEntry::with('account')
            ->orderBy('id', 'desc')
            ->paginate($request->get('per_page', 30));

        return response()->json([
            'status' => 'success',
            'data' => $journals,
        ]);
    }

    public function taxes(): JsonResponse
    {
        $taxes = TaxRecord::orderBy('id', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $taxes,
        ]);
    }

    public function storeTax(Request $request, AccountingService $accountingService): JsonResponse
    {
        $validated = $request->validate([
            'vendor_name' => 'required|string|max:150',
            'npwp' => 'nullable|string|max:50',
            'obj_income' => 'nullable|string|max:150',
            'dpp_amount' => 'required|numeric|min:0',
            'rate_percent' => 'nullable|numeric',
            'period' => 'nullable|string',
            'ntpn' => 'nullable|string',
        ]);

        $tax = $accountingService->recordTaxWithholding($validated);

        return response()->json([
            'status' => 'success',
            'message' => "Bukti Potong PPh 23 {$tax->bupot_no} berhasil diterbitkan.",
            'data' => $tax,
        ], 201);
    }

    public function opex(Request $request): JsonResponse
    {
        $expenses = OpexExpense::orderBy('id', 'desc')->paginate($request->get('per_page', 20));
        return response()->json([
            'status' => 'success',
            'data' => $expenses,
        ]);
    }

    public function storeOpex(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => 'required|string|max:100',
            'vendor_name' => 'nullable|string|max:150',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'bank_account' => 'nullable|string',
            'approver' => 'nullable|string',
        ]);

        $voucherNo = 'OPX-' . Carbon::now()->format('Ymd') . '-' . rand(100, 999);
        $opex = OpexExpense::create(array_merge($validated, [
            'voucher_no' => $voucherNo,
            'exp_date' => Carbon::now()->toDateString(),
            'status' => 'DISETUJUI',
        ]));

        // Record Cash Outflow
        CashTransaction::create([
            'trans_date' => Carbon::now()->toDateString(),
            'description' => "Pengeluaran OPEX {$opex->voucher_no}: {$opex->description}",
            'bank_account' => $opex->bank_account ?? 'BCA Operasional',
            'type' => 'out',
            'amount' => $opex->amount,
            'status' => 'VERIFIED',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Voucher Pengeluaran OPEX {$opex->voucher_no} berhasil dicatat.",
            'data' => $opex,
        ], 201);
    }

    public function cashflow(): JsonResponse
    {
        $cash = CashTransaction::orderBy('id', 'desc')->limit(50)->get();
        $totalIn = CashTransaction::where('type', 'in')->sum('amount');
        $totalOut = CashTransaction::where('type', 'out')->sum('amount');

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_inflow' => $totalIn,
                'total_outflow' => $totalOut,
                'net_balance' => $totalIn - $totalOut,
                'transactions' => $cash,
            ],
        ]);
    }

    public function regulatorySummary(AccountingService $accountingService): JsonResponse
    {
        // Calculate gross revenue from paid invoices DPP
        $grossRevenue = (float) Invoice::where('status', 'paid')->sum('dpp_amount');
        $calc = $accountingService->calculateRegulatoryFees($grossRevenue);

        return response()->json([
            'status' => 'success',
            'data' => $calc,
        ]);
    }
}
