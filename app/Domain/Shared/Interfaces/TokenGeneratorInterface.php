<?php

namespace App\Domain\Shared\Interfaces;

use App\Domain\User\Entities\User;

interface TokenGeneratorInterface
{
    public function generate(User $user): string;
}