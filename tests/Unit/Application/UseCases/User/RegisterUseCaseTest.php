<?php

namespace Tests\Unit\Application\UseCases\User;

use App\Application\Dto\User\RegisterUserDTO;
use App\Application\UseCases\User\RegisterUseCase;
use App\Domain\User\Entities\User;
use App\Domain\User\Exceptions\UserAlreadyExistsException;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\VO\UserId;
use App\Domain\Shared\Interfaces\IdGeneratorInterface;
use App\Domain\Shared\Interfaces\PasswordHasherInterface;
use Tests\TestCase;

class RegisterUseCaseTest extends TestCase
{
    public function testExecuteSuccess(): void
    {
        $dto = new RegisterUserDTO('João Silva', 'joao@email.com', 'password123');
        $repository = $this->getMockBuilder(UserRepositoryInterface::class)
            ->onlyMethods(['findByEmail', 'findById', 'save'])
            ->getMock();
        $idGenerator = $this->createMock(IdGeneratorInterface::class);

        $hasher = $this->getMockBuilder(PasswordHasherInterface::class)
            ->onlyMethods(['hash', 'verify'])
            ->getMock();
        $hasher->method('hash')->willReturn('hashed_password');

        $repository->method('findByEmail')->willReturn(null);
        $idGenerator->method('generate')->willReturn('usr_1');

        $repository->expects($this->once())->method('save');

        $useCase = new RegisterUseCase($repository, $idGenerator, $hasher);
        $user = $useCase->execute($dto);

        $this->assertEquals('usr_1', $user->id()->value());
        $this->assertEquals('João Silva', $user->name());
        $this->assertEquals('joao@email.com', $user->email());
    }

    public function testExecuteWithDuplicateEmail(): void
    {
        $dto = new RegisterUserDTO('João Silva', 'joao@email.com', 'password123');
        $repository = $this->getMockBuilder(UserRepositoryInterface::class)
            ->onlyMethods(['findByEmail', 'findById', 'save'])
            ->getMock();
        $idGenerator = $this->createMock(IdGeneratorInterface::class);

        $hasher = $this->getMockBuilder(PasswordHasherInterface::class)
            ->onlyMethods(['hash', 'verify'])
            ->getMock();
        $hasher->method('hash')->willReturn('hashed_password');

        $existingUser = new User(
            id: UserId::fromString('usr_existing'),
            name: 'Existing User',
            email: 'joao@email.com',
            password: 'hashed_password'
        );
        $repository->method('findByEmail')->willReturn($existingUser);

        $this->expectException(UserAlreadyExistsException::class);

        $useCase = new RegisterUseCase($repository, $idGenerator, $hasher);
        $useCase->execute($dto);
    }
}
