<?php

namespace App\Providers;

use App\Infrastructure\Services\CepService;
use App\Infrastructure\Services\ViaCepService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CepService::class, ViaCepService::class);
    }

    public function boot(): void
    {
        //
    }
}
