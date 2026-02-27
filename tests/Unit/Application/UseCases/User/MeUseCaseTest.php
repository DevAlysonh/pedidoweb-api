<?php

namespace Tests\Unit\Application\UseCases\User;

use App\Application\UseCases\User\MeUseCase;
use App\Domain\User\Entities\User;
use App\Domain\User\Exceptions\UserNotFoundException;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\VO\UserId;
use PHPUnit\Framework\TestCase;

class MeUseCaseTest extends TestCase
{
    public function testExecuteSuccess(): void
    {
        $userId = UserId::fromString('usr_1');
        $user = new User(
            $userId,
            'João Silva',
            'joao@email.com',
            'hashed_password'
        );

        $repository = $this->getMockBuilder(UserRepositoryInterface::class)
            ->onlyMethods(['findByEmail', 'findById', 'save'])
            ->getMock();

        $repository->method('findById')->willReturn($user);

        $useCase = new MeUseCase($repository);
        $result = $useCase->execute($userId);

        $this->assertEquals('usr_1', $result->id()->value());
        $this->assertEquals('João Silva', $result->name());
        $this->assertEquals('joao@email.com', $result->email());
    }

    public function testExecuteWithNonExistentUser(): void
    {
        $userId = UserId::fromString('usr_1');
        $repository = $this->getMockBuilder(UserRepositoryInterface::class)
            ->onlyMethods(['findByEmail', 'findById', 'save'])
            ->getMock();

        $repository->method('findById')->willReturn(null);

        $this->expectException(UserNotFoundException::class);

        $useCase = new MeUseCase($repository);
        $useCase->execute($userId);
    }
}
