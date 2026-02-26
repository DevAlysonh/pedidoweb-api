<?php

namespace App\Infrastructure\Providers;

use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Repositories\CustomerRepository;
use Illuminate\Support\ServiceProvider;

final class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            CustomerRepositoryInterface::class,
            CustomerRepository::class
        );
    }

    public function boot(): void
    { }
}