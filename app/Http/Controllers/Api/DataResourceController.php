<?php

namespace App\Http\Controllers\Api;

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
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class DataResourceController extends Controller
{
 
    public function cities()
    {
        try {
            //code...
        
        return new CityCollection(City::orderBy('created_at')->get());
    } catch (\Throwable $th) {
        return $th->getMessage();
    }
    }

 
}
