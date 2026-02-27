<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\User\Entities\User as DomainUser;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\User as EloquentUser;

// TODO: Refatorar o regerenciamento de usuarios no auth controller, para usar usecases.
class UserRepository implements UserRepositoryInterface
{
    public function findById(string $id): ?DomainUser
    {
        $user = EloquentUser::find($id);
        return $user ? $this->toDomain($user) : null;
    }

    public function findByEmail(string $email): ?DomainUser
    {
        $user = EloquentUser::where('email', $email)->first();
        return $user ? $this->toDomain($user) : null;
    }

    public function save(DomainUser $user): void
    {
        $eloquentUser = new EloquentUser();
        $eloquentUser->id = $user->id();
        $eloquentUser->name = $user->name();
        $eloquentUser->email = $user->email();
        $eloquentUser->save();
    }

    // private function toDomain(EloquentUser $user): DomainUser
    // {
    //     return new DomainUser(
    //         $user->id,
    //         $user->name,
    //         $user->email
    //     );
    // }
}
