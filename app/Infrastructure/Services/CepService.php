<?php

declare(strict_types=1);

namespace App\Infrastructure\Services;

use App\Application\Dto\CepData;

interface CepService
{
    public function lookup(string $zipcode): ?CepData;
}
