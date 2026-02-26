<?php

namespace App\Domain\Customer\Entities;

use App\Domain\Customer\VO\Address;

final class Customer
{
    public function __construct(
        private string $id,
        private string $name,
        private string $email,
        private Address $address
    ) { }

    public function id(): string
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
}
