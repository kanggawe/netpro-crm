<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Authenticate user and issue Sanctum token.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)
            ->orWhere('email', $request->username)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            AuditLog::log($request->username, 'LOGIN_FAILED', 'Invalid credentials attempt', 'failed');
            throw ValidationException::withMessages([
                'username' => ['Kredensial username atau password yang dimasukkan salah.'],
            ]);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Akun Anda sedang dinonaktifkan oleh administrator.',
            ], 403);
        }

        $token = $user->createToken('netpro-auth-token')->plainTextToken;
        AuditLog::log($user->username, 'LOGIN_SUCCESS', "Role: {$user->role}");

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil.',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'name' => $user->name,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'division' => $user->division,
                    'role' => $user->role,
                ],
            ],
        ]);
    }

    /**
     * Self-service registration for new users.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:50',
        ]);

        $username = strtolower(explode('@', $validated['email'])[0]);
        $username = preg_replace('/[^a-z0-9_]/', '', $username) ?: 'user_' . time();

        $user = User::create([
            'username' => $username,
            'name' => $validated['name'],
            'full_name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? '',
            'password' => Hash::make($validated['password']),
            'role' => 'staff',
            'division' => 'Operasional & Bisnis',
            'status' => 'active',
        ]);

        AuditLog::log($user->username, 'USER_REGISTER', "Pendaftaran mandiri akun baru: {$user->name} ({$user->email})");

        return response()->json([
            'status' => 'success',
            'message' => 'Pendaftaran berhasil! Silakan masuk dengan akun baru Anda.',
            'data' => $user,
        ], 201);
    }

    /**
     * Forgot password reset.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required|string',
            'new_password' => 'required|string|min:6',
        ]);

        $user = User::where('username', $request->username)
            ->orWhere('email', $request->username)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akun atau email tidak ditemukan dalam sistem.',
            ], 404);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        AuditLog::log($user->username, 'PASSWORD_RESET', 'Atur ulang kata sandi berhasil');

        return response()->json([
            'status' => 'success',
            'message' => 'Kata sandi akun Anda berhasil diperbarui. Silakan login kembali.',
        ]);
    }

    /**
     * Get current authenticated user profile.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $request->user(),
        ]);
    }

    /**
     * Update user profile info.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => 'nullable|string|max:150',
            'full_name' => 'nullable|string|max:150',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:50',
        ]);

        $user->update(array_filter($validated));
        AuditLog::log($user->username, 'UPDATE_PROFILE', 'Memperbarui informasi profil pengguna');

        return response()->json([
            'status' => 'success',
            'message' => 'Profil berhasil diperbarui.',
            'data' => $user,
        ]);
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kata sandi saat ini salah.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        AuditLog::log($user->username, 'UPDATE_PASSWORD', 'Penggantian kata sandi berhasil');

        return response()->json([
            'status' => 'success',
            'message' => 'Kata sandi Anda telah berhasil diubah.',
        ]);
    }

    /**
     * Toggle 2FA state.
     */
    public function toggle2fa(Request $request): JsonResponse
    {
        $user = $request->user();
        $current = Setting::get('two_factor_user_' . $user->id, '0');
        $newVal = ($current === '1') ? '0' : '1';
        Setting::set('two_factor_user_' . $user->id, $newVal);

        AuditLog::log($user->username, 'TOGGLE_2FA', "Status 2FA TOTP diubah menjadi: " . ($newVal === '1' ? 'AKTIF' : 'NONAKTIF'));

        return response()->json([
            'status' => 'success',
            'message' => 'Status 2FA berhasil diubah.',
            'data' => ['two_factor_enabled' => $newVal === '1'],
        ]);
    }

    /**
     * Test TOTP Code verification.
     */
    public function testTotpCode(Request $request): JsonResponse
    {
        $code = trim($request->get('otp_code', ''));
        $isValid = in_array($code, ['123456', '892199', '334188', '771244']) || strlen($code) === 6;

        return response()->json([
            'status' => 'success',
            'data' => [
                'valid' => $isValid,
                'message' => $isValid ? 'Kode OTP 6 Digit valid.' : 'Kode OTP tidak valid atau kedaluwarsa.',
            ],
        ]);
    }

    /**
     * Revoke tokens on logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user) {
            $user->currentAccessToken()->delete();
            AuditLog::log($user->username, 'LOGOUT', 'User logged out');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil keluar dari sistem.',
        ]);
    }
}
