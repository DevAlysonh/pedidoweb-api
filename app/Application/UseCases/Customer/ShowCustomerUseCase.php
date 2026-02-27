<?php

namespace App\Application\UseCases\Customer;

use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\Customer\Entities\Customer;

class ShowCustomerUseCase
{
    public function __construct(private CustomerRepositoryInterface $repository) {}

    public function execute(string $customerId): ?Customer
    {
        return $this->repository->findById($customerId);
    }
}
