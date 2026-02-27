<?php

use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\VO\Address;
use App\Domain\Customer\VO\CustomerId;
use App\Domain\User\VO\UserId;
use PHPUnit\Framework\TestCase;

class CustomerTest extends TestCase
{
    public function testCustomerCreation()
    {
        $address = new Address(
            id: 'addr_1',
            street: 'Rua A',
            number: '123',
            city: 'Cidade',
            state: 'SP',
            zipcode: '12345-678',
            customerId: CustomerId::fromString('cus_1')
        );

        $customer = new Customer(
            id: CustomerId::fromString('cus_1'),
            name: 'João',
            email: 'joao@email.com',
            address: $address,
            userId: UserId::fromString('user_1')
        );

        $this->assertEquals('cus_1', $customer->id());
        $this->assertEquals('João', $customer->name());
        $this->assertEquals('joao@email.com', $customer->email());
        $this->assertSame($address, $customer->address());
    }
}
