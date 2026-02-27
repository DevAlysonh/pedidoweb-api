<?php

use App\Domain\Customer\Exceptions\CustomerNotFoundException;
use PHPUnit\Framework\TestCase;

class CustomerNotFoundExceptionTest extends TestCase
{
    public function testExceptionMessage()
    {
        $exception = new CustomerNotFoundException();
        $this->assertStringContainsString('cliente', $exception->getMessage());
        $this->assertEquals(404, $exception->getCode());
    }
}
