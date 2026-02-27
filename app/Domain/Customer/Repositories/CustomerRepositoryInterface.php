<?php

namespace App\Domain\Customer\Repositories;

use App\Domain\Customer\Entities\Customer;

interface CustomerRepositoryInterface
{
    public function save(Customer $customer): void;
    public function findAllByUser(string $userId): array;
    public function findById(string $customerId): ?Customer;
}