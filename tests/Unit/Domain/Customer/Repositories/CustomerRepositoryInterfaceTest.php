<?php

use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\Customer\VO\Address;
use PHPUnit\Framework\TestCase;

class CustomerRepositoryInterfaceTest extends TestCase
{
    public function testSaveCustomer()
    {
        $repository = $this->createMock(CustomerRepositoryInterface::class);
        $customer = new Customer(
            id: 'cus_1',
            name: 'João',
            email:'joao@email.com',
            address: new Address(
                id: 'addr_1',
                street: 'Rua A',
                number: '123',
                city: 'Cidade',
                state: 'SP',
                zipcode: '12345678'
            )
        );

        $repository->expects($this->once())
            ->method('save')
            ->with($customer);

        $repository->save($customer);
    }
}
