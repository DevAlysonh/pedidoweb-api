<?php

namespace App\Infrastructure\Providers;

use App\Domain\Shared\Interfaces\IdGenerator;
use App\Infrastructure\Shared\UuidGenerator;
use Illuminate\Support\ServiceProvider;

final class InfrastructureServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            IdGenerator::class,
            UuidGenerator::class
        );
    }

    public function boot(): void
    { }
}