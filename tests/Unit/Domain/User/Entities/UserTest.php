<?php

use App\Domain\User\Entities\User;
use App\Domain\User\VO\UserId;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserProperties()
    {
        $id = $this->createMock(UserId::class);
        $user = new User($id, 'Nome Teste', 'email@teste.com', 'senha123');

        $this->assertSame($id, $user->id());
        $this->assertEquals('Nome Teste', $user->name());
        $this->assertEquals('email@teste.com', $user->email());
        $this->assertEquals('senha123', $user->password());
    }
}
