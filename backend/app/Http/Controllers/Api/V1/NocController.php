<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\NocOutage;
use App\Models\RadiusNas;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NocController extends Controller
{
    public function monitoring(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'pop_hq' => ['name' => 'POP Headquarter Cyber', 'status' => 'ONLINE', 'latency_ms' => 0.12, 'load_percent' => 38],
                'pop_bekasi' => ['name' => 'POP Barat Bekasi', 'status' => 'ONLINE', 'latency_ms' => 0.28, 'load_percent' => 45],
                'pop_depok' => ['name' => 'POP Selatan Depok', 'status' => 'ONLINE', 'latency_ms' => 0.31, 'load_percent' => 52],
                'core_bras' => ['name' => 'CCR2116-Core-01', 'status' => 'ONLINE', 'cpu_usage' => 14, 'temperature_c' => 42],
                'total_olt' => 4,
                'total_pon_ports' => 32,
                'total_onu_online' => 1240,
                'total_onu_los' => 3,
                'upstream_traffic_gbps' => 8.42,
            ],
        ]);
    }

    public function topology(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'nodes' => [
                    ['id' => 'IXP', 'label' => 'OpenIXP / IIX Jakarta', 'type' => 'UPSTREAM', 'status' => 'ONLINE'],
                    ['id' => 'CCR1', 'label' => 'Core Router CCR2116-HQ', 'type' => 'CORE_ROUTER', 'status' => 'ONLINE'],
                    ['id' => 'OLT1', 'label' => 'ZTE C320 OLT 8-Port', 'type' => 'OLT_GPON', 'status' => 'ONLINE'],
                    ['id' => 'ODC1', 'label' => 'ODC-01 Cyber (1:4)', 'type' => 'ODC_FDT', 'status' => 'ONLINE'],
                    ['id' => 'ODP1', 'label' => 'ODP-JTW-01 (1:8)', 'type' => 'ODP', 'status' => 'ONLINE'],
                ],
                'links' => [
                    ['from' => 'IXP', 'to' => 'CCR1', 'capacity' => '10 Gbps SFP+', 'status' => 'ACTIVE'],
                    ['from' => 'CCR1', 'to' => 'OLT1', 'capacity' => '10 Gbps DAC', 'status' => 'ACTIVE'],
                    ['from' => 'OLT1', 'to' => 'ODC1', 'capacity' => 'GPON Port 1/1/1', 'status' => 'ACTIVE'],
                    ['from' => 'ODC1', 'to' => 'ODP1', 'capacity' => 'Feeder Core #1', 'status' => 'ACTIVE'],
                ],
            ],
        ]);
    }

    public function olts(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                ['id' => 1, 'name' => 'OLT-ZTE-C320-HQ', 'ip' => '10.254.10.1', 'vendor' => 'ZTE', 'pon_ports' => 8, 'onus_active' => 380, 'status' => 'ONLINE', 'optical_tx_dbm' => '+4.5 dBm'],
                ['id' => 2, 'name' => 'OLT-HUAWEI-MA5608T-BKS', 'ip' => '10.254.10.2', 'vendor' => 'HUAWEI', 'pon_ports' => 8, 'onus_active' => 410, 'status' => 'ONLINE', 'optical_tx_dbm' => '+5.0 dBm'],
                ['id' => 3, 'name' => 'OLT-HSGQ-E08-DPK', 'ip' => '10.254.10.3', 'vendor' => 'HSGQ', 'pon_ports' => 8, 'onus_active' => 260, 'status' => 'ONLINE', 'optical_tx_dbm' => '+4.2 dBm'],
                ['id' => 4, 'name' => 'OLT-VSOL-V1600G-TNG', 'ip' => '10.254.10.4', 'vendor' => 'VSOL', 'pon_ports' => 8, 'onus_active' => 190, 'status' => 'ONLINE', 'optical_tx_dbm' => '+4.8 dBm'],
            ],
        ]);
    }

    public function otbs(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                ['id' => 1, 'name' => 'OTB-RACK-HQ-01', 'capacity' => '48 Core SC/UPC', 'used' => 36, 'available' => 12, 'location' => 'Server Room Cyber Lt.5'],
                ['id' => 2, 'name' => 'OTB-RACK-HQ-02', 'capacity' => '96 Core SC/APC', 'used' => 78, 'available' => 18, 'location' => 'Server Room Cyber Lt.5'],
                ['id' => 3, 'name' => 'ODF-DIST-BKS-01', 'capacity' => '144 Core SC/APC', 'used' => 112, 'available' => 32, 'location' => 'POP Bekasi Barat'],
            ],
        ]);
    }

    public function odcs(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                ['code' => 'ODC-JTW-01', 'location' => 'Jl. Jatiwaringin Raya No. 12', 'splitter_type' => '1:4 Cassette', 'capacity_core' => 48, 'used_core' => 38, 'status' => 'NORMAL'],
                ['code' => 'ODC-JTC-02', 'location' => 'Jl. Jaticempaka No. 88', 'splitter_type' => '1:8 Cassette', 'capacity_core' => 96, 'used_core' => 74, 'status' => 'NORMAL'],
                ['code' => 'ODC-PDG-03', 'location' => 'Jl. Pondok Gede Permai No. 45', 'splitter_type' => '1:4 Cassette', 'capacity_core' => 48, 'used_core' => 44, 'status' => 'FULL_CAPACITY'],
            ],
        ]);
    }

    public function odps(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                ['code' => 'ODP-JTW-01/01', 'parent_odc' => 'ODC-JTW-01', 'ports' => '8 Port (6 Terisi / 2 Kosong)', 'gps' => '-6.289123, 106.918456', 'rx_power' => '-19.4 dBm', 'status' => 'BAGUS'],
                ['code' => 'ODP-JTW-01/02', 'parent_odc' => 'ODC-JTW-01', 'ports' => '8 Port (8 Terisi / 0 Kosong)', 'gps' => '-6.289450, 106.918890', 'rx_power' => '-21.1 dBm', 'status' => 'PENUH'],
                ['code' => 'ODP-JTC-02/01', 'parent_odc' => 'ODC-JTC-02', 'ports' => '16 Port (12 Terisi / 4 Kosong)', 'gps' => '-6.291200, 106.920100', 'rx_power' => '-18.7 dBm', 'status' => 'BAGUS'],
                ['code' => 'ODP-PDG-03/01', 'parent_odc' => 'ODC-PDG-03', 'ports' => '8 Port (5 Terisi / 3 Kosong)', 'gps' => '-6.294500, 106.924200', 'rx_power' => '-24.8 dBm', 'status' => 'REDAMAN_TINGGI'],
            ],
        ]);
    }

    public function onus(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                ['sn' => 'ZTEGC9120938', 'model' => 'ZTE F670L Dual Band', 'customer' => 'Susi Susanti (CID-100882)', 'rx_dbm' => '-19.5 dBm', 'temp' => '42°C', 'status' => 'ONLINE'],
                ['sn' => 'HWTC88129038', 'model' => 'Huawei EG8145V5', 'customer' => 'Budi Santoso (CID-100883)', 'rx_dbm' => '-20.2 dBm', 'temp' => '40°C', 'status' => 'ONLINE'],
                ['sn' => 'VSOL77281920', 'model' => 'VSOL V2801SG GPON', 'customer' => 'PT Makmur Jaya (CID-100884)', 'rx_dbm' => '-17.8 dBm', 'temp' => '38°C', 'status' => 'ONLINE'],
                ['sn' => 'ZTEGC1129384', 'model' => 'ZTE F609 V3', 'customer' => 'Andi Wijaya (CID-100885)', 'rx_dbm' => '-28.5 dBm', 'temp' => '45°C', 'status' => 'LOS_OPTICAL'],
            ],
        ]);
    }

    public function mikrotik(): JsonResponse
    {
        $nas = RadiusNas::first();
        return response()->json([
            'status' => 'success',
            'data' => [
                'router_model' => 'MikroTik CCR2116-12G-4S+',
                'routeros_version' => 'RouterOS v7.14.2 (stable)',
                'ip_address' => $nas ? $nas->ip_address : '10.254.0.1',
                'api_port' => 8728,
                'cpu_load' => '14%',
                'free_memory' => '14.8 GB / 16.0 GB',
                'uptime' => '48 Hari, 14 Jam, 22 Menit',
                'active_pppoe' => 1240,
                'simple_queues' => 1240,
                'firewall_filter_rules' => 48,
                'status' => 'CONNECTED',
            ],
        ]);
    }

    public function outages(Request $request): JsonResponse
    {
        $query = NocOutage::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $outages = $query->orderBy('id', 'desc')->paginate($request->get('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $outages,
        ]);
    }

    public function storeOutage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'location' => 'required|string|max:200',
            'issue_type' => 'required|string|max:100',
            'affected_users' => 'nullable|integer',
            'tech_name' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
        ]);

        $outageNo = 'INC-' . Carbon::now()->format('Ymd') . '-' . rand(100, 999);

        $outage = NocOutage::create(array_merge($validated, ['outage_no' => $outageNo]));

        return response()->json([
            'status' => 'success',
            'message' => "Insiden NOC {$outage->outage_no} berhasil dilaporkan.",
            'data' => $outage,
        ], 201);
    }

    public function resolveOutage(int $id, Request $request): JsonResponse
    {
        $outage = NocOutage::findOrFail($id);
        $outage->update([
            'status' => 'RESOLVED',
            'resolved_at' => Carbon::now(),
            'resolution_notes' => $request->get('notes', 'Splicing FO dan perbaikan link selesai.'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Insiden {$outage->outage_no} telah ditandai terselesaikan.",
            'data' => $outage,
        ]);
    }
}
