<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'message' => 'Cliente criado com sucesso',
            'customer' => [
                'id' => $this->id(),
                'name' => $this->name(),
                'email' => $this->email(),
                'address' => [
                    'street' => $this->address()->street(),
                    'number' => $this->address()->number(),
                    'city' => $this->address()->city(),
                    'state' => $this->address()->state(),
                    'zipcode' => $this->address()->zipcode(),
                ],
            ],
        ];
    }
}
