<?php
namespace App\Application\Dto\Customer;

class UpdateCustomerDto
{
    public function __construct(
        public ?string $name,
        public ?string $email
    ) { }

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['name'] ?? '',
            $data['email'] ?? ''
        );
    }
}
