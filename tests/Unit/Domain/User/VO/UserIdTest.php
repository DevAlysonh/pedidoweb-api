<?php

use App\Domain\User\VO\UserId;
use PHPUnit\Framework\TestCase;

class UserIdTest extends TestCase
{
    public function testConstructorThrowsOnEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        new UserId('');
    }

    public function testFromStringCreatesInstance()
    {
        $userId = UserId::fromString('user_123');
        $this->assertInstanceOf(UserId::class, $userId);
        $this->assertEquals('user_123', $userId->value());
    }

    public function testEquals()
    {
        $id1 = new UserId('abc');
        $id2 = new UserId('abc');
        $id3 = new UserId('def');
        $this->assertTrue($id1->equals($id2));
        $this->assertFalse($id1->equals($id3));
    }

    public function testToString()
    {
        $id = new UserId('xyz');
        $this->assertEquals('xyz', (string)$id);
    }
}
