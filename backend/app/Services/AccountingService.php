<?php

namespace App\Services;

use App\Models\CoaAccount;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\Payment;
use App\Models\TaxRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    /**
     * Create double-entry journal entry for paid invoices.
     * [Debit] 1101 - Kas & Setara Kas (BCA / Mandiri) = Total Amount
     * [Kredit] 4101 - Pendapatan Jasa Internet = DPP
     * [Kredit] 2102 - Hutang PPN Keluaran 11% = PPN
     */
    public function createJournalForInvoicePayment(Invoice $invoice, Payment $payment): string
    {
        return DB::transaction(function () use ($invoice, $payment) {
            $journalNo = 'JV-' . Carbon::now()->format('Ym') . '-' . str_pad((string) rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            $transDate = $invoice->paid_date ?? Carbon::now()->toDateString();
            $customerName = $invoice->customer ? $invoice->customer->name : 'Pelanggan';

            // 1. Debit Kas / Bank
            JournalEntry::create([
                'journal_no' => $journalNo,
                'trans_date' => $transDate,
                'account_code' => '1101',
                'description' => "Penerimaan Pembayaran {$invoice->invoice_no} ({$customerName}) via {$payment->payment_method}",
                'debit' => $invoice->total_amount,
                'credit' => 0,
                'ref_type' => 'INVOICE',
                'ref_id' => $invoice->id,
            ]);
            $this->adjustAccountBalance('1101', $invoice->total_amount, 'debit');

            // 2. Kredit Pendapatan Jasa Internet (DPP)
            JournalEntry::create([
                'journal_no' => $journalNo,
                'trans_date' => $transDate,
                'account_code' => '4101',
                'description' => "Pendapatan Langganan Internet {$invoice->invoice_no} ({$customerName})",
                'debit' => 0,
                'credit' => $invoice->dpp_amount,
                'ref_type' => 'INVOICE',
                'ref_id' => $invoice->id,
            ]);
            $this->adjustAccountBalance('4101', $invoice->dpp_amount, 'credit');

            // 3. Kredit Hutang PPN Keluaran (jika ada nilai PPN > 0)
            if ($invoice->ppn_amount > 0) {
                JournalEntry::create([
                    'journal_no' => $journalNo,
                    'trans_date' => $transDate,
                    'account_code' => '2102',
                    'description' => "PPN Keluaran 11% Faktur {$invoice->invoice_no}",
                    'debit' => 0,
                    'credit' => $invoice->ppn_amount,
                    'ref_type' => 'INVOICE',
                    'ref_id' => $invoice->id,
                ]);
                $this->adjustAccountBalance('2102', $invoice->ppn_amount, 'credit');
            }

            return $journalNo;
        });
    }

    /**
     * Adjust account balance according to normal balance (Debit/Kredit).
     */
    protected function adjustAccountBalance(string $code, float $amount, string $entryType): void
    {
        $account = CoaAccount::where('code', $code)->first();
        if ($account) {
            $normal = strtolower($account->normal_balance);
            if ($normal === 'debit') {
                $account->balance += ($entryType === 'debit') ? $amount : -$amount;
            } else {
                $account->balance += ($entryType === 'credit') ? $amount : -$amount;
            }
            $account->save();
        }
    }

    /**
     * Calculate Kominfo PNBP Regulatory Contributions (USO 1.25% & BHP 0.50%).
     */
    public function calculateRegulatoryFees(float $grossRevenue): array
    {
        $usoRate = (float) config('isp.uso_rate', 1.25);
        $bhpRate = (float) config('isp.bhp_rate', 0.50);

        $usoAmount = round($grossRevenue * ($usoRate / 100.0), 2);
        $bhpAmount = round($grossRevenue * ($bhpRate / 100.0), 2);
        $totalFees = round($usoAmount + $bhpAmount, 2);

        return [
            'gross_revenue' => $grossRevenue,
            'uso_rate' => $usoRate,
            'uso_amount' => $usoAmount,
            'bhp_rate' => $bhpRate,
            'bhp_amount' => $bhpAmount,
            'total_regulatory_fees' => $totalFees,
        ];
    }

    /**
     * Create e-Bupot PPh 23 withholding tax record.
     */
    public function recordTaxWithholding(array $data): TaxRecord
    {
        $dpp = (float) ($data['dpp_amount'] ?? 0);
        $hasNpwp = !empty($data['npwp']);
        $rate = (float) ($data['rate_percent'] ?? ($hasNpwp ? config('isp.pph23_rate', 2.0) : 4.0));
        $taxAmount = round($dpp * ($rate / 100.0), 2);

        $bupotNo = 'BPT-' . Carbon::now()->format('Ym') . '-' . str_pad((string) rand(1000, 9999), 4, '0', STR_PAD_LEFT);

        return TaxRecord::create([
            'bupot_no' => $bupotNo,
            'tax_type' => $data['tax_type'] ?? 'PPh 23',
            'vendor_name' => $data['vendor_name'],
            'npwp' => $data['npwp'] ?? null,
            'obj_income' => $data['obj_income'] ?? 'Sewa Tiang FO & Upstream Bandwidth',
            'dpp_amount' => $dpp,
            'rate_percent' => $rate,
            'tax_amount' => $taxAmount,
            'period' => $data['period'] ?? Carbon::now()->format('m-Y'),
            'status' => 'TERBIT',
            'ntpn' => $data['ntpn'] ?? null,
        ]);
    }
}
