<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BonusClaim;
use App\Models\Employee;
use App\Models\Survey;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkOrderController extends Controller
{
    public function surveys(Request $request): JsonResponse
    {
        $surveys = Survey::orderBy('id', 'desc')->paginate($request->get('per_page', 20));
        return response()->json([
            'status' => 'success',
            'data' => $surveys,
        ]);
    }

    public function storeSurvey(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:150',
            'phone' => 'required|string|max:50',
            'address' => 'required|string',
            'gps_lat' => 'nullable|numeric',
            'gps_lng' => 'nullable|numeric',
            'nearest_odp' => 'nullable|string|max:100',
            'distance_m' => 'nullable|integer',
            'tech_name' => 'nullable|string|max:100',
            'attenuation' => 'nullable|string|max:50',
        ]);

        $surveyNo = 'SRV-' . Carbon::now()->format('Ymd') . '-' . rand(100, 999);
        $survey = Survey::create(array_merge($validated, [
            'survey_no' => $surveyNo,
            'status' => 'APPROVED',
        ]));

        return response()->json([
            'status' => 'success',
            'message' => 'Hasil survey berhasil disimpan.',
            'data' => $survey,
        ], 201);
    }

    public function workOrders(Request $request): JsonResponse
    {
        $wos = WorkOrder::orderBy('id', 'desc')->paginate($request->get('per_page', 20));
        return response()->json([
            'status' => 'success',
            'data' => $wos,
        ]);
    }

    public function storeWorkOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:150',
            'package_name' => 'nullable|string|max:100',
            'ont_type' => 'nullable|string|max:100',
            'ont_sn' => 'nullable|string|max:100',
            'tech_name' => 'nullable|string|max:100',
            'odp_port' => 'nullable|string|max:100',
            'attenuation' => 'nullable|string|max:50',
        ]);

        $woNo = 'WO-' . Carbon::now()->format('Ymd') . '-' . rand(100, 999);
        $bastNo = 'BAST-' . Carbon::now()->format('Ym') . '-' . rand(1000, 9999);

        $wo = WorkOrder::create(array_merge($validated, [
            'wo_no' => $woNo,
            'bast_no' => $bastNo,
            'status' => 'AKTIF & ONLINE',
            'completed_at' => Carbon::now(),
        ]));

        // Automatically assign BAST incentive point to assigned technician if found
        if (!empty($validated['tech_name'])) {
            $employee = Employee::where('name', 'like', "%{$validated['tech_name']}%")->first();
            if ($employee) {
                BonusClaim::create([
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->name,
                    'role' => 'Teknisi Instalasi FTTH',
                    'bast_no' => $bastNo,
                    'points' => 10,
                    'rate' => 50000,
                    'total_amount' => 500000,
                    'status' => 'TERVERIFIKASI',
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => "Surat Perintah Kerja (SPK) {$wo->wo_no} dan BAST {$wo->bast_no} berhasil diterbitkan.",
            'data' => $wo,
        ], 201);
    }
}
