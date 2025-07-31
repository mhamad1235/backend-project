<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Journey;

class JourneyController extends Controller
{
       public function index()
    {
        $account = auth('account')->user();

     $journeys = $account->journeys()->with('images')->get();


        return response()->json($journeys);
    }
public function show($id)
{
    $journey = Journey::with(['images', 'translations'])->findOrFail($id);
    $this->authorize('view', $journey); 
    return response()->json($journey);
}

    public function store(Request $request)
    {
        $account = auth('account')->user();

        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'destination' => 'required|string',
            'duration' => 'required|integer',
            'price' => 'required|integer',
            'locale' => 'required|string|size:2',
            'images.*' => 'nullable|image|max:2048',
        ]);

        $journey = $account->journeys()->create([
            'destination' => $validated['destination'],
            'duration' => $validated['duration'],
            'price' => $validated['price'],
        ]);

        $journey->translateOrNew($validated['locale'])->name = $validated['name'];
        $journey->translateOrNew($validated['locale'])->description = $validated['description'];
        $journey->save();

       if ($request->hasFile('images')) {
        $images = $request->file('images');
         $images = is_array($images) ? $images : [$images];

         foreach ($images as $image) {
         $path = $image->store('uploads', 's3');
         $journey->images()->create(['path' => $path]);
    }
}


        return response()->json(['message' => 'Journey created', 'data' => $journey], 201);
    }

public function update(Request $request, $id)
{   
   
    $account = auth('account')->user();
    $journey = Journey::where('id', $id)->firstOrFail();
    $this->authorize('update', $journey); 
    $validated = $request->validate([
        'name' => 'sometimes|required|string',
        'description' => 'sometimes|required|string',
        'destination' => 'sometimes|required|string',
        'duration' => 'sometimes|required|integer',
        'price' => 'sometimes|required|integer',
        'images.*' => 'nullable|image|max:2048',
        'locale' => 'sometimes|required|string|size:2', 
    ]);

    
    $journey->update($request->only(['destination', 'duration', 'price']));

    
   if ($request->has('locale') && $request->has('name') && $request->has('description')) {
    foreach (['en', 'ar', 'ckb'] as $locale) {
        $journey->translateOrNew($locale)->fill([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);
    }

    $journey->save(); 
}


 
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $path = $image->store('journeys', 's3');
            $journey->images()->create(['path' => $path]);
        }
    }

    $journey->load('translations', 'images');
    return response()->json([
        'message' => 'Journey updated successfully',
        'data' => $journey
    ]);
}


    public function destroy($id)
    {
         $account = auth('account')->user();
         $journey = Journey::where('id', $id)->firstOrFail();
         $this->authorize('delete', $journey); 
         $journey->delete();

        return response()->json(['message' => 'Journey deleted']);
    }
}
