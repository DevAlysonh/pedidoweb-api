<?php

namespace App\Domain\User\Entities;

use App\Domain\User\VO\UserId;

final class User
{
    public function __construct(
        private UserId $id,
        private string $name,
        private string $email,
        private string $password
    ) {}

    public function id(): UserId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }
}
