<?php

namespace Tests\Feature\Customer;

use App\Application\Dto\CepData;
use App\Infrastructure\Persistence\Eloquent\Models\Address;
use App\Infrastructure\Persistence\Eloquent\Models\Customer;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
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
            'zipcode' => '01001000',
            'city' => 'São Paulo'
        ]);
    }

    public function test_customer_cannot_be_created_with_invalid_address(): void
    {
        $this->withExceptionHandling();

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

    public function test_authenticated_user_can_list_customers(): void
    {
        $user = $this->createAuthenticatedUser();
        $this->actingAs($user, 'api');

        Customer::factory()
            ->for($user)
            ->has(Address::factory()->count(1))
            ->count(3)
            ->create();

        $response = $this->getJson(route('customer.index'));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(3, $response->json('data'));
    }

    public function test_authenticated_user_can_show_customer(): void
    {
        $user = $this->createAuthenticatedUser();
        $this->actingAs($user, 'api');

        $customer = Customer::factory()
            ->for($user)
            ->has(Address::factory()->count(1))
            ->createOne();

        $response = $this->get(route('customer.show', $customer->id));

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEquals($customer->id, $response->json('data.id'));
    }

    public function test_authenticated_user_can_update_customer(): void
    {
        $user = $this->createAuthenticatedUser();

        $customer = Customer::factory()
            ->for($user)
            ->has(Address::factory()->count(1))
            ->createOne();

        $payload = [
            'name' => 'Maria Souza',
            'email' => 'maria@example.com',
        ];

        $response = $this->actingAs($user, 'api')
            ->patchJson(route('customer.update', $customer->id), $payload);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Maria Souza',
            'email' => 'maria@example.com',
        ]);
    }

    public function test_authenticated_user_can_delete_customer(): void
    {
        $user = $this->createAuthenticatedUser();
        $this->actingAs($user, 'api');

        $customer = Customer::factory()
            ->for($user)
            ->has(Address::factory()->count(1))
            ->createOne();

        $response = $this->delete(route('customer.delete', $customer->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('customers', [
            'id' => $customer->id,
        ]);
    }

    public function test_authenticated_user_can_update_customer_address(): void
    {
        $user = $this->createAuthenticatedUser();

        $customer = Customer::factory()
            ->for($user)
            ->has(Address::factory()->count(1))
            ->createOne();

        $payload = [
            'street' => 'Avenida B',
            'number' => '456',
            'city' => 'João Pessoa',
            'state' => 'PB',
            'zipcode' => '58052197'
        ];

        $response = $this->actingAs($user, 'api')
            ->patchJson(route('customer.update-address', $customer->id), $payload);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('addresses', [
            'id' => $customer->address->id,
            'street' => 'Avenida B',
            'number' => '456',
            'city' => 'João Pessoa',
            'state' => 'PB',
            'zipcode' => '58052197'
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
