<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 50)->unique();
            $table->string('name', 150);
            $table->string('category', 100)->default('ONT / MODEM'); // ONT / MODEM, DROP CABLE FO, SFP TRANSCEIVER, PATCH CORD, OTB
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(10);
            $table->string('unit', 20)->default('Pcs');
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->string('status', 50)->default('AMAN'); // AMAN, MENIPIS, HABIS
            $table->timestamps();
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('phone', 50);
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('package_interest', 100)->nullable();
            $table->string('sales_agent', 100)->nullable();
            $table->string('status', 50)->default('NEW LEAD'); // NEW LEAD, CONTACTED, SURVEY_SCHEDULED, WON, LOST
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->text('address');
            $table->string('phone', 50)->nullable();
            $table->string('manager', 150)->nullable();
            $table->integer('subs_count')->default(0);
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('inventory_items');
    }
};
