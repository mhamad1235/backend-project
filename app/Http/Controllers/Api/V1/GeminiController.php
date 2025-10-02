<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GeminiTable;
class GeminiController extends Controller
{
    public function geminiData(){
        $data=GeminiTable::all();
        return response()->json($data);
    }
}
