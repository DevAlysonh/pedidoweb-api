<?php

namespace Tests\Unit\Application\UseCases\User;

use App\Application\Dto\User\LoginUserDTO;
use App\Application\UseCases\User\LoginUseCase;
use App\Domain\Shared\Interfaces\PasswordHasherInterface;
use App\Domain\Shared\Interfaces\TokenGeneratorInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\Exceptions\InvalidCredentialsException;
use App\Domain\User\VO\UserId;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class LoginUseCaseTest extends TestCase
{
    public function testExecuteThrowsInvalidCredentialsExceptionWithInvalidPassword()
    {
        $password = 'password123';
        $hashedPassword = Hash::make($password);

        $userId = UserId::fromString('xpto123');
        $user = new User(
            id: $userId,
            name: 'João Silva',
            email: 'joao@email.com',
            password: $hashedPassword
        );

        $repository = $this->getMockBuilder(UserRepositoryInterface::class)
            ->onlyMethods(['findById', 'findByEmail', 'save'])
            ->getMock();
        $repository->method('findByEmail')->willReturn($user);

        $hasher = $this->getMockBuilder(PasswordHasherInterface::class)
            ->onlyMethods(['hash', 'verify'])
            ->getMock();
        $hasher->method('verify')->willReturn(false);

        $tokenGenerator = $this->getMockBuilder(TokenGeneratorInterface::class)
            ->onlyMethods(['generate'])
            ->getMock();

        $loginDTO = new LoginUserDTO('joao@email.com', 'wrongpassword');
        $useCase = new LoginUseCase($repository, $hasher, $tokenGenerator);

        $this->expectException(InvalidCredentialsException::class);
        $useCase->execute($loginDTO);
    }

    public function testExecuteThrowsInvalidCredentialsExceptionWithNonexistentEmail()
    {
        $repository = $this->getMockBuilder(UserRepositoryInterface::class)
            ->onlyMethods(['findById', 'findByEmail', 'save'])
            ->getMock();

        $repository->method('findByEmail')->willReturn(null);

        $hasher = $this->getMockBuilder(PasswordHasherInterface::class)
            ->onlyMethods(['hash', 'verify'])
            ->getMock();

        $tokenGenerator = $this->getMockBuilder(TokenGeneratorInterface::class)
            ->onlyMethods(['generate'])
            ->getMock();

        $loginDTO = new LoginUserDTO('nonexistent@email.com', 'password123');
        $useCase = new LoginUseCase($repository, $hasher, $tokenGenerator);

        $this->expectException(InvalidCredentialsException::class);
        $useCase->execute($loginDTO);
    }
}
