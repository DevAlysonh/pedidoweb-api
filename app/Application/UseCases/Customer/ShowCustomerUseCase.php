<?php

namespace App\Application\UseCases\Customer;

use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\Exceptions\CustomerNotFoundException;
use App\Domain\Customer\Exceptions\UnauthorizedException;

class ShowCustomerUseCase
{
    public function __construct(private CustomerRepositoryInterface $repository) {}

    public function execute(string $customerId, string $userId): Customer
    {
        $customer = $this->repository->findById($customerId);

        if (!$customer) {
            throw new CustomerNotFoundException();
        }

        if ($customer->userId() !== $userId) {
            throw new UnauthorizedException();
        }

        return $customer;
    }
}
