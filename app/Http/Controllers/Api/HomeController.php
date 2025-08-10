<?php

namespace App\Http\Controllers\Api;
use App\Models\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
class HomeController extends Controller
{
     public function __construct()
    {
        App::setLocale(app()->getLocale());
    }
    public function getRestaurants()
    {

    $restaurants = Restaurant::with([
        'city',
        'images',
        'foods',
        'feedbacks',
    ])->get();

    $restaurants->each(function ($restaurant) {
        $restaurant->makeHidden(['translations', 'created_at', 'updated_at']);
        if ($restaurant->city) {
            $restaurant->city->makeHidden(['created_at', 'updated_at']);
        }
        
        $restaurant->images->each->makeHidden(['created_at', 'updated_at']);
        $restaurant->foods->each->makeHidden(['created_at', 'updated_at']);
        $restaurant->feedbacks->each->makeHidden(['created_at', 'updated_at']);
    });

      return $this->jsonResponse(true,"Get Restaurants",200,$restaurants);
    }
    public function getRestaurant($id)
    {
    $restaurant = Restaurant::with([
        'city',
        'images',
        'foods',
        'feedbacks',
    ])->findOrFail($id);
    $restaurant->makeHidden(['translations', 'created_at', 'updated_at']);
    $restaurant->city->makeHidden(['created_at', 'updated_at']);
    $restaurant->images->each->makeHidden(['created_at', 'updated_at']);
    $restaurant->foods->each->makeHidden(['created_at', 'updated_at']);
    $restaurant->feedbacks->each->makeHidden(['created_at', 'updated_at']);

    $response = [
        'data' => $restaurant,
        'average_rating' => $restaurant->average_rating,
    ];
      return $this->jsonResponse(true,"Get Restaurant",200,$response);
    }

}
