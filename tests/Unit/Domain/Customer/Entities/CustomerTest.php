<?php

use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\VO\Address;
use PHPUnit\Framework\TestCase;

class CustomerTest extends TestCase
{
    public function testCustomerCreation()
    {
        $address = new Address('addr_1', 'Rua A', '123', 'Cidade', 'SP', '12345-678');
        $customer = new Customer('cus_1', 'João', 'joao@email.com', $address);

        $this->assertEquals('cus_1', $customer->id());
        $this->assertEquals('João', $customer->name());
        $this->assertEquals('joao@email.com', $customer->email());
        $this->assertSame($address, $customer->address());
    }
}
