<?php

namespace App\Infrastructure\Shared;

use App\Domain\Shared\Interfaces\TokenGeneratorInterface;
use App\Domain\User\Entities\User;
use App\Infrastructure\Persistence\Eloquent\Models\User as UserModel;

class JwtTokenGenerator implements TokenGeneratorInterface
{
    public function generate(User $user): string
    {
        $eloquentUser = UserModel::where('email', $user->email())->firstOrFail();

        return auth('api')->fromUser($eloquentUser);
    }
}
