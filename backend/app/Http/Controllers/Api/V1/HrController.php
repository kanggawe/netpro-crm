<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\KpiIndicator;
use App\Models\Leave;
use App\Models\PerformanceReview;
use App\Services\PayrollService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HrController extends Controller
{
    public function employees(Request $request): JsonResponse
    {
        $query = Employee::query();
        if ($request->filled('division')) {
            $query->where('division', $request->division);
        }
        $employees = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $employees,
        ]);
    }

    public function storeEmployee(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nik' => 'required|string|max:50|unique:employees,nik',
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:50',
            'division' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'contract_status' => 'nullable|string',
            'basic_salary' => 'required|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'bank_name' => 'nullable|string',
            'bank_account' => 'nullable|string',
        ]);

        $employee = Employee::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data karyawan baru berhasil ditambahkan.',
            'data' => $employee,
        ], 201);
    }

    public function attendances(Request $request): JsonResponse
    {
        $attendances = Attendance::orderBy('id', 'desc')->paginate($request->get('per_page', 30));
        return response()->json([
            'status' => 'success',
            'data' => $attendances,
        ]);
    }

    public function clockIn(Request $request, PayrollService $payrollService): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'gps_lat' => 'required|numeric',
            'gps_lng' => 'required|numeric',
            'shift_type' => 'nullable|string',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        $gpsCheck = $payrollService->validateClockInGps(
            (float) $validated['gps_lat'],
            (float) $validated['gps_lng']
        );

        $now = Carbon::now();
        $isLate = $now->format('H:i:s') > '08:30:00';
        $status = !$gpsCheck['is_valid'] ? 'DILUAR RADIUS' : ($isLate ? 'TERLAMBAT' : 'TEPAT WAKTU');

        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'division' => $employee->division,
            'att_date' => $now->toDateString(),
            'shift_type' => $validated['shift_type'] ?? 'NORMAL',
            'clock_in' => $now->format('H:i:s'),
            'gps_lat' => $validated['gps_lat'],
            'gps_lng' => $validated['gps_lng'],
            'gps_location' => "Lat: {$validated['gps_lat']}, Lng: {$validated['gps_lng']} ({$gpsCheck['distance_m']}m)",
            'status' => $status,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Clock-in berhasil dicatat untuk {$employee->name}. Status: {$status}.",
            'data' => [
                'attendance' => $attendance,
                'gps_validation' => $gpsCheck,
            ],
        ]);
    }

    public function leaves(Request $request): JsonResponse
    {
        $leaves = Leave::orderBy('id', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $leaves,
        ]);
    }

    public function storeLeave(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $days = $start->diffInDays($end) + 1;

        $leave = Leave::create([
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'division' => $employee->division,
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'duration_days' => $days,
            'reason' => $validated['reason'] ?? null,
            'status' => 'APPROVED',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Pengajuan cuti ({$days} hari) berhasil disimpan.",
            'data' => $leave,
        ], 201);
    }

    public function kpiList(): JsonResponse
    {
        $kpis = KpiIndicator::all();
        $reviews = PerformanceReview::orderBy('id', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'indicators' => $kpis,
                'reviews' => $reviews,
            ],
        ]);
    }
}
