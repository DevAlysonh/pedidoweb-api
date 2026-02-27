<?php

declare(strict_types=1);

namespace App\Application\UseCases\User;

use App\Application\Dto\User\LoginUserDTO;
use App\Domain\Shared\Interfaces\PasswordHasherInterface;
use App\Domain\Shared\Interfaces\TokenGeneratorInterface;
use App\Domain\User\Exceptions\InvalidCredentialsException;
use App\Domain\User\Repositories\UserRepositoryInterface;

class LoginUseCase
{
    public function __construct(
        private UserRepositoryInterface $repository,
        private PasswordHasherInterface $passwordHasher,
        private TokenGeneratorInterface $tokenGenerator
    ) {}

    public function execute(LoginUserDTO $dto): string
    {
        $user = $this->repository->findByEmail($dto->email);

        if (!$user || !$this->passwordHasher->verify($dto->password, $user->password())) {
            throw new InvalidCredentialsException();
        }

        $token = $this->tokenGenerator->generate($user);

        return $token;
    }
}
