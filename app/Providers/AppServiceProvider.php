<?php

namespace App\Providers;

use App\Services\MobileMoneyGateway;
use App\Services\SimulatedMobileMoneyGateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MobileMoneyGateway::class, SimulatedMobileMoneyGateway::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
