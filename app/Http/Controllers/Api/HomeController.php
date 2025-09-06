<?php

namespace App\Http\Controllers\Api;
use App\Models\Restaurant;
use App\Models\Environment;
use App\Models\Journey;
use App\Models\Hotel;
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
        ])
        ->withExists(['favorites as is_favorite' => function ($query) {
            $query->where('user_id', auth()->id());
        }])
        ->get();

    $restaurants->each(function ($restaurant) {
        $restaurant->makeHidden(['translations', 'created_at', 'updated_at']);

        if ($restaurant->city) {
            $restaurant->city->makeHidden(['created_at', 'updated_at']);
        }

        $restaurant->images->each->makeHidden(['created_at', 'updated_at']);
        $restaurant->foods->each->makeHidden(['created_at', 'updated_at']);
        $restaurant->feedbacks->each->makeHidden(['created_at', 'updated_at']);
    });

    return $this->jsonResponse(true, "Get Restaurants", 200, $restaurants);
}

public function getRestaurant($id)
{
    $restaurant = Restaurant::with([
        'city',
        'images',
        'foods',
        'feedbacks.user',
        'properties',
    ])->findOrFail($id);

    // Group foods by category column
 $foodsGrouped = $restaurant->foods
    ->groupBy('category_id')
    ->map(function ($foods, $categoryId) {
        // Get category name from the first food in the group
        $categoryName = $foods->first()->category->name ?? 'Uncategorized';

        return [
            'category' => $categoryName,
            'items' => $foods->map(function ($food) {
                return [
                    'id'    => $food->id,
                    'name'  => $food->name,
                    'price' => $food->price,
                    'image' => $food->images ?? null,
                ];
            })->values()
        ];
    })
    ->values();

  

    $response = [
        'restaurant' => [
            'id'         => $restaurant->id,
            'latitude'   => $restaurant->latitude,
            'longitude'  => $restaurant->longitude,
            'address'    => $restaurant->address,
            'city'       => $restaurant->city,
            'images'     => $restaurant->images,
            'feedbacks'  => $restaurant->feedbacks,
            'properties' => $restaurant->properties,
            'foods'      => $foodsGrouped,
            'is_favorite' => $restaurant->is_favorite, // 👈 add favorite flag here
        ],
        'average_rating' => $restaurant->average_rating,
    ];

    return $this->jsonResponse(true, "Get Restaurant", 200, $response);
}



   
    public function getEnvironments($type){
        $query = Environment::with([
            'images',
            'city',
            'feedbacks',           
            'favorites'
        ]);

        if ($type) {
            $environments = $query
                ->where('type', $type)
                ->get()
                ->map(function ($env) {
                    return $this->formatEnvironment($env);
                });
          
            $data=[
                'type' => $type,
                'data' => $environments
            ];
        return $this->jsonResponse(true,"Get {$type}",200,$data);
        }      
    }
        public function getEnvironment($id)
    {
        $environment = Environment::with([
            'images',
            'city',
            'feedbacks',
            'favorites'
        ])->findOrFail($id);

      return $this->jsonResponse(true,"Get Data",200,$this->formatEnvironment($environment));
  
    }
    private function formatEnvironment($env)
    {
        return [
            'id' => $env->id,
            'name' => $env->name,
            'description' => $env->description,
            'phone' => $env->phone,
            'latitude' => $env->latitude,
            'longitude' => $env->longitude,
            'type' => $env->type,
            'city' => $env->city,
            'images' => $env->images,
            'feedbacks' => $env->feedbacks,
            'average_rating' => $env->average_rating,
            'favorites' => $env->favorites,
            'is_favorite' => $env->is_favorite, 
        ];
    }
  public function getJourney()
{
    $journeys = Journey::with([
        'tourist',
        'images',
        'locations',
        'favorites',
        'users',
        'feedbacks'
    ])
      ->withExists(['favorites as is_favorite' => function ($query) {
            $query->where('user_id', auth()->id());
        }])->get()
      ->map(function ($journey) {
          return [
              'id' => $journey->id,
              'name' => $journey->name,
              'description' => $journey->description,
              'destination' => $journey->destination,
              'duration' => $journey->duration,
              'price' => $journey->price,
              'feedback' => $journey->feedbacks,
              'images' => $journey->images->map(function ($image){
                return [
                    'path'=>$image->path
                ];
              }),
              'locations' => $journey->locations->map(function ($location) {
                  return [
                      'latitude' => $location->latitude,
                      'longitude' => $location->longitude,
                  ];
              }),
              'favorites_count' => $journey->favorites->count(),
              'users_count' => $journey->users->count(),
              'is_favorite' => $journey->is_favorite, // 👈 add favorite flag here
          ];
      });

    return $this->jsonResponse(true, "Get Data", 200, $journeys);
}
       public function getJourneyById($id){
        $journey = Journey::with([
        'images',
        'locations',
        'favorites',
        'users',
        'feedbacks'
         ])->findOrFail($id);
      
          $data= [
              'id' => $journey->id,
              'name' => $journey->name,
              'description' => $journey->description,
              'destination' => $journey->destination,
              'duration' => $journey->duration,
              'price' => $journey->price,
              'feedback'=>$journey->feedbacks,
              'images' => $journey->images->map(function ($image){
                return [
                    'path'=>$image->path
                ];
              }),
              'locations' => $journey->locations->map(function ($location) {
                  return [
                      'latitude' => $location->latitude,
                      'longitude' => $location->longitude,
                  ];
              }),
              'favorites_count' => $journey->favorites->count(),
              'users_count' => $journey->users->count(),
              'is_favorite' => $journey->is_favorite, // 👈 add favorite flag here
          ];
      
       return $this->jsonResponse(true, "Get Data", 200, $data);
      }
 public function getHotels()
{
    $hotels = Hotel::with([
        'city',
        'images',
        'feedbacks',
        'favorites',
        'properties',
        'rooms.type', 
    ])
    ->withExists(['favorites as is_favorite' => function ($query) {
        $query->where('user_id', auth()->id());
    }])
    ->get()
    ->map(function ($hotel) {
        return [
            'id' => $hotel->id,
            'name' => $hotel->name,
            'description' => $hotel->description,
            'address' => $hotel->address,
            'latitude' => $hotel->latitude,
            'longitude' => $hotel->longitude,
            'city' => $hotel->city,
            'images' => $hotel->images,
            'feedbacks' => $hotel->feedbacks,
            'properties' => $hotel->properties,
            'rooms' => $hotel->rooms->map(function ($room) {
                return [
                    'id' => $room->id,
                    'name' => $room->name,
                    'capacity' => $room->capacity,   
                    'quantity' => $room->quantity,
                    'price' => $room->price,
                    'is_active' => $room->is_active==1 ? true : false,
                    'room_type' => $room->type ? [
                        'id' => $room->type->id,
                        'name' => $room->type->name,
                       
                        
                    ] : null,
                ];
            }),
            'average_rating' => $hotel->average_rating,
            'is_favorite' => $hotel->is_favorite,
        ];
    });

    return $this->jsonResponse(true, "Get Hotels", 200, $hotels);
}
}
