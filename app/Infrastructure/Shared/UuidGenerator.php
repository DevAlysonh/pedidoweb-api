<?php

namespace App\Infrastructure\Shared;

use App\Domain\Shared\Interfaces\IdGeneratorInterface;
use Illuminate\Support\Str;

class UuidGenerator implements IdGeneratorInterface
{
    public function generate(string $prefix = ''): string
    {
        return $prefix . Str::uuid()->toString();
    }
}