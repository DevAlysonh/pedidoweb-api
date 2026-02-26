<?php

namespace Tests\Unit\Infrastructure\Shared;

use App\Infrastructure\Shared\UuidGenerator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UuidGeneratorTest extends TestCase
{
    #[DataProvider('prefixProvider')]
    public function test_it_generates_valid_uuid_with_optional_prefix(
        string $prefix
    ): void {
        $generator = new UuidGenerator();

        $id = $generator->generate($prefix);

        if ($prefix) {
            $this->assertStringStartsWith($prefix, $id);
        }

        $uuid = substr($id, strlen($prefix));
        $this->assertMatchesRegularExpression(
            '/^[0-9a-fA-F-]{36}$/',
            $uuid
        );
    }

    public static function prefixProvider(): array
    {
        return [
            'sem prefixo' => [''],
            'prefixo simples' => ['CUS_'],
            'prefixo numerico' => ['123_'],
            'prefixo complexo' => ['ORDER_2026_'],
        ];
    }

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
