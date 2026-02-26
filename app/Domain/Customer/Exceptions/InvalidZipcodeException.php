<?php

declare(strict_types=1);

namespace App\Domain\Customer\Exceptions;

class InvalidZipcodeException extends \DomainException
{
    public function __construct(string $zipcode)
    {
        parent::__construct("CEP inválido ou incorreto para este endereço: {$zipcode}", 422);
    }
}
