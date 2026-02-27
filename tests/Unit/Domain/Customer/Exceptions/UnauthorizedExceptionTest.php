<?php

use App\Domain\Customer\Exceptions\UnauthorizedException;
use PHPUnit\Framework\TestCase;

class UnauthorizedExceptionTest extends TestCase
{
    public function testExceptionMessage()
    {
        $exception = new UnauthorizedException();
        $this->assertStringContainsString('Não autorizado', $exception->getMessage());
        $this->assertEquals(403, $exception->getCode());
    }
}
