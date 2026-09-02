<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->string('survey_no', 50)->unique();
            $table->string('customer_name', 150);
            $table->string('phone', 50);
            $table->text('address');
            $table->decimal('gps_lat', 10, 6)->default(-6.289100);
            $table->decimal('gps_lng', 10, 6)->default(106.918200);
            $table->string('nearest_odp', 100)->nullable();
            $table->integer('distance_m')->default(50);
            $table->string('tech_name', 100)->nullable();
            $table->string('status', 50)->default('APPROVED'); // PENDING, APPROVED, REJECTED
            $table->string('attenuation', 50)->default('-18.2 dBm');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('wo_no', 50)->unique();
            $table->string('customer_name', 150);
            $table->string('package_name', 100)->nullable();
            $table->string('ont_type', 100)->nullable();
            $table->string('ont_sn', 100)->nullable();
            $table->string('tech_name', 100)->nullable();
            $table->string('odp_port', 100)->nullable();
            $table->string('attenuation', 50)->nullable();
            $table->string('status', 50)->default('AKTIF & ONLINE'); // DRAFT, ASSIGNED, IN_PROGRESS, AKTIF & ONLINE, CANCELLED
            $table->string('bast_no', 100)->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_orders');
        Schema::dropIfExists('surveys');
    }
};
