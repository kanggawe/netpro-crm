<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\RadiusNas;
use App\Models\RadiusUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RadiusCoaService
{
    /**
     * Sync customer credentials and package profile to FreeRADIUS 3.0 schema and management tables.
     */
    public function syncUser(Customer $customer): RadiusUser
    {
        $package = $customer->package;
        $speed = $package ? $package->speed_mbps : 20;
        $pkgName = $package ? strtoupper(preg_replace('/[^a-zA-Z0-9]/', '_', $package->name)) : 'HOME_20M';
        $profileName = "PROFILE_{$pkgName}";
        $rateLimit = "{$speed}M/{$speed}M";
        $password = $customer->pppoe_password ?? '123456';
        $username = $customer->pppoe_user;

        $ipAlloc = '10.100.10.' . (10 + ($customer->id % 240));

        // 1. Sync FreeRADIUS Core: radcheck (Authentication Cleartext-Password)
        DB::connection('radius')->table('radcheck')->updateOrInsert(
            ['username' => $username, 'attribute' => 'Cleartext-Password'],
            ['op' => ':=', 'value' => $password]
        );

        // 2. Sync FreeRADIUS Core: radgroupreply (Profile Rate Limit & Pool)
        DB::connection('radius')->table('radgroupreply')->updateOrInsert(
            ['groupname' => $profileName, 'attribute' => 'Mikrotik-Rate-Limit'],
            ['op' => '=', 'value' => $rateLimit]
        );
        DB::connection('radius')->table('radgroupreply')->updateOrInsert(
            ['groupname' => $profileName, 'attribute' => 'Framed-Pool'],
            ['op' => '=', 'value' => config('radius.default_framed_pool', 'pool-pppoe-home')]
        );

        // Ensure PROFILE_ISOLIR group exists in radgroupreply
        DB::connection('radius')->table('radgroupreply')->updateOrInsert(
            ['groupname' => 'PROFILE_ISOLIR', 'attribute' => 'Mikrotik-Rate-Limit'],
            ['op' => '=', 'value' => '128k/128k']
        );
        DB::connection('radius')->table('radgroupreply')->updateOrInsert(
            ['groupname' => 'PROFILE_ISOLIR', 'attribute' => 'Framed-Pool'],
            ['op' => '=', 'value' => config('radius.isolated_framed_pool', 'pool-isolated-suspend')]
        );

        // 3. Sync FreeRADIUS Core: radusergroup (Assign user to Group Profile)
        $targetGroup = $customer->status === 'isolated' ? 'PROFILE_ISOLIR' : $profileName;
        DB::connection('radius')->table('radusergroup')->updateOrInsert(
            ['username' => $username],
            ['groupname' => $targetGroup, 'priority' => 1]
        );

        // 4. Sync FreeRADIUS Core: radreply (Framed-IP-Address)
        DB::connection('radius')->table('radreply')->updateOrInsert(
            ['username' => $username, 'attribute' => 'Framed-IP-Address'],
            ['op' => '=', 'value' => $ipAlloc]
        );

        // 5. Sync CRM Bridge Table: radius_users
        return RadiusUser::updateOrCreate(
            ['username' => $username],
            [
                'password' => $password,
                'customer_name' => $customer->name,
                'profile_name' => $targetGroup,
                'ip_address' => $ipAlloc,
                'nas_name' => config('radius.nas_identifier', 'CCR-CORE-HQ-01'),
                'rate_limit' => $rateLimit,
                'status' => $customer->status === 'active' ? 'CONNECTED' : ($customer->status === 'isolated' ? 'ISOLATED' : 'DISCONNECTED'),
            ]
        );
    }

    /**
     * Disconnect/Kick active PPPoE session via RFC 3576 CoA / PoD on UDP 3799.
     */
    public function disconnectUser(string $username, ?string $nasHost = null, ?string $secret = null): bool
    {
        $nasHost = $nasHost ?? config('radius.server_host', '127.0.0.1');
        $coaPort = (int) config('radius.coa_port', 3799);
        $secret = $secret ?? config('radius.secret', 'testing123-radius-netpro');

        try {
            // Build RFC 3576 Disconnect-Request Packet
            $identifier = random_int(1, 255);
            $authenticator = random_bytes(16);

            $attrUsername = chr(1) . chr(2 + strlen($username)) . $username;
            $length = 20 + strlen($attrUsername);

            $packetHeader = chr(40) . chr($identifier) . pack('n', $length) . $authenticator;
            $rawPacket = $packetHeader . $attrUsername;

            $md5Hash = md5(chr(40) . chr($identifier) . pack('n', $length) . str_repeat("\x00", 16) . $attrUsername . $secret, true);
            $finalPacket = chr(40) . chr($identifier) . pack('n', $length) . $md5Hash . $attrUsername;

            $socket = @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            if ($socket) {
                socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, ['sec' => 1, 'usec' => 0]);
                socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, ['sec' => 1, 'usec' => 0]);
                @socket_sendto($socket, $finalPacket, strlen($finalPacket), 0, $nasHost, $coaPort);
                @socket_close($socket);
            }

            AuditLog::log(auth()->user()->username ?? 'noc_lead', 'RADIUS_COA_DISCONNECT', "CoA kick sesi PPPoE {$username}");
            Log::info("Sent RADIUS CoA Disconnect-Request for user [{$username}] to {$nasHost}:{$coaPort}");
            return true;
        } catch (\Throwable $t) {
            Log::warning("CoA Disconnect error for user [{$username}]: " . $t->getMessage());
            return false;
        }
    }

    /**
     * Isolate a PPPoE user: change FreeRADIUS group to PROFILE_ISOLIR and send CoA disconnect packet.
     */
    public function isolateUser(string $username): bool
    {
        // 1. Update FreeRADIUS radusergroup
        DB::connection('radius')->table('radusergroup')->updateOrInsert(
            ['username' => $username],
            ['groupname' => 'PROFILE_ISOLIR', 'priority' => 1]
        );

        // 2. Update CRM radius_users
        RadiusUser::where('username', $username)->update([
            'status' => 'ISOLATED',
            'profile_name' => 'PROFILE_ISOLIR',
        ]);

        return $this->disconnectUser($username);
    }

    /**
     * Restore a PPPoE user back to normal package profile and send CoA disconnect packet.
     */
    public function restoreUser(string $username): bool
    {
        $user = RadiusUser::where('username', $username)->first();
        if ($user) {
            $customer = $user->customer;
            $package = $customer ? $customer->package : null;
            $pkgName = $package ? strtoupper(preg_replace('/[^a-zA-Z0-9]/', '_', $package->name)) : 'HOME_20M';
            $profileName = "PROFILE_{$pkgName}";

            // 1. Update FreeRADIUS radusergroup
            DB::connection('radius')->table('radusergroup')->updateOrInsert(
                ['username' => $username],
                ['groupname' => $profileName, 'priority' => 1]
            );

            // 2. Update CRM radius_users
            $user->update([
                'status' => 'CONNECTED',
                'profile_name' => $profileName,
                'last_online_at' => Carbon::now(),
            ]);

            // Kick user so they reconnect with restored profile immediately
            $this->disconnectUser($username);
            return true;
        }

        return false;
    }

    /**
     * Delete user from FreeRADIUS core tables and CRM bridge.
     */
    public function deleteUser(string $username): void
    {
        DB::connection('radius')->table('radcheck')->where('username', $username)->delete();
        DB::connection('radius')->table('radusergroup')->where('username', $username)->delete();
        DB::connection('radius')->table('radreply')->where('username', $username)->delete();
        RadiusUser::where('username', $username)->delete();
    }

    /**
     * Sync Router NAS to FreeRADIUS nas table.
     */
    public function syncNas(RadiusNas $nas): void
    {
        DB::connection('radius')->table('nas')->updateOrInsert(
            ['nasname' => $nas->nasname],
            [
                'shortname' => $nas->shortname,
                'type' => $nas->type ?? 'mikrotik',
                'ports' => $nas->ports ?? 1812,
                'secret' => $nas->secret,
                'server' => $nas->server,
                'community' => $nas->community,
                'description' => $nas->description,
            ]
        );
    }

    /**
     * Hardware Probe Socket Test (MikroTik 8728, OLT 23/80, RADIUS 1812).
     */
    public function probeHardware(string $host, int $port, float $timeout = 0.5): array
    {
        $startTime = microtime(true);
        $fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
        $latencyMs = round((microtime(true) - $startTime) * 1000, 2);

        if ($fp) {
            fclose($fp);
            return [
                'host' => $host,
                'port' => $port,
                'status' => 'ONLINE',
                'latency_ms' => $latencyMs,
            ];
        }

        return [
            'host' => $host,
            'port' => $port,
            'status' => 'OFFLINE',
            'error' => $errstr ?: 'Connection timed out',
            'latency_ms' => $latencyMs,
        ];
    }
}
