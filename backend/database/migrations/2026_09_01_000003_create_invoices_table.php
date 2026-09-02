<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no', 50)->unique();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('billing_period', 50); // e.g. "September 2026"
            $table->decimal('dpp_amount', 15, 2);
            $table->decimal('ppn_amount', 15, 2);
            $table->string('ppn_mode', 20)->default('include'); // include, exclude
            $table->string('billing_type', 20)->default('postpaid'); // prepaid, postpaid
            $table->decimal('total_amount', 15, 2);
            $table->date('due_date')->nullable();
            $table->date('paid_date')->nullable();
            $table->string('payment_method', 50)->nullable(); // QRIS, Transfer BCA, Mandiri VA, Cash
            $table->string('status', 20)->default('unpaid'); // unpaid, paid, overdue, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('payment_ref', 100)->unique();
            $table->decimal('amount', 15, 2);
            $table->string('payment_method', 50);
            $table->timestamp('paid_at');
            $table->string('gateway_response', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
    }
};
