<?php

namespace App\Providers;

use App\Infrastructure\Services\CepService;
use App\Infrastructure\Services\ViaCepService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind da interface CepService para a implementação ViaCepService
        $this->app->bind(CepService::class, ViaCepService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
