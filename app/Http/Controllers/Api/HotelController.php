<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class HotelController extends Controller
{
    public function __construct()
    {
        // Set the locale for the controller
        App::setLocale(app()->getLocale());
    }


    public function getHotel(){
    $account = auth('account')->user();
    $hotel = $account->hotel()->with([
        'city',
        'images',
        'feedbacks',
        'properties',
        
        
    ])->first();
    return response()->json($hotel);
    }

}
