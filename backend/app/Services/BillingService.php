<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillingService
{
    /**
     * Calculate DPP and PPN 11% based on scheme (include or exclude).
     */
    public function calculatePpn(float $amount, string $ppnMode = 'include'): array
    {
        $ppnRate = (float) config('isp.ppn_rate', 11.0);
        $ppnMultiplier = 1.0 + ($ppnRate / 100.0);

        if (strtolower($ppnMode) === 'include') {
            $dpp = round($amount / $ppnMultiplier, 2);
            $ppn = round($amount - $dpp, 2);
            $total = round($amount, 2);
        } else {
            $dpp = round($amount, 2);
            $ppn = round($dpp * ($ppnRate / 100.0), 2);
            $total = round($dpp + $ppn, 2);
        }

        return [
            'dpp' => $dpp,
            'ppn' => $ppn,
            'total' => $total,
            'rate' => $ppnRate,
            'mode' => strtolower($ppnMode),
        ];
    }

    /**
     * Calculate prorated price for fixed date billing cycle.
     */
    public function calculateProrata(float $packagePrice, ?Carbon $startDate = null, string $billingCycleType = 'anniversary'): array
    {
        $startDate = $startDate ?? Carbon::now();
        $totalDaysInMonth = (int) $startDate->daysInMonth;
        $currentDay = (int) $startDate->day;
        $daysRemaining = max(1, $totalDaysInMonth - $currentDay + 1);

        $isProrata = ($billingCycleType === 'fixed_date') && ($daysRemaining < $totalDaysInMonth);

        if ($isProrata) {
            $prorataFactor = $daysRemaining / $totalDaysInMonth;
            $finalPrice = round($packagePrice * $prorataFactor, 2);
            $label = $startDate->translatedFormat('F Y') . " (Prorata {$daysRemaining}/{$totalDaysInMonth} Hari)";
        } else {
            $finalPrice = round($packagePrice, 2);
            $label = $startDate->translatedFormat('F Y');
        }

        return [
            'is_prorata' => $isProrata,
            'original_price' => $packagePrice,
            'final_price' => $finalPrice,
            'days_remaining' => $daysRemaining,
            'total_days' => $totalDaysInMonth,
            'period_label' => $label,
        ];
    }

    /**
     * Generate an invoice for a specific customer.
     */
    public function generateInvoiceForCustomer(Customer $customer, ?string $period = null, bool $forceProrata = false): Invoice
    {
        $package = $customer->package;
        $packagePrice = $package ? (float) $package->price : 150000.0;
        $now = Carbon::now();

        $prorataCalc = $this->calculateProrata(
            $packagePrice,
            $now,
            $forceProrata ? 'fixed_date' : $customer->billing_cycle_type
        );

        $chargeAmount = $prorataCalc['final_price'];
        $periodLabel = $period ?? $prorataCalc['period_label'];

        $calc = $this->calculatePpn($chargeAmount, $customer->ppn_scheme ?? 'include');
        $invoiceNo = 'INV-' . $now->format('Ym') . '-' . str_pad((string) rand(1000, 9999), 4, '0', STR_PAD_LEFT);

        $dueDay = (int) config('isp.postpaid_due_day', 20);
        $dueDate = $customer->billing_type === 'prepaid'
            ? $now->copy()->addMinutes(config('isp.prepaid_grace_minutes', 30))
            : $now->copy()->day(min($dueDay, $now->daysInMonth));

        return Invoice::create([
            'invoice_no' => $invoiceNo,
            'customer_id' => $customer->id,
            'billing_period' => $periodLabel,
            'dpp_amount' => $calc['dpp'],
            'ppn_amount' => $calc['ppn'],
            'ppn_mode' => $calc['mode'],
            'billing_type' => $customer->billing_type,
            'total_amount' => $calc['total'],
            'due_date' => $dueDate->toDateString(),
            'status' => 'unpaid',
            'notes' => 'Generated automatically by NETPRO Billing Engine',
        ]);
    }

    /**
     * Mass generate monthly postpaid invoices on 1st of month.
     */
    public function generateMonthlyInvoices(): array
    {
        $now = Carbon::now();
        $periodLabel = $now->translatedFormat('F Y');
        $customers = Customer::where('billing_type', 'postpaid')
            ->where('status', 'active')
            ->get();

        $generated = 0;
        $skipped = 0;

        foreach ($customers as $customer) {
            $exists = Invoice::where('customer_id', $customer->id)
                ->where('billing_period', 'like', $now->format('F Y') . '%')
                ->exists();

            if (!$exists) {
                $this->generateInvoiceForCustomer($customer, $periodLabel);
                $generated++;
            } else {
                $skipped++;
            }
        }

        return [
            'period' => $periodLabel,
            'generated_count' => $generated,
            'skipped_count' => $skipped,
        ];
    }

    /**
     * Process auto-isolir for overdue/expired customers.
     */
    public function processAutoIsolir(): array
    {
        $now = Carbon::now();
        $radiusCoa = app(RadiusCoaService::class);
        $isolatedCustomers = [];

        // 1. Check Prepaid customers whose expired_at is past
        $expiredPrepaid = Customer::where('billing_type', 'prepaid')
            ->where('status', 'active')
            ->whereNotNull('expired_at')
            ->where('expired_at', '<', $now)
            ->get();

        foreach ($expiredPrepaid as $customer) {
            $customer->update(['status' => 'isolated']);
            if ($customer->pppoe_user) {
                $radiusCoa->isolateUser($customer->pppoe_user);
            }
            $isolatedCustomers[] = [
                'cid' => $customer->cid,
                'name' => $customer->name,
                'reason' => 'Prepaid expired',
            ];
        }

        // 2. Check Postpaid customers with overdue invoices past due_date
        $overdueInvoices = Invoice::where('billing_type', 'postpaid')
            ->where('status', 'unpaid')
            ->whereNotNull('due_date')
            ->where('due_date', '<', $now->toDateString())
            ->with('customer')
            ->get();

        foreach ($overdueInvoices as $invoice) {
            $customer = $invoice->customer;
            if ($customer && $customer->status === 'active') {
                $customer->update(['status' => 'isolated']);
                if ($customer->pppoe_user) {
                    $radiusCoa->isolateUser($customer->pppoe_user);
                }
                $invoice->update(['status' => 'overdue']);
                $isolatedCustomers[] = [
                    'cid' => $customer->cid,
                    'name' => $customer->name,
                    'reason' => "Overdue invoice {$invoice->invoice_no}",
                ];
            }
        }

        return [
            'isolated_count' => count($isolatedCustomers),
            'isolated_list' => $isolatedCustomers,
        ];
    }

    /**
     * Record payment and trigger auto-journal and reactivations.
     */
    public function recordPayment(Invoice $invoice, array $paymentData): Payment
    {
        return DB::transaction(function () use ($invoice, $paymentData) {
            $now = Carbon::now();

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'payment_ref' => $paymentData['payment_ref'] ?? 'PAY-' . time() . '-' . rand(100, 999),
                'amount' => $paymentData['amount'] ?? $invoice->total_amount,
                'payment_method' => $paymentData['payment_method'] ?? 'QRIS Instant',
                'paid_at' => $now,
                'gateway_response' => $paymentData['gateway_response'] ?? 'PAID_SUCCESS',
            ]);

            $invoice->update([
                'status' => 'paid',
                'paid_date' => $now->toDateString(),
                'payment_method' => $payment->payment_method,
            ]);

            // Reactivate Customer & extend prepaid expired_at
            $customer = $invoice->customer;
            if ($customer) {
                $updateData = ['status' => 'active'];
                if ($customer->billing_type === 'prepaid') {
                    $baseDate = ($customer->expired_at && $customer->expired_at->isFuture())
                        ? $customer->expired_at
                        : $now;
                    $updateData['expired_at'] = $baseDate->copy()->addDays((int) config('isp.prepaid_cycle_days', 30));
                }
                $customer->update($updateData);

                // Restore RADIUS user
                if ($customer->pppoe_user) {
                    app(RadiusCoaService::class)->restoreUser($customer->pppoe_user);
                }
            }

            // Trigger double-entry accounting journal
            try {
                app(AccountingService::class)->createJournalForInvoicePayment($invoice, $payment);
            } catch (\Throwable $t) {
                Log::error("Failed to generate journal for invoice #{$invoice->invoice_no}: " . $t->getMessage());
            }

            return $payment;
        });
    }
}
