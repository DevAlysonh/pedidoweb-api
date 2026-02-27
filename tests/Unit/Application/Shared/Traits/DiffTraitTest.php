<?php
namespace Tests\Unit\Application\Shared\Traits;

use PHPUnit\Framework\TestCase;
use App\Application\Shared\Traits\DiffTrait;

class DiffTraitTest extends TestCase
{
    use DiffTrait;

    public function test_diff_returns_changes_between_arrays()
    {
        $old = [
            'name' => 'João',
            'email' => 'joao@email.com',
            'address' => [
                'city' => 'São Paulo',
                'state' => 'SP'
            ]
        ];
        $new = [
            'name' => 'Maria',
            'email' => 'joao@email.com',
            'address' => [
                'city' => 'Rio de Janeiro',
                'state' => 'RJ'
            ]
        ];
        $changes = $this->diff($old, $new);
        $this->assertArrayHasKey('name', $changes);
        $this->assertEquals(['old' => 'João', 'new' => 'Maria'], $changes['name']);
        $this->assertArrayHasKey('address.city', $changes);
        $this->assertEquals(['old' => 'São Paulo', 'new' => 'Rio de Janeiro'], $changes['address.city']);
        $this->assertArrayHasKey('address.state', $changes);
        $this->assertEquals(['old' => 'SP', 'new' => 'RJ'], $changes['address.state']);
    }

    public function test_diff_returns_empty_when_no_changes()
    {
        $old = ['name' => 'João', 'email' => 'joao@email.com'];
        $new = ['name' => 'João', 'email' => 'joao@email.com'];
        $changes = $this->diff($old, $new);
        $this->assertEmpty($changes);
    }

    public function test_diff_detects_new_keys()
    {
        $old = ['name' => 'João'];
        $new = ['name' => 'João', 'phone' => '123456'];
        $changes = $this->diff($old, $new);
        $this->assertArrayHasKey('phone', $changes);
        $this->assertEquals(['old' => null, 'new' => '123456'], $changes['phone']);
    }
}
