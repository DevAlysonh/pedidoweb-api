<?php

declare(strict_types=1);

namespace App\Application\Dto;

final class CepData
{
    public function __construct(
        public readonly string $zipcode,
        public readonly string $street,
        public readonly string $city,
        public readonly string $state,
    ) {}
}
