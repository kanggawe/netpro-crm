<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 50)->unique();
            $table->string('name', 150);
            $table->string('email', 100)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('division', 100); // Field Engineering, NOC & Infrastructure, Finance, Marketing
            $table->string('position', 100);
            $table->string('contract_status', 50)->default('TETAP'); // TETAP, KONTRAK, MAGANG
            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->decimal('allowance', 15, 2)->default(0);
            $table->string('bank_name', 100)->default('BCA');
            $table->string('bank_account', 100)->nullable();
            $table->string('status', 20)->default('active'); // active, inactive, terminated
            $table->timestamps();
        });

        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('employee_name', 150);
            $table->string('division', 100)->nullable();
            $table->string('leave_type', 100); // TAHUNAN, SAKIT, MELAHIRKAN, IZIN
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('duration_days')->default(1);
            $table->text('reason')->nullable();
            $table->string('status', 50)->default('PENDING'); // PENDING, APPROVED, REJECTED
            $table->timestamps();
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('employee_name', 150);
            $table->string('division', 100)->nullable();
            $table->date('att_date');
            $table->string('shift_type', 100)->default('NORMAL');
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->string('gps_location', 255)->nullable();
            $table->decimal('gps_lat', 10, 6)->nullable();
            $table->decimal('gps_lng', 10, 6)->nullable();
            $table->string('status', 50)->default('TEPAT WAKTU'); // TEPAT WAKTU, TERLAMBAT, PULANG CEPAT, ALFA
            $table->timestamps();
        });

        Schema::create('kpi_indicators', function (Blueprint $table) {
            $table->id();
            $table->string('division', 100);
            $table->string('name', 150);
            $table->string('target', 100);
            $table->integer('weight')->default(25);
            $table->string('method', 100)->nullable();
            $table->string('status', 50)->default('AKTIF');
            $table->timestamps();
        });

        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('employee_name', 150);
            $table->string('division', 100)->nullable();
            $table->string('position', 100)->nullable();
            $table->string('period', 50); // e.g. "Q3 2026"
            $table->decimal('tech_score', 5, 2)->default(90);
            $table->decimal('discipline_score', 5, 2)->default(90);
            $table->decimal('total_score', 5, 2)->default(90);
            $table->text('notes')->nullable();
            $table->string('supervisor_name', 150)->nullable();
            $table->timestamps();
        });

        Schema::create('salary_components', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->string('category', 50); // PENDAPATAN, POTONGAN
            $table->string('formula', 150)->nullable();
            $table->string('borne_by', 50)->default('Perusahaan');
            $table->string('status', 50)->default('AKTIF');
            $table->timestamps();
        });

        Schema::create('payroll_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('employee_name', 150);
            $table->string('period', 50); // e.g. "Agustus 2026"
            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->decimal('allowance', 15, 2)->default(0);
            $table->decimal('bonus', 15, 2)->default(0); // Insentif BAST / Kinerja
            $table->decimal('deductions', 15, 2)->default(0); // BPJS / PPh 21
            $table->decimal('thp', 15, 2)->default(0); // Take Home Pay
            $table->string('status', 50)->default('APPROVED'); // DRAFT, APPROVED, TRANSFERRED
            $table->string('bank_name', 100)->nullable();
            $table->string('account_no', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('bonus_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('employee_name', 150);
            $table->string('role', 100)->nullable();
            $table->string('bast_no', 100)->nullable();
            $table->integer('points')->default(10);
            $table->decimal('rate', 15, 2)->default(50000);
            $table->decimal('total_amount', 15, 2)->default(500000);
            $table->string('status', 50)->default('TERVERIFIKASI'); // PENDING, TERVERIFIKASI, CAIR
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bonus_claims');
        Schema::dropIfExists('payroll_records');
        Schema::dropIfExists('salary_components');
        Schema::dropIfExists('performance_reviews');
        Schema::dropIfExists('kpi_indicators');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('leaves');
        Schema::dropIfExists('employees');
    }
};
