<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'radius';

    public function up(): void
    {
        if (!Schema::connection('radius')->hasTable('radius_nas')) {
            Schema::connection('radius')->create('radius_nas', function (Blueprint $table) {
                $table->id();
                $table->string('nasname', 128)->unique();
                $table->string('shortname', 32)->nullable();
                $table->string('type', 30)->default('mikrotik');
                $table->integer('ports')->default(1812);
                $table->string('secret', 60)->default('testing123-radius-netpro');
                $table->string('server', 64)->nullable();
                $table->string('community', 50)->nullable();
                $table->string('description', 200)->nullable();
                $table->string('ip_address', 50)->nullable();
                $table->integer('api_port')->default(8728);
                $table->string('status', 20)->default('ONLINE');
                $table->timestamps();
            });
        }

        if (!Schema::connection('radius')->hasTable('radius_users')) {
            Schema::connection('radius')->create('radius_users', function (Blueprint $table) {
                $table->id();
                $table->string('username', 64)->unique();
                $table->string('password', 64);
                $table->string('customer_name', 150)->nullable();
                $table->string('profile_name', 64)->default('PROFILE_HOME_20M');
                $table->string('ip_address', 50)->nullable();
                $table->string('nas_name', 64)->default('CCR-CORE-HQ-01');
                $table->string('mac_address', 50)->nullable();
                $table->string('rate_limit', 50)->default('20M/20M');
                $table->string('status', 30)->default('DISCONNECTED'); // CONNECTED, DISCONNECTED, ISOLATED
                $table->timestamp('last_online_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::connection('radius')->hasTable('radius_accts')) {
            Schema::connection('radius')->create('radius_accts', function (Blueprint $table) {
                $table->id();
                $table->string('radacctid', 64)->nullable();
                $table->string('username', 64);
                $table->string('nasipaddress', 50)->nullable();
                $table->string('framedipaddress', 50)->nullable();
                $table->timestamp('acctstarttime')->nullable();
                $table->timestamp('acctstoptime')->nullable();
                $table->integer('acctsessiontime')->default(0);
                $table->bigInteger('acctinputoctets')->default(0);
                $table->bigInteger('acctoutputoctets')->default(0);
                $table->string('acctterminatecause', 50)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::connection('radius')->dropIfExists('radius_accts');
        Schema::connection('radius')->dropIfExists('radius_users');
        Schema::connection('radius')->dropIfExists('radius_nas');
    }
};
