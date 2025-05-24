<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Hotel;
use App\Policies\HotelPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */


    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
