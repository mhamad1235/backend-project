<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Enums\{
    BannerPosition,
    DeviceType,
    Gender,
    Language,
    OrderStatus,
    StoreName,
    TransactionType,
    VoteType,
};
use App\Http\Resources\CityCollection;
use App\Models\City;
use App\Models\Property;
use App\Models\RoomType;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class DataResourceController extends Controller
{
    
     
    public function cities()
    {
        try {        
        return new CityCollection(City::orderBy('created_at')->get());
    } catch (\Throwable $th) {
        return $th->getMessage();
    }
    }
    
    public function properties()
    {
        try {
            $properties =Property::all();
            return response()->json([
                'success' => true,
                'message' => 'Properties retrieved successfully',
                'data' => $properties
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function roomtypes(){
       try {   
       $roomTypes = RoomType::orderBy('created_at')
        ->get()
        ->map(function ($roomType) {
        return [
            'id' => $roomType->id,
            'name' => $roomType->name, 
        ];
    });

       return response()->json([
        'data' => $roomTypes
       ]);
    } catch (\Throwable $th) {
        return $th->getMessage();
    }
    }
    public function foodtypes(Request $request)
    {
        // Get the Accept-Language header or default to 'en'
        $lang = $request->header('lang', 'en');

        // Define food types for different languages
        $foodTypes = [
            'en' => [
                ['key' => 'popular', 'name' => 'Popular'],
                ['key' => 'starters', 'name' => 'Starters'],
                ['key' => 'mains', 'name' => 'Mains'],
                ['key' => 'drinks', 'name' => 'Drinks'],
                ['key' => 'desserts', 'name' => 'Desserts'],
            ],
            'ar' => [
                ['key' => 'popular', 'name' => 'الأكثر شعبية'],
                ['key' => 'starters', 'name' => 'المقبلات'],
                ['key' => 'mains', 'name' => 'الأطباق الرئيسية'],
                ['key' => 'drinks', 'name' => 'المشروبات'],
                ['key' => 'desserts', 'name' => 'الحلويات'],
            ],
            // Add more languages as needed
        ];

        return response()->json([
            'result' => true,
            'status' => 200,
            'message' => 'Food Types',
            'data' => $foodTypes[$lang] ?? $foodTypes['en']
        ]);
    }

    
}
