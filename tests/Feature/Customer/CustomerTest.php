<?php

namespace Tests\Feature\Customer;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_customer(): void
    {
        $user = User::factory()->createOne();

        $payload = [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'address' => [
                'street' => 'Rua A',
                'number' => '123',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zipcode' => '01001000'
            ]
        ];

        $response = $this->actingAs($user, 'api')->postJson(
            route('customer.create'),
            $payload
        );

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'name' => 'João Silva',
                'email' => 'joao@example.com',
            ]);

        $this->assertDatabaseHas('customers', [
            'email' => 'joao@example.com'
        ]);

        $this->assertDatabaseHas('addresses', [
            'zipcode' => '01001000'
        ]);
    }
}
