<?php
namespace App\Application\Dto\Customer;

class UpdateCustomerAddressDto
{
    public function __construct(
        public ?string $street,
        public ?string $number,
        public ?string $city,
        public ?string $state,
        public ?string $zipcode
    ) { }

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['street'] ?? '',
            $data['number'] ?? '',
            $data['city'] ?? '',
            $data['state'] ?? '',
            $data['zipcode'] ?? ''
        );
    }
}
