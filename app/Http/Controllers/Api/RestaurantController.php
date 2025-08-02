<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Restaurant;
use App\Models\Account;
use Illuminate\Support\Facades\App;

class RestaurantController extends Controller
{
      public function store(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string',
            'translations.*.name' => 'required|string',
            'translations.*.description' => 'nullable|string',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $account = auth('account')->user();

        DB::beginTransaction();
        try {
            // Create restaurant
            $restaurant = new Restaurant($request->only(['latitude', 'longitude', 'address', 'city_id']));
            $restaurant->account_id = $account->id;
            $restaurant->save();

            // Add translations
            foreach ($request->translations as $trans) {
                $restaurant->translateOrNew($trans['locale'])->name = $trans['name'];
                $restaurant->translateOrNew($trans['locale'])->description = $trans['description'] ?? null;
            }
            $restaurant->save();

            // Attach images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $imageFile) {
                    $path = $imageFile->store('restaurants', 'public');
                    $restaurant->images()->create(['path' => $path]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Restaurant created successfully.',
                'data' => $restaurant->load('translations', 'images'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create restaurant.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
    $restaurant = Restaurant::with(['images', 'city'])->findOrFail($id);

    App::setLocale(app()->getLocale());

    return response()->json([
        'id' => $restaurant->id,
        'name' => $restaurant->name, 
        'description' => $restaurant->description,
        'latitude' => $restaurant->latitude,
        'longitude' => $restaurant->longitude,
        'address' => $restaurant->address,
        'city' => $restaurant->city->name ?? null,
        'images' => $restaurant->images
    ]);
    }

    public function destroy($id)
    {  
    $account = Auth::guard('account')->user();

    $restaurant = Restaurant::where('id', $id)->where('account_id', $account->id)->first();

    if (! $restaurant) {
        return response()->json(['message' => 'Restaurant not found or unauthorized'], 403);
    }
      $restaurant->delete();
    return response()->json(['message' => 'Restaurant deleted successfully']);
    }
}