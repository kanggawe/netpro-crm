<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send WhatsApp notification (Mock / Gateway integration).
     */
    public function sendWhatsApp(string $phone, string $message): array
    {
        Log::info("WhatsApp Notification to [{$phone}]: {$message}");
        return [
            'success' => true,
            'phone' => $phone,
            'message' => $message,
            'status' => 'QUEUED_DELIVERED',
        ];
    }

    /**
     * Send payment reminder message for an invoice.
     */
    public function sendInvoiceReminder(Invoice $invoice): bool
    {
        $customer = $invoice->customer;
        if (!$customer || empty($customer->phone)) {
            return false;
        }

        $formattedAmount = 'Rp ' . number_format($invoice->total_amount, 0, ',', '.');
        $dueDate = $invoice->due_date ? $invoice->due_date->format('d/m/Y') : '-';

        $msg = "Yth. {$customer->name},\n\n";
        $msg .= "Tagihan Internet NETPRO Anda periode *{$invoice->billing_period}* sebesar *{$formattedAmount}* telah terbit.\n";
        $msg .= "Nomor Tagihan: *{$invoice->invoice_no}*\n";
        $msg .= "Jatuh Tempo: *{$dueDate}*\n\n";
        $msg .= "Silakan lakukan pembayaran melalui QRIS / Virtual Account pada portal pelanggan atau hubungi Customer Care kami.\n\nTerima kasih.";

        $this->sendWhatsApp($customer->phone, $msg);
        return true;
    }
}
