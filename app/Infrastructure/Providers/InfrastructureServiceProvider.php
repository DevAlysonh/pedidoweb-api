<?php

namespace App\Infrastructure\Providers;

use App\Domain\Shared\Interfaces\IdGenerator;
use App\Domain\Shared\Interfaces\LoggerInterface;
use App\Infrastructure\Services\CepService;
use App\Infrastructure\Services\ViaCepService;
use App\Infrastructure\Shared\LaravelLogger;
use App\Infrastructure\Shared\UuidGenerator;
use Illuminate\Support\ServiceProvider;

final class InfrastructureServiceProvider extends ServiceProvider
{
    public function register()
    {
        // interfaces
        $this->app->bind(IdGenerator::class, UuidGenerator::class);
        $this->app->bind(LoggerInterface::class, LaravelLogger::class);
        // interfaces

        // services
        $this->app->bind(CepService::class, ViaCepService::class);
        // services
    }

    public function boot(): void
    { }
}