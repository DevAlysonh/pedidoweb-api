<?php

use App\Domain\Customer\Exceptions\InvalidZipcodeException;
use PHPUnit\Framework\TestCase;

class InvalidZipcodeExceptionTest extends TestCase
{
    public function testExceptionMessage()
    {
        $exception = new InvalidZipcodeException('00000-000');
        $this->assertStringContainsString('00000-000', $exception->getMessage());
    }
}
