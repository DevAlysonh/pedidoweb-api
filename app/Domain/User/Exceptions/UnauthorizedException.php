<?php

namespace App\Domain\User\Exceptions;

class UnauthorizedException extends \DomainException
{
    public function __construct()
    {
        parent::__construct("Não autorizado", 403);
    }
}
