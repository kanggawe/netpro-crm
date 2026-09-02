<?php

namespace App\Services;

use App\Models\BonusClaim;
use App\Models\Employee;
use App\Models\PayrollRecord;

class PayrollService
{
    /**
     * Calculate Distance between 2 GPS coordinates in meters using Haversine formula.
     */
    public function calculateHaversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // meters

        $latFrom = deg2rad($lat1);
        $lngFrom = deg2rad($lng1);
        $latTo = deg2rad($lat2);
        $lngTo = deg2rad($lng2);

        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lngDelta / 2), 2)));

        return round($angle * $earthRadius, 2);
    }

    /**
     * Validate GPS clock-in within allowable office/POP radius (default 200m).
     */
    public function validateClockInGps(
        float $lat,
        float $lng,
        float $officeLat = -6.2891,
        float $officeLng = 106.9182,
        float $maxRadiusMeters = 200.0
    ): array {
        $distance = $this->calculateHaversineDistance($lat, $lng, $officeLat, $officeLng);
        $isValid = $distance <= $maxRadiusMeters;

        return [
            'is_valid' => $isValid,
            'distance_m' => $distance,
            'max_allowed_m' => $maxRadiusMeters,
            'message' => $isValid ? "Lokasi presensi valid ({$distance}m dari titik pusat)" : "Di luar radius presensi ({$distance}m, maks {$maxRadiusMeters}m)",
        ];
    }

    /**
     * Calculate and generate Take Home Pay (THP) payroll record for an employee.
     */
    public function calculateThp(
        Employee $employee,
        string $period,
        float $extraBonus = 0,
        float $extraDeductions = 0
    ): PayrollRecord {
        $basicSalary = (float) $employee->basic_salary;
        $allowance = (float) $employee->allowance;

        // Calculate verified BAST Bonus Claims
        $bastBonus = (float) BonusClaim::where('employee_id', $employee->id)
            ->where('status', 'TERVERIFIKASI')
            ->sum('total_amount');

        $totalBonus = $bastBonus + $extraBonus;

        // BPJS Ketenagakerjaan & Kesehatan estimate (~3% employee portion)
        $bpjsDeduction = round(($basicSalary + $allowance) * 0.03, 2);
        $totalDeductions = $bpjsDeduction + $extraDeductions;

        $thp = round($basicSalary + $allowance + $totalBonus - $totalDeductions, 2);

        return PayrollRecord::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'period' => $period,
            ],
            [
                'employee_name' => $employee->name,
                'basic_salary' => $basicSalary,
                'allowance' => $allowance,
                'bonus' => $totalBonus,
                'deductions' => $totalDeductions,
                'thp' => $thp,
                'status' => 'APPROVED',
                'bank_name' => $employee->bank_name,
                'account_no' => $employee->bank_account,
            ]
        );
    }
}
