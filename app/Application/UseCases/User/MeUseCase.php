<?php

declare(strict_types=1);

namespace App\Application\UseCases\User;

use App\Domain\User\Entities\User;
use App\Domain\User\Exceptions\UserNotFoundException;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\VO\UserId;

class MeUseCase
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}

    public function execute(UserId $userId): User
    {
        $user = $this->repository->findById($userId->value());

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }
}
