<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Hotel;
use App\Policies\HotelPolicy;
use App\Models\Journey;
use App\Policies\JourneyPolicy;
use App\Policies\FoodPolicy;
use App\Models\Food;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    protected $policies = [
    Journey::class  =>    JourneyPolicy::class,
    Food::class     =>    FoodPolicy::class,
    ];

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
