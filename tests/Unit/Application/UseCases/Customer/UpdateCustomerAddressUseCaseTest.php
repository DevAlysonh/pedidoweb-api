<?php

namespace Tests\Unit\Application\UseCases\Customer;

use App\Application\Dto\CepData;
use App\Application\UseCases\Customer\UpdateCustomerAddressUseCase;
use App\Application\Dto\Customer\UpdateCustomerAddressDto;
use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\Exceptions\CustomerNotFoundException;
use App\Domain\Customer\Exceptions\InvalidZipcodeException;
use App\Domain\Customer\Exceptions\UnauthorizedException;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\Customer\VO\Address;
use App\Domain\Customer\VO\CustomerId;
use App\Domain\Shared\Interfaces\LoggerInterface;
use App\Domain\User\VO\UserId;
use App\Infrastructure\Services\CepService;
use PHPUnit\Framework\TestCase;

class UpdateCustomerAddressUseCaseTest extends TestCase
{
    private CustomerRepositoryInterface $repository;
    private LoggerInterface $logger;
    private CepService $cepService;
    private UpdateCustomerAddressUseCase $useCase;

    protected function setUp(): void
    {
        $this->repository = $this->getMockBuilder(CustomerRepositoryInterface::class)
            ->onlyMethods(['findById', 'save', 'findAllByUser', 'delete'])
            ->getMock();

        $this->logger = $this->getMockBuilder(LoggerInterface::class)
            ->onlyMethods(['info', 'warning', 'debug', 'error'])
            ->getMock();

        $this->cepService = $this->createMock(CepService::class);

        $this->useCase = new UpdateCustomerAddressUseCase($this->repository, $this->logger, $this->cepService);
    }

