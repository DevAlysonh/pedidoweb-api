<?php

namespace App\Domain\Customer\Entities;

use App\Domain\Customer\VO\Address;
use App\Domain\Customer\VO\CustomerId;
use App\Domain\User\VO\UserId;

final class Customer
{
    public const PREFIX = 'cus_';

    public function __construct(
        private CustomerId $id,
        private string $name,
        private string $email,
        private Address $address,
        private UserId $userId
    ) { }

    public function id(): CustomerId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function address(): Address
    {
        return $this->address;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function update(string $name, string $email): void
    {
        $this->name = $name ?: $this->name;
        $this->email = $email ?: $this->email;
    }

    public function changeAddress(Address $address): void
    {
        $this->address = $address;
    }

    public function snapshot(): array
    {
        return [
            'id' => $this->id()->value(),
            'name' => $this->name(),
            'email' => $this->email(),
            'address' => [
                'street' => $this->address()->street(),
                'number' => $this->address()->number(),
                'city' => $this->address()->city(),
                'state' => $this->address()->state(),
                'zipcode' => $this->address()->zipcode(),
            ],
            'user_id' => $this->userId()->value(),
        ];
    }
}
