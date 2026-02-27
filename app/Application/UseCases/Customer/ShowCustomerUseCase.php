<?php

namespace App\Application\UseCases\Customer;

use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\Exceptions\CustomerNotFoundException;
use App\Domain\Customer\VO\CustomerId;
use App\Domain\User\Exceptions\UnauthorizedException;
use App\Domain\User\VO\UserId;

class ShowCustomerUseCase
{
    public function __construct(private CustomerRepositoryInterface $repository) {}

    public function execute(CustomerId $customerId, UserId $userId): Customer
    {
        $customer = $this->repository->findById($customerId);

        if (!$customer) {
            throw new CustomerNotFoundException();
        }

        if (!$customer->userId()->equals($userId)) {
            throw new UnauthorizedException();
        }

        return $customer;
    }
}
