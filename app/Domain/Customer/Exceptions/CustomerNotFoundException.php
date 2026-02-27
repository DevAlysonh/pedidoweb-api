<?php

namespace App\Domain\Customer\Exceptions;

class CustomerNotFoundException extends \DomainException
{
    public function __construct()
    {
        parent::__construct("Este cliente não existe ou não foi encontrado", 404);
    }
}