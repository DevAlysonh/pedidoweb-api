<?php

declare(strict_types=1);

namespace App\Application\UseCases\User;

use App\Application\Dto\User\RegisterUserDTO;
use App\Domain\User\Entities\User;
use App\Domain\User\Exceptions\UserAlreadyExistsException;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\VO\UserId;
use App\Domain\Shared\Interfaces\IdGeneratorInterface;
use App\Domain\Shared\Interfaces\PasswordHasherInterface;

class RegisterUseCase
{
    public function __construct(
        private UserRepositoryInterface $repository,
        private IdGeneratorInterface $idGenerator,
        private PasswordHasherInterface $passwordHasher
    ) {}

    public function execute(RegisterUserDTO $dto): User
    {
        $existingUser = $this->repository->findByEmail($dto->email);
        if ($existingUser) {
            throw new UserAlreadyExistsException();
        }

        $userId = $this->idGenerator->generate(User::PREFIX);
        $hashedPassword = $this->passwordHasher->hash($dto->password);

        $user = new User(
            id: UserId::fromString($userId),
            name: $dto->name,
            email: $dto->email,
            password: $hashedPassword
        );

        $this->repository->save($user);

        return $user;
    }
}
