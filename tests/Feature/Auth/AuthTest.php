<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_register_with_valid_data(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => ['access_token', 'token_type', 'expires_in'],
            ]);
    }

    public function test_cannot_register_with_duplicate_email(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => 'password123',
        ])->assertCreated();

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Maria Santos',
            'email' => 'joao@example.com',
            'password' => 'password123',
        ]);

        $response->assertConflict();
    }

    public function test_can_login_with_valid_credentials(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => 'password123',
        ])->assertCreated();

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'joao@example.com',
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'data' => ['access_token', 'token_type', 'expires_in'],
            ]);
    }

    public function test_cannot_login_with_invalid_password(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => 'password123',
        ])->assertCreated();

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'joao@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertUnauthorized();
    }

    public function test_cannot_login_with_non_existing_email(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_get_profile(): void
    {
        $registerResponse = $this->postJson('/api/v1/auth/register', [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => 'password123',
        ])->assertCreated();

        $token = $registerResponse->json('data.access_token');

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/v1/auth/me');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                ],
            ]);
    }

    public function test_unauthenticated_user_cannot_get_profile(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $registerResponse = $this->postJson('/api/v1/auth/register', [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => 'password123',
        ])->assertCreated();

        $token = $registerResponse->json('data.access_token');

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/v1/auth/logout');

        $response->assertOk();
    }

    public function test_unauthenticated_user_cannot_logout(): void
    {
        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertUnauthorized();
    }
}
