<?php

namespace App\Infrastructure\Shared;

use App\Domain\Shared\Interfaces\IdGenerator;
use Illuminate\Support\Str;

class UuidGenerator implements IdGenerator
{
    public function generate(string $prefix = ''): string
    {
        return $prefix . Str::uuid()->toString();
    }
}