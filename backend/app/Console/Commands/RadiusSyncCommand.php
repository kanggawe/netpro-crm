<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Services\RadiusCoaService;
use Illuminate\Console\Command;

class RadiusSyncCommand extends Command
{
    protected $signature = 'isp:radius-sync';
    protected $description = 'Synchronize all customer PPPoE credentials and profiles to FreeRADIUS database';

    public function handle(RadiusCoaService $radiusService): int
    {
        $this->info('🚀 Memulai sinkronisasi seluruh pelanggan ke FreeRADIUS Engine...');

        $customers = Customer::whereNotNull('pppoe_user')->get();
        $count = 0;

        foreach ($customers as $customer) {
            $radiusService->syncUser($customer);
            $count++;
        }

        $this->info("✅ Sukses menyinkronkan {$count} kredensial PPPoE ke tabel radius_users.");
        return self::SUCCESS;
    }
}
