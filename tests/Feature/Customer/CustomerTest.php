<?php

namespace Tests\Feature\Customer;

use App\Application\Dto\CepData;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class FakeCepService implements \App\Infrastructure\Services\CepService
{
    private ?CepData $dataToReturn = null;
    private ?bool $shouldReturnNull = false;

    public function setData(CepData $data): void
    {
        $this->dataToReturn = $data;
        $this->shouldReturnNull = false;
    }

    public function setNull(): void
    {
        $this->shouldReturnNull = true;
        $this->dataToReturn = null;
    }

    public function lookup(string $zipcode): ?CepData
    {
        if ($this->shouldReturnNull) {
            return null;
        }
        return $this->dataToReturn;
    }
}

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_customer_with_valid_zipcode(): void
    {
        $user = $this->createAuthenticatedUser();

        $payload = [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'street' => 'Rua A',
            'number' => '123',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zipcode' => '01001-000'
        ];

        $response = $this->actingAs($user, 'api')->postJson(
            route('customer.create'),
            $payload
        );

        if ($response->status() !== Response::HTTP_CREATED) {
            echo "Response: " . $response->getContent();
        }

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('customers', [
            'name' => 'João Silva',
            'email' => 'joao@example.com'
        ]);

        $this->assertDatabaseHas('addresses', [
            'zipcode' => '01001-000',
            'city' => 'São Paulo'
        ]);
    }

    private function createAuthenticatedUser()
    {
        return User::factory()->createOne();
    }
}
