<?php

namespace Tests\Unit\Application\UseCases\Customer;

use App\Application\UseCases\Customer\DeleteCustomerUseCase;
use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\Exceptions\CustomerNotFoundException;
use App\Domain\Customer\Exceptions\UnauthorizedException;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\Customer\VO\Address;
use App\Domain\Customer\VO\CustomerId;
use App\Domain\Shared\Interfaces\LoggerInterface;
use App\Domain\User\VO\UserId;
use PHPUnit\Framework\TestCase;

class DeleteCustomerUseCaseTest extends TestCase
{
    public function test_execute_deletes_customer_when_authorized()
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
        $repo->expects($this->once())->method('delete')->with($customer);

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->onlyMethods(['info', 'warning', 'debug', 'error'])
            ->getMock();

        $useCase = new DeleteCustomerUseCase($repo, $logger);
        $useCase->execute($customerId, $userId);
        $this->assertTrue(true);
    }

    public function test_deleteCustomerUseCase_should_throw_an_exception_when_customer_not_found()
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

        $useCase = new DeleteCustomerUseCase($repo, $logger);
        $this->expectException(CustomerNotFoundException::class);
        $useCase->execute($customerId, $userId);
    }

        public function test_deleteCustomerUseCase_should_throw_an_exception_when_user_is_not_owner()
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

        $useCase = new DeleteCustomerUseCase($repo, $logger);
        $this->expectException(UnauthorizedException::class);
        $useCase->execute($customerId, UserId::fromString('user_2'));
    }
}
