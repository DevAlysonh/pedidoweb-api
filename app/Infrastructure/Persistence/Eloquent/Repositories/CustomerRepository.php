<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\AddressModel;
use App\Infrastructure\Persistence\Eloquent\Models\CustomerModel;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function save(Customer $customer): void
    {
        CustomerModel::create([
            'id' => $customer->id(),
            'name' => $customer->name(),
            'email' => $customer->email(),
        ]);

        AddressModel::create([
            'id' => $customer->address()->id(),
            'customer_id' => $customer->id(),
            'street' => $customer->address()->street(),
            'number' => $customer->address()->number(),
            'city' => $customer->address()->city(),
            'state' => $customer->address()->state(),
            'zipcode' => $customer->address()->zipcode(),
        ]);
    }
}
