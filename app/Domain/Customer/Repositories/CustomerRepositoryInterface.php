<?php

namespace App\Domain\Customer\Repositories;

use App\Domain\Customer\Entities\Customer;

interface CustomerRepositoryInterface
{
    public function save(Customer $customer): void;
}