<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('cid', 50)->unique();
            $table->string('name', 150);
            $table->string('nik', 50);
            $table->string('phone', 50);
            $table->string('email', 100)->nullable();
            $table->text('address');
            $table->decimal('gps_lat', 10, 6)->default(-6.289100);
            $table->decimal('gps_lng', 10, 6)->default(106.918200);
            $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();
            $table->string('ppn_scheme', 20)->default('include'); // include, exclude
            $table->string('auth_method', 20)->default('pppoe'); // pppoe, hotspot, static
            $table->string('pppoe_user', 100)->nullable()->index();
            $table->string('pppoe_password', 100)->nullable();
            $table->string('billing_type', 20)->default('postpaid'); // prepaid, postpaid
            $table->string('billing_cycle_type', 30)->default('anniversary'); // anniversary (Rolling 30 Hari), fixed_date (Reset Akhir Bulan)
            $table->timestamp('expired_at')->nullable();
            $table->string('status', 20)->default('inactive'); // active, inactive, isolated, terminated
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
