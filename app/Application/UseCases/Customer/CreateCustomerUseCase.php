<?php

namespace App\Application\UseCases\Customer;

use App\Application\Dto\Customer\CreateCustomerDTO;
use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\Exceptions\InvalidZipcodeException;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\Customer\VO\Address;
use App\Domain\Shared\Interfaces\IdGenerator;
use App\Domain\Shared\Interfaces\LoggerInterface;
use App\Infrastructure\Services\CepService;

class CreateCustomerUseCase
{
    public function __construct(
        private CustomerRepositoryInterface $repository,
        private IdGenerator $idGenerator,
        private CepService $cepService,
        private LoggerInterface $logger
    ) {}

    public function execute(CreateCustomerDTO $dto): Customer
    {
        $this->validateAddress($dto);

        $customerId = $this->idGenerator->generate(Customer::PREFIX);
        $addressId  = $this->idGenerator->generate(Address::PREFIX);

        $address = new Address(
            id: $addressId,
            street: $dto->street,
            number: $dto->number,
            city: $dto->city,
            state: $dto->state,
            zipcode: $dto->zipcode,
        );

        $customer = new Customer(
            id: $customerId,
            name: $dto->name,
            email: $dto->email,
            address: $address
        );

        $this->repository->save($customer);

        $this->logger->info('Novo cliente cadastrado', [
            'customer_id' => $customerId,
            'email' => $dto->email,
        ]);

        return $customer;
    }

    private function validateAddress(CreateCustomerDTO $dto): void
    {
        $cepData = $this->cepService->lookup($dto->zipcode);

        if ($cepData === null) {
            throw new InvalidZipcodeException($dto->zipcode);
        }

        if (
            strtolower($cepData->city) !== strtolower($dto->city) ||
            strtoupper($cepData->state) !== strtoupper($dto->state)
        ) {
            throw new InvalidZipcodeException($dto->zipcode);
        }
    }
}
