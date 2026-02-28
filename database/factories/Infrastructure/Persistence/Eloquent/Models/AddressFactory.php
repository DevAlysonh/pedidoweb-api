<?php

namespace Database\Factories\Infrastructure\Persistence\Eloquent\Models;

use App\Infrastructure\Persistence\Eloquent\Models\Address;
use App\Infrastructure\Persistence\Eloquent\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;


class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'customer_id' => Customer::factory(),
            'street' => fake()->streetAddress(),
            'number' => fake()->randomNumber(3),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'zipcode' => fake()->numerify('########')
        ];
    }
}
