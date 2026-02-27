<?php

namespace App\Infrastructure\Providers;

use App\Domain\Shared\Interfaces\IdGeneratorInterface;
use App\Domain\Shared\Interfaces\LoggerInterface;
use App\Domain\Shared\Interfaces\PasswordHasherInterface;
use App\Domain\Shared\Interfaces\TokenGeneratorInterface;
use App\Infrastructure\Services\CepService;
use App\Infrastructure\Services\ViaCepService;
use App\Infrastructure\Shared\JwtTokenGenerator;
use App\Infrastructure\Shared\LaravelLogger;
use App\Infrastructure\Shared\LaravelPasswordHasher;
use App\Infrastructure\Shared\UuidGenerator;
use Illuminate\Support\ServiceProvider;

final class InfrastructureServiceProvider extends ServiceProvider
{
    public function register()
    {
        // interfaces
        $this->app->bind(IdGeneratorInterface::class, UuidGenerator::class);
        $this->app->bind(LoggerInterface::class, LaravelLogger::class);
        $this->app->bind(PasswordHasherInterface::class, LaravelPasswordHasher::class);
        $this->app->bind(TokenGeneratorInterface::class, JwtTokenGenerator::class);
        // interfaces

        // services
        $this->app->bind(CepService::class, ViaCepService::class);
        // services
    }

    public function boot(): void
    { }
}