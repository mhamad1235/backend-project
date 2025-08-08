<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Restaurant;
use App\Models\Account;
use Illuminate\Support\Facades\App;
use App\Models\Food;
class RestaurantController extends Controller
{
   public function store(Request $request)
{
    $account = auth('account')->user();

    // Check if account already has a restaurant
    if ($account->restaurant) {
        return response()->json(['message' => 'Restaurant already exists for this account.'], 400);
    }

    // Validate the input
    $validated = $request->validate([
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'address' => 'required|string|max:255',
        'city_id' => 'required|exists:cities,id',
        'name' => 'required|string',
        'description' => 'required|string',
        'locale' => 'required|string|size:2',
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    DB::beginTransaction();

    try {
        // Create restaurant
        $restaurant = $account->restaurant()->create([
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'address' => $validated['address'],
            'city_id' => $validated['city_id'],
        ]);

        $requiredLocales = ['ar', 'en', 'ku'];
        for ($i=0; $i < count($requiredLocales); $i++) { 
        $restaurant->translateOrNew($requiredLocales[$i])->name = $validated['name'];
        $restaurant->translateOrNew($requiredLocales[$i])->description = $validated['description'] ?? null;
        $restaurant->save();
        }
  

        // Upload images if any
        if ($request->hasFile('images')) {
            $images = $request->file('images');
            $images = is_array($images) ? $images : [$images];

            foreach ($images as $image) {
                $path = $image->store('upload', 'public');
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


     public function storeFood(Request $request)
    {
        $account = auth('account')->user();
        $restaurant = $account->restaurant;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category' => 'nullable|string|max:255',
            'images.*' => 'nullable|image|max:2048',
            'description' => 'required|string|max:1000',
            'is_available' => 'required|boolean',
        ]);

        
        $food = $restaurant->foods()->create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'category' => $validated['category'] ?? null,
            'description' => $validated['description'],
            'is_available' => $validated['is_available'],
        ]);

        if ($request->hasFile('images')) {
            foreach ((array) $request->file('images') as $image) {
                $path = $image->store('uploads', 's3');
                $food->images()->create(['path' => $path]);
            }
        }
        return $this->jsonResponse(
            'Food created successfully',
            $food->load('images'),
            201
        );
        
    }
 public function foods(Request $request)
{
    $account = auth('account')->user();
    $restaurant = $account->restaurant;

    if (!$restaurant) {
        return response()->json(['message' => 'Restaurant not found'], 404);
    }

    $search = $request->query('search');

    $query = $restaurant->foods()->available()->with('images');
    if ($search) {
        $query->where('name', 'LIKE', '%' . $search . '%');
    }

    $foods = $query->get();

    // Group foods by category
    $grouped = $foods->groupBy('category')->map(function ($items, $category) {
        return [
            'category' => $category,
            'foods' => $items->values(),
        ];
    })->values();

    return $this->jsonResponse(true,'Foods grouped by category successfully',200,$grouped);
}



     public function deleteFood($id){
        $account = auth('account')->user();
        $restaurant = $account->restaurant;
        if (!$restaurant) {
            return response()->json(['message' => 'Restaurant not found'], 404);
        }
        $food = $restaurant->foods()->find($id);
        if (!$food) {
            return response()->json(['message' => 'Food not found'], 404);
        }
        $this->authorize('delete', $food);
        $food->delete();
        return response()->json(['message' => 'Food deleted successfully']);
     }
     
     public function updateFood(Request $request, $id)
     {
        $account = auth('account')->user();
        $restaurant = $account->restaurant;

        if (!$restaurant) {
            return response()->json(['message' => 'Restaurant not found'], 404);
        }

        $food = $restaurant->foods()->find($id);
        if (!$food) {
            return response()->json(['message' => 'Food not found'], 404);
        }

        $this->authorize('update', $food);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category' => 'nullable|string|max:255',
            'description' => 'required|string|max:1000',
            'is_available' => 'required|boolean',
        ]);

        $food->update($validated);

        if ($request->hasFile('images')) {
            foreach ((array) $request->file('images') as $image) {
                $path = $image->store('uploads', 's3');
                $food->images()->create(['path' => $path]);
            }
        }

        return response()->json([
            'message' => 'Food updated successfully',
            'data' => $food->load('images'),
        ]);    
    }

    public function showFood($id){
         App::setLocale(app()->getLocale());
     $food=Food::with('images')->find($id);
     $this->authorize('view', $food);
     return $this->jsonResponse(true,'Food retrieved successfully',200,$food);

    }
}