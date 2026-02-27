<?php

use App\Domain\Customer\VO\CustomerId;
use PHPUnit\Framework\TestCase;

class CustomerIdTest extends TestCase
{
    public function testConstructorThrowsOnEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        new CustomerId('');
    }

    public function testFromStringCreatesInstance()
    {
        $customerId = CustomerId::fromString('cus_123');
        $this->assertInstanceOf(CustomerId::class, $customerId);
        $this->assertEquals('cus_123', $customerId->value());
    }

    public function testEquals()
    {
        $id1 = new CustomerId('abc');
        $id2 = new CustomerId('abc');
        $id3 = new CustomerId('def');
        $this->assertTrue($id1->equals($id2));
        $this->assertFalse($id1->equals($id3));
    }

    public function testToString()
    {
        $id = new CustomerId('xyz');
        $this->assertEquals('xyz', (string)$id);
    }
}
