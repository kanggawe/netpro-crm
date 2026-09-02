<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Package;
use App\Models\RadiusAcct;
use App\Models\RadiusNas;
use App\Models\RadiusUser;
use App\Services\RadiusCoaService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RadiusController extends Controller
{
    public function telemetry(RadiusCoaService $radiusService): JsonResponse
    {
        $totalUsers = RadiusUser::count();
        $connectedUsers = RadiusUser::where('status', 'CONNECTED')->count();
        $isolatedUsers = RadiusUser::where('status', 'ISOLATED')->count();
        $nasCount = RadiusNas::count();

        // Hardware Probes
        $coreNasProbe = $radiusService->probeHardware(config('radius.server_host', '127.0.0.1'), config('radius.auth_port', 1812));
        $mikrotikProbe = $radiusService->probeHardware(config('radius.server_host', '127.0.0.1'), 8728);

        return response()->json([
            'status' => 'success',
            'data' => [
                'radius_server' => [
                    'status' => $coreNasProbe['status'],
                    'latency_ms' => $coreNasProbe['latency_ms'],
                    'port_auth' => config('radius.auth_port', 1812),
                    'port_coa' => config('radius.coa_port', 3799),
                ],
                'mikrotik_core' => [
                    'status' => $mikrotikProbe['status'],
                    'latency_ms' => $mikrotikProbe['latency_ms'],
                ],
                'subscribers' => [
                    'total' => $totalUsers,
                    'connected' => $connectedUsers,
                    'isolated' => $isolatedUsers,
                    'offline' => max(0, $totalUsers - $connectedUsers - $isolatedUsers),
                ],
                'traffic_stats' => [
                    'total_sessions' => RadiusAcct::count(),
                    'total_upload_gb' => round(RadiusAcct::sum('acctinputoctets') / (1024 * 1024 * 1024), 2),
                    'total_download_gb' => round(RadiusAcct::sum('acctoutputoctets') / (1024 * 1024 * 1024), 2),
                ],
            ],
        ]);
    }

    public function users(Request $request): JsonResponse
    {
        $query = RadiusUser::with('customer.package');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('username', 'like', "%{$s}%")
                    ->orWhere('customer_name', 'like', "%{$s}%")
                    ->orWhere('ip_address', 'like', "%{$s}%");
            });
        }

        $users = $query->orderBy('id', 'desc')->paginate($request->get('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $users,
        ]);
    }

    public function storeUser(Request $request, RadiusCoaService $radiusService): JsonResponse
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:radius.radius_users,username',
            'password' => 'required|string',
            'customer_name' => 'nullable|string',
            'profile_name' => 'nullable|string',
            'ip_address' => 'nullable|string',
            'rate_limit' => 'nullable|string',
        ]);

        $user = RadiusUser::create(array_merge($validated, ['status' => 'DISCONNECTED']));

        // Sync FreeRADIUS core tables
        \Illuminate\Support\Facades\DB::connection('radius')->table('radcheck')->updateOrInsert(
            ['username' => $user->username, 'attribute' => 'Cleartext-Password'],
            ['op' => ':=', 'value' => $user->password]
        );

        $group = $user->profile_name ?? 'PROFILE_HOME_20M';
        \Illuminate\Support\Facades\DB::connection('radius')->table('radusergroup')->updateOrInsert(
            ['username' => $user->username],
            ['groupname' => $group, 'priority' => 1]
        );

        AuditLog::log(auth()->user()->username ?? 'admin', 'CREATE_RADIUS_USER', "Tambah user PPPoE: {$user->username}");

        return response()->json([
            'status' => 'success',
            'message' => "User PPPoE {$user->username} berhasil ditambahkan ke RADIUS.",
            'data' => $user,
        ], 201);
    }

    public function destroyUser(int $id, RadiusCoaService $radiusService): JsonResponse
    {
        $user = RadiusUser::findOrFail($id);
        $username = $user->username;
        $radiusService->deleteUser($username);
        AuditLog::log(auth()->user()->username ?? 'admin', 'DELETE_RADIUS_USER', "Hapus user PPPoE: $username");

        return response()->json([
            'status' => 'success',
            'message' => "User PPPoE {$username} berhasil dihapus.",
        ]);
    }

    public function nasList(): JsonResponse
    {
        $nas = RadiusNas::all();
        return response()->json([
            'status' => 'success',
            'data' => $nas,
        ]);
    }

    public function storeNas(Request $request, RadiusCoaService $radiusService): JsonResponse
    {
        $validated = $request->validate([
            'nasname' => 'required|string|max:128',
            'shortname' => 'required|string|max:32',
            'type' => 'nullable|string|max:30',
            'ports' => 'nullable|integer',
            'secret' => 'required|string|max:60',
            'description' => 'nullable|string',
            'ip_address' => 'nullable|string',
            'api_port' => 'nullable|integer',
        ]);

        $nas = RadiusNas::create(array_merge($validated, ['status' => 'ONLINE']));
        $radiusService->syncNas($nas);

        AuditLog::log(auth()->user()->username ?? 'admin', 'CREATE_NAS', "Tambah Router NAS: {$nas->shortname}");

        return response()->json([
            'status' => 'success',
            'message' => "Router NAS {$nas->shortname} berhasil ditambahkan.",
            'data' => $nas,
        ], 201);
    }

    public function destroyNas(int $id): JsonResponse
    {
        $nas = RadiusNas::findOrFail($id);
        $name = $nas->shortname;
        $nas->delete();
        AuditLog::log(auth()->user()->username ?? 'admin', 'DELETE_NAS', "Hapus Router NAS: $name");

        return response()->json([
            'status' => 'success',
            'message' => "Router NAS {$name} berhasil dihapus.",
        ]);
    }

    public function profiles(): JsonResponse
    {
        $packages = Package::all();
        $profiles = $packages->map(function ($pkg) {
            return [
                'id' => $pkg->id,
                'name' => 'PROFILE_' . strtoupper(str_replace(' ', '_', $pkg->name)),
                'rate_limit' => "{$pkg->speed_mbps}M/{$pkg->speed_mbps}M",
                'pool_name' => 'POOL_PPPOE_RESIDENTIAL',
                'session_timeout' => '86400',
                'idle_timeout' => '1800',
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $profiles,
        ]);
    }

    public function vouchers(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                ['id' => 1, 'code' => 'NETPRO-1D-8821', 'profile' => 'HOTSPOT_1_DAY', 'price' => 5000, 'duration' => '24 Jam', 'status' => 'AVAILABLE'],
                ['id' => 2, 'code' => 'NETPRO-7D-9912', 'profile' => 'HOTSPOT_7_DAYS', 'price' => 25000, 'duration' => '7 Hari', 'status' => 'USED'],
                ['id' => 3, 'code' => 'NETPRO-30D-4412', 'profile' => 'HOTSPOT_30_DAYS', 'price' => 75000, 'duration' => '30 Hari', 'status' => 'AVAILABLE'],
            ],
        ]);
    }

    public function reports(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'monthly_total_gb' => 48200.5,
                'peak_traffic_mbps' => 8420.0,
                'avg_session_hours' => 18.5,
                'total_disconnects_coa' => 42,
            ],
        ]);
    }

    public function disconnect(Request $request, RadiusCoaService $radiusService): JsonResponse
    {
        $request->validate([
            'username' => 'required|string',
        ]);

        $success = $radiusService->disconnectUser($request->username);

        return response()->json([
            'status' => $success ? 'success' : 'error',
            'message' => $success ? "Paket Disconnect-Request (CoA) berhasil dikirim untuk akun [{$request->username}]." : "Gagal mengirim paket CoA.",
        ]);
    }

    public function probe(Request $request, RadiusCoaService $radiusService): JsonResponse
    {
        $request->validate([
            'host' => 'required|string',
            'port' => 'required|integer',
        ]);

        $result = $radiusService->probeHardware($request->host, (int) $request->port);

        return response()->json([
            'status' => 'success',
            'data' => $result,
        ]);
    }
}
