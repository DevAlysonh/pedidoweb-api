<?php

namespace App\Application\UseCases\Customer;

use App\Application\Dto\Customer\UpdateCustomerAddressDto;
use App\Application\Shared\Traits\DiffTrait;
use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\Exceptions\CustomerNotFoundException;
use App\Domain\Customer\Exceptions\InvalidZipcodeException;
use App\Domain\User\Exceptions\UnauthorizedException;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\Customer\VO\Address;
use App\Domain\Customer\VO\CustomerId;
use App\Domain\Shared\Interfaces\LoggerInterface;
use App\Domain\User\VO\UserId;
use App\Infrastructure\Services\CepService;

class UpdateCustomerAddressUseCase
{
    use DiffTrait;

    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private LoggerInterface $logger,
        private CepService $cepService,
    ) {}

    public function execute(
        CustomerId $customerId,
        UserId $userId,
        UpdateCustomerAddressDto $dto
    ): Customer {
        $customer = $this->customerRepository->findById($customerId);

        if (!$customer) {
            throw new CustomerNotFoundException();
        }

        if (!$customer->userId()->equals($userId)) {
            $this->logger->warning('Tentativa de atualização de endereço do cliente por usuário não autorizado', [
                'customer_id' => $customerId,
                'user_id' => $userId,
            ]);

            throw new UnauthorizedException();
        }

        $old = $customer->snapshot();

        $newAddress = new Address(
            id: $customer->address()->id(),
            street: $dto->street ?? $customer->address()->street(),
            number: $dto->number ?? $customer->address()->number(),
            city: $dto->city ?? $customer->address()->city(),
            state: $dto->state ?? $customer->address()->state(),
            zipcode: $dto->zipcode ?? $customer->address()->zipcode(),
            customerId: $customerId->value()
        );

        $customer->changeAddress($newAddress);

        $this->validateAddress($customer);

        $this->customerRepository->save($customer);
        $new = $customer->snapshot();

        $this->logger->info('Os dados do cliente foram atualizados', [
            'customer_id' => $customerId,
            'user_id' => $userId,
            'changes' => $this->diff($old, $new),
        ]);

        return $customer;
    }

    private function validateAddress(Customer $customer): void
    {
        $cepData = $this->cepService->lookup($customer->address()->zipcode());

        if ($cepData === null) {
            throw new InvalidZipcodeException($customer->address()->zipcode());
        }

        if (
            strtolower($cepData->city) !== strtolower($customer->address()->city()) ||
            strtoupper($cepData->state) !== strtoupper($customer->address()->state())
        ) {
            throw new InvalidZipcodeException($customer->address()->zipcode());
        }
    }
}
