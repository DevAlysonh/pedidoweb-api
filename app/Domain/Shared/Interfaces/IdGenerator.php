<?php

namespace App\Domain\Shared\Interfaces;

interface IdGenerator
{
    public function generate(string $prefix): string;
}