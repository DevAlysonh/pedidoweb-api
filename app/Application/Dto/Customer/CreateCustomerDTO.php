<?php

namespace App\Application\Dto\Customer;

final class CreateCustomerDTO
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

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            street: $data['street'],
            number: $data['number'],
            city: $data['city'],
            state: $data['state'],
            zipcode: $data['zipcode'],
        );
    }
}
