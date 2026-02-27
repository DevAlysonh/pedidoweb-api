<?php

namespace App\Domain\Customer\Repositories;

use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\VO\CustomerId;

interface CustomerRepositoryInterface
{
    public function save(Customer $customer): void;
    public function findAllByUser(string $userId): array;
    public function findById(CustomerId $customerId): ?Customer;
    public function update(Customer $customer): void;
    public function delete(string $customerId): void;
}