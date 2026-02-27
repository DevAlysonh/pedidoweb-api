<?php

namespace App\Domain\User\Exceptions;

class InvalidCredentialsException extends \DomainException
{
    public function __construct()
    {
        parent::__construct("Credenciais inválidas", 401);
    }
}
