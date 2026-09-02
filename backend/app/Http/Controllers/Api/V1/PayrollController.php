<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BonusClaim;
use App\Models\Employee;
use App\Models\PayrollRecord;
use App\Services\PayrollService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function records(Request $request): JsonResponse
    {
        $query = PayrollRecord::with('employee');
        if ($request->filled('period')) {
            $query->where('period', $request->period);
        }

        $records = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $records,
        ]);
    }

    public function generatePayroll(Request $request, PayrollService $payrollService): JsonResponse
    {
        $period = $request->get('period', Carbon::now()->translatedFormat('F Y'));
        $employees = Employee::where('status', 'active')->get();

        $generated = [];
        foreach ($employees as $employee) {
            $record = $payrollService->calculateThp($employee, $period);
            $generated[] = $record;
        }

        return response()->json([
            'status' => 'success',
            'message' => "Proses payroll periode {$period} berhasil dibuat untuk " . count($generated) . " karyawan.",
            'data' => $generated,
        ]);
    }

    public function bonusClaims(): JsonResponse
    {
        $claims = BonusClaim::orderBy('id', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $claims,
        ]);
    }

    public function approveBonusClaim(int $id): JsonResponse
    {
        $claim = BonusClaim::findOrFail($id);
        $claim->update(['status' => 'TERVERIFIKASI']);

        return response()->json([
            'status' => 'success',
            'message' => "Klaim poin insentif BAST #{$claim->bast_no} berhasil diverifikasi.",
            'data' => $claim,
        ]);
    }
}
