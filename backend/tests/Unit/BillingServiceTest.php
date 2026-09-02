<?php

namespace Tests\Unit;

use App\Services\BillingService;
use Tests\TestCase;

class BillingServiceTest extends TestCase
{
    protected BillingService $billingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->billingService = new BillingService();
    }

    public function test_calculate_ppn_include(): void
    {
        // Rp 150.000 Include PPN 11% => DPP = 150000 / 1.11 = 135135.14, PPN = 14864.86, Total = 150000
        $result = $this->billingService->calculatePpn(150000, 'include');

        $this->assertEquals(135135.14, $result['dpp']);
        $this->assertEquals(14864.86, $result['ppn']);
        $this->assertEquals(150000.0, $result['total']);
        $this->assertEquals('include', $result['mode']);
    }

    public function test_calculate_ppn_exclude(): void
    {
        // Rp 200.000 Exclude PPN 11% => DPP = 200000, PPN = 22000, Total = 222000
        $result = $this->billingService->calculatePpn(200000, 'exclude');

        $this->assertEquals(200000.0, $result['dpp']);
        $this->assertEquals(22000.0, $result['ppn']);
        $this->assertEquals(222000.0, $result['total']);
        $this->assertEquals('exclude', $result['mode']);
    }

    public function test_calculate_prorata_fixed_date(): void
    {
        $startDate = \Carbon\Carbon::create(2026, 8, 16); // Day 16 of 31 days in Aug
        $result = $this->billingService->calculateProrata(310000, $startDate, 'fixed_date');

        $this->assertTrue($result['is_prorata']);
        $this->assertEquals(16, $result['days_remaining']);
        $this->assertEquals(31, $result['total_days']);
        // 310000 * (16/31) = 160000
        $this->assertEquals(160000.0, $result['final_price']);
    }
}
