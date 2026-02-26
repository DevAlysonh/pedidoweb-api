<?php

namespace App\Infrastructure\Providers;

use App\Domain\Shared\Interfaces\IdGenerator;
use App\Infrastructure\Services\CepService;
use App\Infrastructure\Services\ViaCepService;
use App\Infrastructure\Shared\UuidGenerator;
use Illuminate\Support\ServiceProvider;

final class InfrastructureServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(IdGenerator::class, UuidGenerator::class);
        $this->app->bind(CepService::class, ViaCepService::class);
    }

    public function boot(): void
    { }
}