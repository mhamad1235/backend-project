<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CityRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "en.name"     => ['required', "string", "min:3", "max:50"],
            "ar.name"     => ['required', "string", "min:3", "max:50"],
            "ku.name"     => ['required', "string", "min:3", "max:50"],
            "cost"        => ["required", "numeric"],
            "is_delivery" => ["sometimes", "boolean"],
        ];
    }

    public function messages()
    {
        return [
            "en.name.required" => "The English name field is required",
            "ar.name.required" => "The Arabic name field is required",
            "ku.name.required" => "The Kurdish name field is required",
            "cost.required"    => "The cost field is required",
        ];
    }


}
