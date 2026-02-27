<?php

use App\Application\UseCases\Customer\ShowCustomerUseCase;
use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\Customer\VO\CustomerId;
use App\Domain\User\VO\UserId;
use App\Domain\Customer\Exceptions\CustomerNotFoundException;
use App\Domain\Customer\Exceptions\UnauthorizedException;
use App\Domain\Customer\VO\Address;
use PHPUnit\Framework\TestCase;

class ShowCustomerUseCaseTest extends TestCase
{
    public function testExecuteReturnsCustomer()
    {
        $customerId = new CustomerId('cus_1');
        $userId = new UserId('user_1');
        $address = new Address(
            id: 'addr_1',
            street: 'Rua A',
            number: '123',
            city: 'Cidade',
            state: 'Estado',
            zipcode: '12345-678',
            customerId: $customerId
        );

        $customer = new Customer(
            id: $customerId,
            userId: $userId,
            name: 'João',
            email: 'joao@email.com',
            address: $address
        );

        $repository = $this->getMockBuilder(CustomerRepositoryInterface::class)
            ->onlyMethods(['findById', 'save', 'findAllByUser', 'delete'])
            ->getMock();
        $repository->expects($this->once())
            ->method('findById')
            ->with($customerId)
            ->willReturn($customer);

        $useCase = new ShowCustomerUseCase($repository);
        $result = $useCase->execute($customerId, $userId);

        $this->assertSame($customer, $result);
    }

    public function testExecuteThrowsCustomerNotFoundException()
    {
        $customerId = new CustomerId('cus_1');
        $userId = new UserId('user_1');
        $repository = $this->createMock(CustomerRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('findById')
            ->with($customerId)
            ->willReturn(null);

        $useCase = new ShowCustomerUseCase($repository);

        $this->expectException(CustomerNotFoundException::class);
        $useCase->execute($customerId, $userId);
    }

    public function testExecuteThrowsUnauthorizedException()
    {
        $customerId = new CustomerId('cus_1');
        $userId = new UserId('user_1');
        $otherUserId = new UserId('user_2');
        $address = new Address(
            id: 'addr_1',
            street: 'Rua A',
            number: '123',
            city: 'Cidade',
            state: 'Estado',
            zipcode: '12345-678',
            customerId: $customerId
        );

        $customer = new Customer(
            id: $customerId,
            userId: $userId,
            name: 'João',
            email: 'joao@email.com',
            address: $address
        );

        $repository = $this->createMock(CustomerRepositoryInterface::class);
        $repository->method('findById')->with($customerId)->willReturn($customer);

        $useCase = new ShowCustomerUseCase($repository);

        $this->expectException(UnauthorizedException::class);
        $useCase->execute($customerId, $otherUserId);
    }
}
