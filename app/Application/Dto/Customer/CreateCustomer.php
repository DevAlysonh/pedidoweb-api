<?php

namespace App\Application\Dto\Customer;

final class CreateCustomer
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $street,
        public readonly string $number,
        public readonly string $city,
        public readonly string $state,
        public readonly string $zipcode,
    ) {}
}
