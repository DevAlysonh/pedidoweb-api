<?php

use App\Infrastructure\Persistence\Eloquent\Repositories\CustomerRepository;
use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\VO\Address;
use App\Domain\Customer\VO\CustomerId;
use App\Domain\Shared\Interfaces\LoggerInterface;
use App\Domain\User\VO\UserId;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\TestCase;

class CustomerRepositoryTest extends TestCase
{
    public function testSaveCallsLoggerOnError()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('error')->with(
            $this->stringContains('Erro ao persistir cliente'),
            $this->arrayHasKey('customer_id')
        );

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

        DB::shouldReceive('transaction')->andThrow(new \Exception('fail'));

        $repo = new CustomerRepository($logger);

        $this->expectException(\Exception::class);
        $repo->save($customer);
    }
}
