<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\VO\UserId;
use App\Infrastructure\Persistence\Eloquent\Models\User as UserModel;
use Throwable;

class UserRepository implements UserRepositoryInterface
{
    public function findById(string $id): ?User
    {
        $user = UserModel::find($id);
        return $user ? $this->toDomain($user) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $user = UserModel::where('email', $email)->first();

        return $user ? $this->toDomain($user) : null;
    }

    public function save(User $user): void
    {
        try {
            $userModel = UserModel::find($user->id()->value());

            if ($userModel) {
                $userModel->update([
                    'name' => $user->name(),
                    'email' => $user->email(),
                    'password' => $user->password(),
                ]);
            } else {
                UserModel::create([
                    'id' => $user->id()->value(),
                    'name' => $user->name(),
                    'email' => $user->email(),
                    'password' => $user->password(),
                ]);
            }
        } catch (Throwable $e) {
            throw $e;
        }
    }

    private function toDomain(UserModel $user): User
    {
        return new User(
            id: UserId::fromString($user->id),
            name: $user->name,
            email: $user->email,
            password: $user->password
        );
    }
}
