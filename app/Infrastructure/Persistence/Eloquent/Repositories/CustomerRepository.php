<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Customer\Entities\Customer;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\Shared\Interfaces\LoggerInterface;
use App\Infrastructure\Persistence\Eloquent\Models\AddressModel;
use App\Infrastructure\Persistence\Eloquent\Models\CustomerModel;
use Illuminate\Support\Facades\DB;
use Throwable;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    public function save(Customer $customer): void
    {
        try {
            DB::transaction(function () use ($customer) {
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
            });
        } catch (Throwable $e) {
            $this->logger->error('Erro ao persistir cliente', [
                'customer_id' => $customer->id(),
                'error' => $e->getMessage(),
                'exception' => get_class($e),
            ]);
            throw $e;
        }
    }
}
