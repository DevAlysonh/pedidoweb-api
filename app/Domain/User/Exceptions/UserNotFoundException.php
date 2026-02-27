<?php

namespace App\Domain\User\Exceptions;

class UserNotFoundException extends \DomainException
{
    public function __construct()
    {
        parent::__construct("Este usuário não existe ou não foi encontrado", 404);
    }
}
