<?php

namespace App\Domain\User\Exceptions;

class UserAlreadyExistsException extends \DomainException
{
    public function __construct()
    {
        parent::__construct("Este usuário já existe", 409);
    }
}
