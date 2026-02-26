<?php

namespace App\Domain\Shared\Interfaces;

interface IdGeneratorInterface
{
    public function generate(string $prefix): string;
}