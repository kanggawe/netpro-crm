<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addons', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('category', 50)->default('ADDON PRO'); // ADDON PRO, IP PUBLIK, MESH WIFI, CCTV CLOUD, IPTV
            $table->decimal('price', 15, 2);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('title', 150);
            $table->decimal('discount_amount', 15, 2);
            $table->integer('quota')->default(100);
            $table->integer('used_count')->default(0);
            $table->date('valid_until')->nullable();
            $table->string('status', 20)->default('AKTIF'); // AKTIF, EXPIRED, HABIS
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promos');
        Schema::dropIfExists('addons');
    }
};
