<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Journey;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\Environment;
use App\Models\Favorite;
use Illuminate\Support\Facades\App;
class FavoriteController extends Controller
{
      private const FAVORITE_TYPES = [
        'environment' => Environment::class,
        'hotel'       => Hotel::class,
        'restaurant'  => Restaurant::class,
        'journey'     => Journey::class,
    ];

    public function toggle(string $type, int $id)
    {
        if (!isset(self::FAVORITE_TYPES[$type])) {
            return response()->json(['message' => 'Invalid favorite type'], 400);
        }

        $modelClass = self::FAVORITE_TYPES[$type];
        $item = $modelClass::findOrFail($id);

        $favorite = Favorite::where('user_id', Auth::id())
            ->where('favoritable_id', $item->id)
            ->where('favoritable_type', $modelClass)
            ->first();

        if ($favorite) {
            $favorite->delete();
        return $this->jsonResponse(true,"Favorites fetched",200,"Removed from favorites");
           
        }

        $item->favorites()->create(['user_id' => Auth::id()]);
          return $this->jsonResponse(true,"Favorites fetched",200,"Added for favorites");
      
    }
       public function index()
    {   App::setLocale(app()->getLocale());
        $favorites = Favorite::with([
        'favoritable.city',     // eager load city for each favoritable
        'favoritable.images',
        'favoritable.feedbacks',
       
    ])
    ->where('user_id', Auth::id())
    ->get()
    ->map(function ($fav) {
    

     return [
    'id' => $fav->id,
    'type' => array_search($fav->favoritable_type, self::FAVORITE_TYPES, true) ?: 'unknown',
    'data' => array_merge(
        $fav->favoritable->toArray(),       
        ['average_rating' => $fav->favoritable->average_rating] 
    ),
];
    });
      return $this->jsonResponse(true,"Get Favorites",200,$favorites);

    }
}
