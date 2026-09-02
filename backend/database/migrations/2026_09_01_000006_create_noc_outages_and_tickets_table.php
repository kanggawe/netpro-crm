<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('noc_outages', function (Blueprint $table) {
            $table->id();
            $table->string('outage_no', 50)->unique();
            $table->string('location', 200);
            $table->string('issue_type', 100); // CABLE CUT FO, POWER OUTAGE, OLT PORT FAILURE
            $table->integer('affected_users')->default(0);
            $table->string('tech_name', 100)->nullable();
            $table->string('status', 50)->default('IN_PROGRESS'); // OPEN, IN_PROGRESS, RESOLVED
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_no', 50)->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('category', 100); // INTERNET_LOSS, LOS_RED_LIGHT, ROUTER_CONFIG, BILLING
            $table->string('priority', 20)->default('MEDIUM'); // LOW, MEDIUM, HIGH, CRITICAL
            $table->string('assigned_tech', 100)->nullable();
            $table->integer('sla_minutes')->default(120);
            $table->string('status', 50)->default('OPEN'); // OPEN, IN_PROGRESS, RESOLVED, CLOSED
            $table->text('description')->nullable();
            $table->text('solution')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('noc_outages');
    }
};
