<?php

namespace App\Domain\Customer\VO;

final class Address
{
    public function __construct(
        private string $id,
        private string $customerId,
        private string $street,
        private string $number,
        private string $city,
        private string $state,
        private string $zipcode
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function customerId(): string
    {
        return $this->customerId;
    }

    public function street(): string
    {
        return $this->street;
    }

    public function number(): string
    {
        return $this->number;
    }

    public function city(): string
    {
        return $this->city;
    }

    public function state(): string
    {
        return $this->state;
    }

    public function zipcode(): string
    {
        return $this->zipcode;
    }
}
