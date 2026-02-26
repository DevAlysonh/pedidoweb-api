<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receive_token(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Foo Bar',
            'email' => 'foo@email.com',
            'password' => '123456',
        ]);

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in'
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'foo@email.com'
        ]);
    }

    public function test_cannot_register_with_existing_email(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Foo Bar',
            'email' => 'foo@email.com',
            'password' => '123456',
        ]);

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Outro',
            'email' => 'foo@email.com',
            'password' => '123456',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_user_can_login_and_receive_token(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Foo Bar',
            'email' => 'foo@email.com',
            'password' => '123456',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'foo@email.com',
            'password' => '123456',
        ]);

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in'
            ]);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Foo Bar',
            'email' => 'foo@email.com',
            'password' => '123456',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'foo@email.com',
            'password' => 'wrongpass',
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_authenticated_user_can_access_me(): void
    {
        $register = $this->postJson('/api/v1/auth/register', [
            'name' => 'Foo Bar',
            'email' => 'foo@email.com',
            'password' => '123456',
        ]);

        $token = $register->json('access_token');

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/v1/auth/me');

        $response
            ->assertStatus(200)
            ->assertJson([
                'email' => 'foo@email.com'
            ]);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Foo Bar',
            'email' => 'foo@email.com',
            'password' => '123456',
        ]);

        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'foo@email.com',
            'password' => '123456',
        ]);

        $token = $loginResponse->json('access_token');

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/auth/logout');

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Successfully logged out'
            ]);
    }

    public function test_guest_cannot_logout(): void
    {
        $this->postJson('/api/v1/auth/logout')
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
