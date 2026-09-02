<?php

namespace Tests\Feature;

use App\Models\BonusClaim;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\InventoryItem;
use App\Models\Invoice;
use App\Models\NocOutage;
use App\Models\Package;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompleteApiEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->user = User::where('username', 'superadmin')->first();
        $loginRes = $this->postJson('/api/v1/auth/login', [
            'username' => 'superadmin',
            'password' => 'admin123',
        ]);
        $this->token = $loginRes->json('data.token');
    }

    protected function authGet(string $url)
    {
        return $this->withHeader('Authorization', "Bearer {$this->token}")->getJson($url);
    }

    protected function authPost(string $url, array $data = [])
    {
        return $this->withHeader('Authorization', "Bearer {$this->token}")->postJson($url, $data);
    }

    protected function authPut(string $url, array $data = [])
    {
        return $this->withHeader('Authorization', "Bearer {$this->token}")->putJson($url, $data);
    }

    public function test_all_modules_endpoints_succeed(): void
    {
        // 1. Auth & Tax simulation
        $this->authGet('/api/v1/auth/me')->assertStatus(200);
        $this->getJson('/api/v1/tax/simulation?amount=150000&mode=include')->assertStatus(200);

        // 2. Packages, Addons, Promos
        $this->authGet('/api/v1/packages')->assertStatus(200);
        $pkgRes = $this->authPost('/api/v1/packages', [
            'name' => 'Corporate Enterprise 200M',
            'speed_mbps' => 200,
            'price' => 1200000,
            'default_ppn_mode' => 'include',
            'category' => 'business',
        ]);
        $pkgRes->assertStatus(201);
        $pkgId = $pkgRes->json('data.id');
        $this->authPut("/api/v1/packages/{$pkgId}", ['price' => 1250000])->assertStatus(200);
        $this->authGet('/api/v1/addons')->assertStatus(200);
        $this->authGet('/api/v1/promos')->assertStatus(200);

        // 3. Customers
        $this->authGet('/api/v1/customers')->assertStatus(200);
        $custRes = $this->authPost('/api/v1/customers', [
            'name' => 'Budi Pratama',
            'nik' => '3275019800010005',
            'phone' => '081234567890',
            'email' => 'budi@example.com',
            'address' => 'Jl. Boulevard No. 45',
            'package_id' => 1,
            'billing_type' => 'postpaid',
        ]);
        $custRes->assertStatus(201);
        $custId = $custRes->json('data.id');

        $this->authGet("/api/v1/customers/{$custId}")->assertStatus(200);
        $this->authPut("/api/v1/customers/{$custId}", ['phone' => '081234567899'])->assertStatus(200);
        $this->authPost("/api/v1/customers/{$custId}/set-online")->assertStatus(200);
        $this->authPost("/api/v1/customers/{$custId}/quick-topup", ['payment_method' => 'QRIS'])->assertStatus(200);
        $this->authPost("/api/v1/customers/{$custId}/isolate")->assertStatus(200);

        // 4. Invoices & Billing
        $this->authGet('/api/v1/invoices')->assertStatus(200);
        $invRes = $this->authPost('/api/v1/invoices/generate-customer', [
            'customer_id' => $custId,
            'period' => 'September 2026',
        ]);
        $invRes->assertStatus(201);
        $invId = $invRes->json('data.id');

        $this->authGet("/api/v1/invoices/{$invId}")->assertStatus(200);
        $this->authPost("/api/v1/invoices/{$invId}/pay", [
            'payment_method' => 'BCA Virtual Account',
        ])->assertStatus(200);
        $this->authPost("/api/v1/invoices/{$invId}/send-reminder")->assertStatus(200);
        $this->authPost('/api/v1/invoices/generate-monthly')->assertStatus(200);

        // 5. RADIUS & CoA
        $this->authGet('/api/v1/radius/telemetry')->assertStatus(200);
        $this->authGet('/api/v1/radius/users')->assertStatus(200);
        $this->authGet('/api/v1/radius/nas')->assertStatus(200);
        $this->authPost('/api/v1/radius/disconnect', ['username' => '32750123-SUSI'])->assertStatus(200);
        $this->authPost('/api/v1/radius/probe', ['host' => '127.0.0.1', 'port' => 1812])->assertStatus(200);

        // 6. Surveys & Work Orders
        $this->authGet('/api/v1/surveys')->assertStatus(200);
        $this->authPost('/api/v1/surveys', [
            'customer_name' => 'Dewi Lestari',
            'phone' => '081299998888',
            'address' => 'Jl. Palm Hills A2',
            'nearest_odp' => 'ODP-JKT-04',
            'distance_m' => 45,
        ])->assertStatus(201);

        $this->authGet('/api/v1/work-orders')->assertStatus(200);
        $this->authPost('/api/v1/work-orders', [
            'customer_name' => 'Dewi Lestari',
            'package_name' => 'Home Basic 20M',
            'ont_type' => 'ZTE F670L',
            'ont_sn' => 'ZTEGC1234567',
            'tech_name' => 'Ahmad Rian Maulana',
            'odp_port' => 'ODP-JKT-04/Port-02',
            'attenuation' => '-18.5 dBm',
        ])->assertStatus(201);

        // 7. NOC & Tickets
        $this->authGet('/api/v1/noc/outages')->assertStatus(200);
        $outageRes = $this->authPost('/api/v1/noc/outages', [
            'location' => 'Kuningan Barat Segmen 3',
            'issue_type' => 'CABLE CUT FO',
            'affected_users' => 45,
            'tech_name' => 'Tim Fiber Restore',
        ]);
        $outageRes->assertStatus(201);
        $outageId = $outageRes->json('data.id');
        $this->authPost("/api/v1/noc/outages/{$outageId}/resolve", ['notes' => 'FO Splicing selesai'])->assertStatus(200);

        $this->authGet('/api/v1/tickets')->assertStatus(200);
        $ticketRes = $this->authPost('/api/v1/tickets', [
            'customer_id' => $custId,
            'category' => 'LOS_RED_LIGHT',
            'priority' => 'HIGH',
            'description' => 'Lampu LOS merah berkedip',
        ]);
        $ticketRes->assertStatus(201);
        $ticketId = $ticketRes->json('data.id');
        $this->authPost("/api/v1/tickets/{$ticketId}/resolve", ['solution' => 'Redaman diperbaiki'])->assertStatus(200);

        // 8. Finance PSAK
        $this->authGet('/api/v1/finance/coa')->assertStatus(200);
        $this->authGet('/api/v1/finance/journals')->assertStatus(200);
        $this->authGet('/api/v1/finance/taxes')->assertStatus(200);
        $this->authPost('/api/v1/finance/taxes', [
            'vendor_name' => 'PT Moratelindo TBK',
            'npwp' => '01.345.678.9-012.000',
            'obj_income' => 'Sewa Core Fiber Optik',
            'dpp_amount' => 15000000,
        ])->assertStatus(201);

        $this->authGet('/api/v1/finance/opex')->assertStatus(200);
        $this->authPost('/api/v1/finance/opex', [
            'category' => 'SEWA TIANG & FO',
            'vendor_name' => 'PT PLN Icon Plus',
            'description' => 'Sewa Tiang Distribusi Q3 2026',
            'amount' => 7500000,
        ])->assertStatus(201);

        $this->authGet('/api/v1/finance/cashflow')->assertStatus(200);
        $this->authGet('/api/v1/finance/regulatory-summary')->assertStatus(200);

        // 9. HR & Payroll
        $this->authGet('/api/v1/hr/employees')->assertStatus(200);
        $empRes = $this->authPost('/api/v1/hr/employees', [
            'nik' => 'EMP-2026-099',
            'name' => 'Fajar Nugraha',
            'division' => 'NOC',
            'position' => 'NOC Engineer',
            'basic_salary' => 6000000,
            'allowance' => 1200000,
        ]);
        $empRes->assertStatus(201);
        $empId = $empRes->json('data.id');

        $this->authGet('/api/v1/hr/attendances')->assertStatus(200);
        $this->authPost('/api/v1/hr/clock-in', [
            'employee_id' => $empId,
            'gps_lat' => -6.289110,
            'gps_lng' => 106.918210,
        ])->assertStatus(200);

        $this->authGet('/api/v1/hr/leaves')->assertStatus(200);
        $this->authPost('/api/v1/hr/leaves', [
            'employee_id' => $empId,
            'leave_type' => 'TAHUNAN',
            'start_date' => '2026-09-10',
            'end_date' => '2026-09-12',
            'reason' => 'Keperluan keluarga',
        ])->assertStatus(201);

        $this->authGet('/api/v1/hr/kpi')->assertStatus(200);
        $this->authGet('/api/v1/payroll/records')->assertStatus(200);
        $this->authPost('/api/v1/payroll/generate', ['period' => 'Agustus 2026'])->assertStatus(200);
        $this->authGet('/api/v1/payroll/bonus-claims')->assertStatus(200);
        $claim = BonusClaim::first();
        if ($claim) {
            $this->authPost("/api/v1/payroll/bonus-claims/{$claim->id}/approve")->assertStatus(200);
        }

        // 10. Inventory, Leads, Branches
        $this->authGet('/api/v1/inventory/items')->assertStatus(200);
        $itemRes = $this->authPost('/api/v1/inventory/items', [
            'sku' => 'SFP-1G-BIDI-20KM',
            'name' => 'SFP BiDi 1.25G 20KM TX1310',
            'category' => 'SFP TRANSCEIVER',
            'stock' => 50,
            'unit_cost' => 110000,
        ]);
        $itemRes->assertStatus(201);
        $itemId = $itemRes->json('data.id');
        $this->authPost("/api/v1/inventory/items/{$itemId}/adjust-stock", ['adjustment' => 10])->assertStatus(200);

        $this->authGet('/api/v1/leads')->assertStatus(200);
        $this->authGet('/api/v1/branches')->assertStatus(200);

        // 11. Settings & Logs
        $this->authGet('/api/v1/settings')->assertStatus(200);
        $this->authPost('/api/v1/settings', ['company_phone' => '021-5550999'])->assertStatus(200);
        $this->authGet('/api/v1/audit-logs')->assertStatus(200);
        $this->authGet('/api/v1/backups')->assertStatus(200);

        // 12. Logout
        $this->authPost('/api/v1/auth/logout')->assertStatus(200);
    }
}
