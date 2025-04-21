<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class CityCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'result' => true,
            'status' => Response::HTTP_OK,
            'message' => "Cities list",
            "total" => $this->collection->count(),
            'data' => $this->collection->map(function ($city) {
                return [
                    'id' => $city->id,
                    'name' => $city->name,
                ];
            }),
        ];
    }
}
