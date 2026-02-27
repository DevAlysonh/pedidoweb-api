<?php

namespace App\Domain\Customer\Exceptions;

class UnauthorizedException extends \DomainException
{
    public function __construct()
    {
        parent::__construct("Não autorizado", 403);
    }
}