    public function test_execute_updates_customer_address_successfully()
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
                city: 'São Paulo',
                state: 'SP',
                zipcode: '01310100',
                customerId: $customerId->value()
            )
        );

        $this->repository->method('findById')->willReturn($customer);
        $this->repository->expects($this->once())->method('save')->with($customer);

        $cepData = new CepData(
            zipcode: '01310100',
            street: 'Avenida Paulista',
            number: '1000',
            city: 'São Paulo',
            state: 'SP'
        );

        $this->cepService->method('lookup')->willReturn($cepData);
        $this->logger->expects($this->once())->method('info');

        $dto = new UpdateCustomerAddressDto(
            street: 'Avenida Paulista',
            number: '1000',
            city: 'São Paulo',
            state: 'SP',
            zipcode: '01310100'
        );

        $result = $this->useCase->execute($customerId, $userId, $dto);

        $this->assertSame($customer, $result);
        $this->assertEquals('Avenida Paulista', $customer->address()->street());
        $this->assertEquals('1000', $customer->address()->number());
    }

    public function test_execute_updates_only_street_when_other_fields_are_null()
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
                city: 'São Paulo',
                state: 'SP',
                zipcode: '01310100',
                customerId: $customerId->value()
            )
        );

        $this->repository->method('findById')->willReturn($customer);
        $this->repository->expects($this->once())->method('save')->with($customer);

        $cepData = new CepData(
            zipcode: '01310100',
            street: 'Avenida Paulista',
            number: '123',
            city: 'São Paulo',
            state: 'SP'
        );

        $this->cepService->method('lookup')->willReturn($cepData);
        $this->logger->expects($this->once())->method('info');

        $dto = new UpdateCustomerAddressDto(
            street: 'Avenida Paulista',
            number: null,
            city: null,
            state: null,
            zipcode: null
        );

        $result = $this->useCase->execute($customerId, $userId, $dto);

        $this->assertEquals('Avenida Paulista', $customer->address()->street());
        $this->assertEquals('123', $customer->address()->number());
        $this->assertEquals('São Paulo', $customer->address()->city());
    }

    public function test_execute_throws_exception_when_customer_not_found()
    {
        $customerId = CustomerId::fromString('cus_1');
        $userId = UserId::fromString('user_1');

        $this->repository->method('findById')->willReturn(null);

        $dto = new UpdateCustomerAddressDto(
            street: 'Rua Nova',
            number: '456',
            city: 'Rio de Janeiro',
            state: 'RJ',
            zipcode: '20040020'
        );

        $this->expectException(CustomerNotFoundException::class);
        $this->useCase->execute($customerId, $userId, $dto);
    }

    public function test_execute_throws_exception_when_user_not_authorized()
    {
        $customerId = CustomerId::fromString('cus_1');
        $ownerUserId = UserId::fromString('user_1');
        $differentUserId = UserId::fromString('user_2');

        $customer = new Customer(
            id: $customerId,
            name: 'João',
            email: 'joao@email.com',
            userId: $ownerUserId,
            address: new Address(
                id: 'addr_1',
                street: 'Rua A',
                number: '123',
                city: 'São Paulo',
                state: 'SP',
                zipcode: '01310100',
                customerId: $customerId->value()
            )
        );

        $this->repository->method('findById')->willReturn($customer);
        $this->logger->expects($this->once())->method('warning');

        $dto = new UpdateCustomerAddressDto(
            street: 'Rua Nova',
            number: '456',
            city: 'Rio de Janeiro',
            state: 'RJ',
            zipcode: '20040020'
        );

        $this->expectException(UnauthorizedException::class);
        $this->useCase->execute($customerId, $differentUserId, $dto);
    }

    public function test_execute_throws_exception_when_zipcode_not_found()
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
                city: 'São Paulo',
                state: 'SP',
                zipcode: '01310100',
                customerId: $customerId->value()
            )
        );

        $this->repository->method('findById')->willReturn($customer);
        $this->cepService->method('lookup')->willReturn(null);

        $dto = new UpdateCustomerAddressDto(
            street: 'Rua Nova',
            number: '456',
            city: 'Rio de Janeiro',
            state: 'RJ',
            zipcode: '00000000'
        );

        $this->expectException(InvalidZipcodeException::class);
        $this->useCase->execute($customerId, $userId, $dto);
    }

    public function test_execute_throws_exception_when_city_does_not_match_zipcode()
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
                city: 'São Paulo',
                state: 'SP',
                zipcode: '01310100',
                customerId: $customerId->value()
            )
        );

        $this->repository->method('findById')->willReturn($customer);

        $cepData = new CepData(
            zipcode: '01310100',
            street: 'Avenida Paulista',
            number: '1000',
            city: 'São Paulo',
            state: 'SP'
        );

        $this->cepService->method('lookup')->willReturn($cepData);

        $dto = new UpdateCustomerAddressDto(
            street: 'Avenida Paulista',
            number: '1000',
            city: 'Rio de Janeiro',
            state: 'SP',
            zipcode: '01310100'
        );

        $this->expectException(InvalidZipcodeException::class);
        $this->useCase->execute($customerId, $userId, $dto);
    }

    public function test_execute_throws_exception_when_state_does_not_match_zipcode()
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
                city: 'São Paulo',
                state: 'SP',
                zipcode: '01310100',
                customerId: $customerId->value()
            )
        );

        $this->repository->method('findById')->willReturn($customer);

        $cepData = new CepData(
            zipcode: '01310100',
            street: 'Avenida Paulista',
            number: '1000',
            city: 'São Paulo',
            state: 'SP'
        );

        $this->cepService->method('lookup')->willReturn($cepData);

        $dto = new UpdateCustomerAddressDto(
            street: 'Avenida Paulista',
            number: '1000',
            city: 'São Paulo',
            state: 'RJ',
            zipcode: '01310100'
        );

        $this->expectException(InvalidZipcodeException::class);
        $this->useCase->execute($customerId, $userId, $dto);
    }

    public function test_execute_city_and_state_validation_is_case_insensitive()
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
                city: 'São Paulo',
                state: 'SP',
                zipcode: '01310100',
                customerId: $customerId->value()
            )
        );

        $this->repository->method('findById')->willReturn($customer);
        $this->repository->expects($this->once())->method('save')->with($customer);

        $cepData = new CepData(
            zipcode: '01310100',
            street: 'Avenida Paulista',
            number: '1000',
            city: 'São Paulo',
            state: 'sp'
        );

        $this->cepService->method('lookup')->willReturn($cepData);
        $this->logger->expects($this->once())->method('info');

        $dto = new UpdateCustomerAddressDto(
            street: 'Avenida Paulista',
            number: '1000',
            city: 'são paulo',
            state: 'SP',
            zipcode: '01310100'
        );

        $result = $this->useCase->execute($customerId, $userId, $dto);

        $this->assertSame($customer, $result);
    }

    public function test_execute_logs_information_with_diff_when_address_updated()
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
                city: 'São Paulo',
                state: 'SP',
                zipcode: '01310100',
                customerId: $customerId->value()
            )
        );

        $this->repository->method('findById')->willReturn($customer);
        $this->repository->expects($this->once())->method('save');

        $cepData = new CepData(
            zipcode: '01310100',
            street: 'Avenida Paulista',
            number: '1000',
            city: 'São Paulo',
            state: 'SP'
        );

        $this->cepService->method('lookup')->willReturn($cepData);
        $this->logger->expects($this->once())->method('info')->with(
            'Os dados do cliente foram atualizados',
            $this->callback(function ($context) {
                return isset($context['customer_id']) &&
                       isset($context['user_id']) &&
                       isset($context['changes']) &&
                       is_array($context['changes']);
            })
        );

        $dto = new UpdateCustomerAddressDto(
            street: 'Avenida Paulista',
            number: '1000',
            city: 'São Paulo',
            state: 'SP',
            zipcode: '01310100'
        );

        $this->useCase->execute($customerId, $userId, $dto);
    }

    public function test_execute_returns_customer_with_updated_zipcode()
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
                city: 'São Paulo',
                state: 'SP',
                zipcode: '01310100',
                customerId: $customerId->value()
            )
        );

        $this->repository->method('findById')->willReturn($customer);
        $this->repository->expects($this->once())->method('save');

        $newZipcode = '20040020';
        $cepData = new CepData(
            zipcode: $newZipcode,
            street: 'Avenida Rio Branco',
            number: '500',
            city: 'Rio de Janeiro',
            state: 'RJ'
        );

        $this->cepService->method('lookup')->willReturn($cepData);
        $this->logger->expects($this->once())->method('info');

        $dto = new UpdateCustomerAddressDto(
            street: 'Avenida Rio Branco',
            number: '500',
            city: 'Rio de Janeiro',
            state: 'RJ',
            zipcode: $newZipcode
        );

        $result = $this->useCase->execute($customerId, $userId, $dto);

        $this->assertEquals($newZipcode, $result->address()->zipcode());
        $this->assertEquals('Rio de Janeiro', $result->address()->city());
        $this->assertEquals('RJ', $result->address()->state());
    }

    public function test_execute_preserves_address_id_when_updating()
    {
        $customerId = CustomerId::fromString('cus_1');
        $userId = UserId::fromString('user_1');
        $originalAddressId = 'addr_1';

        $customer = new Customer(
            id: $customerId,
            name: 'João',
            email: 'joao@email.com',
            userId: $userId,
            address: new Address(
                id: $originalAddressId,
                street: 'Rua A',
                number: '123',
                city: 'São Paulo',
                state: 'SP',
                zipcode: '01310100',
                customerId: $customerId->value()
            )
        );

        $this->repository->method('findById')->willReturn($customer);
        $this->repository->expects($this->once())->method('save');

        $cepData = new CepData(
            zipcode: '01310100',
            street: 'Avenida Paulista',
            number: '1000',
            city: 'São Paulo',
            state: 'SP'
        );

        $this->cepService->method('lookup')->willReturn($cepData);
        $this->logger->expects($this->once())->method('info');

        $dto = new UpdateCustomerAddressDto(
            street: 'Avenida Paulista',
            number: '1000',
            city: 'São Paulo',
            state: 'SP',
            zipcode: '01310100'
        );

        $result = $this->useCase->execute($customerId, $userId, $dto);

        $this->assertEquals($originalAddressId, $result->address()->id());
    }
}
