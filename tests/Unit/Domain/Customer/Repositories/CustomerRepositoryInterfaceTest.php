<?php

use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\Customer\VO\Address;
use App\Domain\Customer\VO\CustomerId;
use App\Domain\User\VO\UserId;
use PHPUnit\Framework\TestCase;

class CustomerRepositoryInterfaceTest extends TestCase
{
    public function testSaveCustomer()
    {
        $repository = $this->createMock(CustomerRepositoryInterface::class);
        $customer = new Customer(
            id: CustomerId::fromString('xpto1'),
            name: 'João',
            email:'joao@email.com',
            userId: UserId::fromString('user_1'),
            address: new Address(
                id: 'xpto1',
                street: 'Rua A',
                number: '123',
                city: 'Cidade',
                state: 'SP',
                zipcode: '12345678',
                customerId: 'xpto1'
            )
        );

        $repository->expects($this->once())
            ->method('save')
            ->with($customer);

        $repository->save($customer);
    }
}
