<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coa_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->string('category', 50); // ASET, KEWAJIBAN, EKUITAS, PENDAPATAN, BEBAN
            $table->string('normal_balance', 10)->default('Debit'); // Debit, Kredit
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });

        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('journal_no', 50)->index();
            $table->date('trans_date');
            $table->string('account_code', 50);
            $table->text('description');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->string('ref_type', 50)->nullable(); // INVOICE, OPEX, CASH, MANUAL
            $table->unsignedBigInteger('ref_id')->nullable();
            $table->timestamps();

            $table->foreign('account_code')->references('code')->on('coa_accounts')->cascadeOnDelete();
        });

        Schema::create('tax_records', function (Blueprint $table) {
            $table->id();
            $table->string('bupot_no', 50)->unique();
            $table->string('tax_type', 50); // PPh 23, PPN 11%, USO 1.25%, BHP 0.50%
            $table->string('vendor_name', 150);
            $table->string('npwp', 50)->nullable();
            $table->string('obj_income', 150)->nullable(); // Sewa Tiang FO, Sewa Core, Jasa Konsultan IT
            $table->decimal('dpp_amount', 15, 2);
            $table->decimal('rate_percent', 5, 2)->default(2.00);
            $table->decimal('tax_amount', 15, 2);
            $table->string('period', 50); // e.g. "08-2026"
            $table->string('status', 50)->default('TERBIT'); // TERBIT, DISETOR, DILAPORKAN
            $table->string('ntpn', 100)->nullable(); // Nomor Transaksi Penerimaan Negara
            $table->timestamps();
        });

        Schema::create('opex_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_no', 50)->unique();
            $table->date('exp_date');
            $table->string('category', 100); // SEWA TIANG & FO, BANDWIDTH UPSTREAM, LISTRIK POP & DATA CENTER, GAJI & OPEX
            $table->string('vendor_name', 150)->nullable();
            $table->text('description');
            $table->decimal('amount', 15, 2);
            $table->string('bank_account', 100)->nullable();
            $table->string('approver', 100)->nullable();
            $table->string('status', 50)->default('DISETUJUI'); // DRAFT, MENUNGGU_APPROVAL, DISETUJUI, DITOLAK
            $table->timestamps();
        });

        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('trans_date');
            $table->text('description');
            $table->string('bank_account', 100)->default('BCA Operasional');
            $table->string('type', 10); // in, out
            $table->decimal('amount', 15, 2);
            $table->string('status', 50)->default('VERIFIED');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
        Schema::dropIfExists('opex_expenses');
        Schema::dropIfExists('tax_records');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('coa_accounts');
    }
};
