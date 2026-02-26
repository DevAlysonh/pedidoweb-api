<?php

namespace Tests\Feature\Customer;

use App\Application\Dto\CepData;
use App\Domain\Customer\Exceptions\InvalidZipcodeException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Psy\Util\Str;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_customer_with_valid_zipcode(): void
    {
        $fakeCepService = $this->fakeCepService(
            zipcode: '01001-000',
            number: '123',
            street: 'Rua A',
            city: 'São Paulo',
            state: 'SP'
        );

        $this->app->instance(\App\Infrastructure\Services\CepService::class, $fakeCepService);

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

    public function test_customer_cannot_be_created_with_invalid_address(): void
    {
        $fakeCepService = $this->fakeCepService(
            zipcode: '58052197',
            number: '123',
            street: 'Rejane F Correia',
            city: 'João Pessoa',
            state: 'PB'
        );

        $this->app->instance(\App\Infrastructure\Services\CepService::class, $fakeCepService);

        $user = $this->createAuthenticatedUser();

        $payload = [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'street' => 'Rua A',
            'number' => '123',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zipcode' => '58052197'
        ];

        $response = $this->actingAs($user, 'api')->postJson(
            route('customer.create'),
            $payload
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonFragment([
                'message' => 'CEP inválido ou incorreto para este endereço: 58052197'
            ]);
    }

    private function createAuthenticatedUser()
    {
        return User::factory()->createOne();
    }

    private function fakeCepService(
        string $zipcode,
        string $number,
        string $street,
        string $city,
        string $state
    ): FakeCepService {
        $fakeService = new FakeCepService();
        $fakeService->setData(new CepData(
            zipcode: $zipcode,
            number: $number,
            street: $street,
            city: $city,
            state: $state
        ));

        return $fakeService;
    }
}

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
