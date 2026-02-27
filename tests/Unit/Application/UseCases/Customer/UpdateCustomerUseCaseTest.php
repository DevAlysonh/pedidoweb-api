<?php

namespace Tests\Unit\Application\UseCases\Customer;

use App\Application\UseCases\Customer\UpdateCustomerUseCase;
use App\Application\Dto\Customer\UpdateCustomerDto;
use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\Exceptions\CustomerNotFoundException;
use App\Domain\Customer\Exceptions\UnauthorizedException;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\Customer\VO\Address;
use App\Domain\Customer\VO\CustomerId;
use App\Domain\Shared\Interfaces\LoggerInterface;
use App\Domain\User\VO\UserId;
use PHPUnit\Framework\TestCase;

class UpdateCustomerUseCaseTest extends TestCase
{
    public function test_execute_updates_customer_when_authorized()
    {
        $customerId = CustomerId::fromString('cus_1');
        $userId = UserId::fromString('user_1');

        $customer = new Customer(
            id: $customerId,
            name: 'João',
            email: 'joao@email.com',
            userId: $userId,
            address: new Address(
                id: 'addr_1',
                street: 'Rua A',
                number: '123',
                city: 'Cidade',
                state: 'SP',
                zipcode: '12345678',
                customerId: $customerId->value()
            )
        );

        $repo = $this->getMockBuilder(CustomerRepositoryInterface::class)
            ->onlyMethods(['findById', 'save', 'findAllByUser', 'delete'])
            ->getMock();
        $repo->method('findById')->willReturn($customer);
        $repo->expects($this->once())->method('save')->with($customer);

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->onlyMethods(['info', 'warning', 'debug', 'error'])
            ->getMock();
        $logger->expects($this->once())->method('info');

        $dto = new UpdateCustomerDto($customer->name(), $customer->email());
        $useCase = new UpdateCustomerUseCase($repo, $logger);
        $result = $useCase->execute($customerId, $userId, $dto);
        $this->assertSame($customer, $result);
    }

    public function test_updateCustomerUseCase_should_throw_an_exception_when_customer_not_found()
    {
        $customerId = CustomerId::fromString('cus_1');
        $userId = UserId::fromString('user_1');

        $repo = $this->getMockBuilder(CustomerRepositoryInterface::class)
            ->onlyMethods(['findById', 'save', 'findAllByUser', 'delete'])
            ->getMock();
        $repo->method('findById')->willReturn(null);

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->onlyMethods(['info', 'warning', 'debug', 'error'])
            ->getMock();

        $dto = new UpdateCustomerDto('João', 'joao@email.com');
        $useCase = new UpdateCustomerUseCase($repo, $logger);
        $this->expectException(CustomerNotFoundException::class);
        $useCase->execute($customerId, $userId, $dto);
    }

    public function test_updateCustomerUseCase_should_throw_an_exception_when_user_is_not_owner()
    {
        $customerId = CustomerId::fromString('cus_1');
        $userId = UserId::fromString('user_1');

        $customer = new Customer(
            id: $customerId,
            name: 'João',
            email: 'joao@email.com',
            userId: $userId,
            address: new Address(
                id: 'addr_1',
                street: 'Rua A',
                number: '123',
                city: 'Cidade',
                state: 'SP',
                zipcode: '12345678',
                customerId: $customerId->value()
            )
        );

        $repo = $this->getMockBuilder(CustomerRepositoryInterface::class)
            ->onlyMethods(['findById', 'save', 'findAllByUser', 'delete'])
            ->getMock();
        $repo->method('findById')->willReturn($customer);

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->onlyMethods(['info', 'warning', 'debug', 'error'])
            ->getMock();

        $dto = new UpdateCustomerDto($customer->name(), $customer->email());
        $useCase = new UpdateCustomerUseCase($repo, $logger);
        $this->expectException(UnauthorizedException::class);
        $useCase->execute($customerId, UserId::fromString('cus_2'), $dto);
    }
}
