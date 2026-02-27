<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id()->value(),
            'name' => $this->name(),
            'email' => $this->email(),
            'user_id' => $this->userId()->value(),
            'address' => [
                'address_id' => $this->address()->id(),
                'street' => $this->address()->street(),
                'number' => $this->address()->number(),
                'city' => $this->address()->city(),
                'state' => $this->address()->state(),
                'zipcode' => $this->address()->zipcode(),
                'customer_id' => $this->address()->customerId(),
            ],
        ];
    }
}
