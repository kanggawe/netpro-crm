<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    protected array $supportedProviders = [
        'google',
        'github',
        'facebook',
        'twitter',
        'twitter-oauth-2',
    ];

    /**
     * Get OAuth Redirect URL for the given provider.
     */
    public function redirect(Request $request, string $provider)
    {
        $normalizedProvider = $provider === 'twitter' ? 'twitter-oauth-2' : $provider;

        if (!in_array($normalizedProvider, $this->supportedProviders) && !in_array($provider, $this->supportedProviders)) {
            return response()->json([
                'status' => 'error',
                'message' => "Provider OAuth '{$provider}' tidak didukung.",
            ], 400);
        }

        try {
            /** @var \Laravel\Socialite\Two\AbstractProvider $driver */
            $driver = Socialite::driver($normalizedProvider)->stateless();

            // Set optional scopes
            if ($provider === 'google') {
                $driver->scopes(['openid', 'profile', 'email']);
            } elseif ($provider === 'github') {
                $driver->scopes(['user:email', 'read:user']);
            }

            $targetUrl = $driver->redirect()->getTargetUrl();

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'success',
                    'provider' => $provider,
                    'redirect_url' => $targetUrl,
                ]);
            }

            return redirect()->away($targetUrl);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => "Gagal menginisiasi OAuth {$provider}: " . $e->getMessage(),
                'fallback_mock' => true,
            ], 500);
        }
    }

    /**
     * Handle OAuth Callback from social media provider.
     */
    public function callback(Request $request, string $provider)
    {
        $normalizedProvider = $provider === 'twitter' ? 'twitter-oauth-2' : $provider;

        try {
            $socialUser = Socialite::driver($normalizedProvider)->stateless()->user();
        } catch (\Exception $e) {
            // If callback failed or credentials not yet configured in .env, check if test mode or return helpful error
            return response()->json([
                'status' => 'error',
                'message' => "Autentikasi OAuth {$provider} gagal: " . $e->getMessage(),
            ], 400);
        }

        $email = $socialUser->getEmail();
        $name = $socialUser->getName() ?: $socialUser->getNickname() ?: 'Social User';
        $oauthId = $socialUser->getId();
        $avatar = $socialUser->getAvatar();

        // Check if user already exists with this oauth_id or email
        $user = User::where('oauth_provider', $provider)
            ->where('oauth_id', (string) $oauthId)
            ->first();

        if (!$user && $email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                // Link account
                $user->update([
                    'oauth_provider' => $provider,
                    'oauth_id' => (string) $oauthId,
                    'avatar' => $avatar ?: $user->avatar,
                    'oauth_data' => array_merge($user->oauth_data ?? [], [
                        $provider => [
                            'id' => $oauthId,
                            'name' => $name,
                            'email' => $email,
                            'linked_at' => now()->toIso8601String(),
                        ],
                    ]),
                ]);
            }
        }

        if (!$user) {
            // Register new user via OAuth
            $username = strtolower(Str::slug($name, '_')) ?: 'user_' . Str::random(6);
            if (User::where('username', $username)->exists()) {
                $username .= '_' . rand(100, 999);
            }

            $user = User::create([
                'username' => $username,
                'name' => $name,
                'full_name' => $name,
                'email' => $email ?: "{$username}@oauth.netpro.id",
                'role' => 'staff',
                'division' => 'Operasional & Bisnis',
                'status' => 'active',
                'password' => Hash::make(Str::random(32)),
                'oauth_provider' => $provider,
                'oauth_id' => (string) $oauthId,
                'avatar' => $avatar,
                'oauth_data' => [
                    $provider => [
                        'id' => $oauthId,
                        'name' => $name,
                        'email' => $email,
                        'linked_at' => now()->toIso8601String(),
                    ],
                ],
            ]);

            AuditLog::log($user->username, 'OAUTH_REGISTER', "Registrasi baru via {$provider} SSO ({$user->email})");
        } else {
            AuditLog::log($user->username, 'OAUTH_LOGIN', "Masuk via {$provider} OAuth SSO");
        }

        $token = $user->createToken('netpro-oauth-token')->plainTextToken;


        // $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
        $frontendUrl = env('FRONTEND_URL', config('app.frontend_url', 'http://localhost:5173'));
        $redirectTarget = "{$frontendUrl}?token=" . urlencode($token) . "&oauth=success&provider=" . urlencode($provider);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Autentikasi {$provider} berhasil.",
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'avatar' => $user->avatar,
                        'oauth_provider' => $user->oauth_provider,
                    ],
                ],
            ]);
        }

        return redirect()->away($redirectTarget);
    }

    /**
     * Link social media account to authenticated user.
     */
    public function link(Request $request, string $provider): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $validated = $request->validate([
            'oauth_id' => 'required|string',
            'email' => 'nullable|email',
            'name' => 'nullable|string',
            'avatar' => 'nullable|string',
        ]);

        $oauthData = $user->oauth_data ?? [];
        $oauthData[$provider] = [
            'id' => $validated['oauth_id'],
            'email' => $validated['email'] ?? $user->email,
            'name' => $validated['name'] ?? $user->name,
            'linked_at' => now()->toIso8601String(),
        ];

        $user->update([
            'oauth_provider' => $provider,
            'oauth_id' => $validated['oauth_id'],
            'avatar' => $validated['avatar'] ?? $user->avatar,
            'oauth_data' => $oauthData,
        ]);

        AuditLog::log($user->username, 'OAUTH_LINK', "Menautkan akun media sosial: {$provider}");

        return response()->json([
            'status' => 'success',
            'message' => "Akun {$provider} berhasil ditautkan ke akun Anda.",
            'data' => [
                'user' => $user,
                'linked_providers' => array_keys($oauthData),
            ],
        ]);
    }

    /**
     * Unlink social media account from authenticated user.
     */
    public function unlink(Request $request, string $provider): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $oauthData = $user->oauth_data ?? [];

        if (isset($oauthData[$provider])) {
            unset($oauthData[$provider]);
        }

        $user->update([
            'oauth_provider' => !empty($oauthData) ? array_key_first($oauthData) : null,
            'oauth_id' => !empty($oauthData) ? $oauthData[array_key_first($oauthData)]['id'] ?? null : null,
            'oauth_data' => $oauthData,
        ]);

        AuditLog::log($user->username, 'OAUTH_UNLINK', "Memutuskan tautan akun media sosial: {$provider}");

        return response()->json([
            'status' => 'success',
            'message' => "Tautan akun {$provider} berhasil diputuskan.",
            'data' => [
                'linked_providers' => array_keys($oauthData),
            ],
        ]);
    }

    /**
     * Get all connected social accounts for the user.
     */
    public function linkedAccounts(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $oauthData = $user->oauth_data ?? [];

        $providers = [
            'google' => [
                'name' => 'Google Account',
                'connected' => isset($oauthData['google']) || $user->oauth_provider === 'google',
                'email' => $oauthData['google']['email'] ?? ($user->oauth_provider === 'google' ? $user->email : null),
                'linked_at' => $oauthData['google']['linked_at'] ?? null,
            ],
            'github' => [
                'name' => 'GitHub Developer',
                'connected' => isset($oauthData['github']) || $user->oauth_provider === 'github',
                'email' => $oauthData['github']['email'] ?? ($user->oauth_provider === 'github' ? $user->email : null),
                'linked_at' => $oauthData['github']['linked_at'] ?? null,
            ],
            'facebook' => [
                'name' => 'Facebook Account',
                'connected' => isset($oauthData['facebook']) || $user->oauth_provider === 'facebook',
                'email' => $oauthData['facebook']['email'] ?? ($user->oauth_provider === 'facebook' ? $user->email : null),
                'linked_at' => $oauthData['facebook']['linked_at'] ?? null,
            ],
            'twitter' => [
                'name' => 'X (Twitter)',
                'connected' => isset($oauthData['twitter']) || isset($oauthData['twitter-oauth-2']) || $user->oauth_provider === 'twitter',
                'email' => $oauthData['twitter']['email'] ?? ($user->oauth_provider === 'twitter' ? $user->email : null),
                'linked_at' => $oauthData['twitter']['linked_at'] ?? null,
            ],
        ];

        return response()->json([
            'status' => 'success',
            'data' => $providers,
        ]);
    }
}
