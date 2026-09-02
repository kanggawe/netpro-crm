<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthAndCustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_login_and_retrieve_profile(): void
    {
        $this->seed();

        $response = $this->postJson('/api/v1/auth/login', [
            'username' => 'superadmin',
            'password' => 'admin123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'token',
                    'user' => ['id', 'username', 'role'],
                ],
            ]);

        $token = $response->json('data.token');

        // Test authenticated profile
        $meResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/auth/me');

        $meResponse->assertStatus(200)
            ->assertJsonPath('data.username', 'superadmin');
    }

    public function test_customer_registration_and_invoice_creation(): void
    {
        $this->seed();
        $user = User::where('username', 'superadmin')->first();
        $package = Package::first();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/customers', [
                'name' => 'John Doe ISP',
                'nik' => '3275019988770002',
                'phone' => '081234567890',
                'address' => 'Jl. Merdeka No. 10',
                'package_id' => $package->id,
                'billing_type' => 'postpaid',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'John Doe ISP')
            ->assertJsonStructure(['data' => ['cid', 'pppoe_user']]);

        $customerId = $response->json('data.id');

        // Test activate online
        $onlineResponse = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/customers/{$customerId}/set-online");

        $onlineResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'active');
    }
}
