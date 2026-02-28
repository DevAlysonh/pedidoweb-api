<?php

namespace Tests\Unit\Infrastructure\Shared;

use App\Infrastructure\Shared\UuidGenerator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UuidGeneratorTest extends TestCase
{
    public function test_it_generates_unique_values(): void
    {
        $generator = new UuidGenerator();

        $first = $generator->generate();
        $second = $generator->generate();

        $this->assertNotEquals($first, $second);
    }

    public function test_it_generates_valid_uuid_format_v4(): void
    {
        $generator = new UuidGenerator();

        $id = $generator->generate();

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $id
        );
    }
}
