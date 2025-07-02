<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cabin;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
use App\Models\City;
class CabinController extends Controller
{
   

    public function create()
    {
        $cities = City::with(['translations' => function ($query) {
        $query->where('locale', 'en');
        }])->get();
        return view('admin.cabins.create',compact('cities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'city_id' => 'required|exists:cities,id',
        ]);
        
        $cabin = Cabin::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'city_id' => $request->city_id,
        ]);
        
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('uploads', 's3');
                
                $cabin->images()->create([
                    'path' => $path
                ]);
            }
        }
        
        return redirect()->route('cabins.index')->with('success', 'Cabin created successfully');
    }

    public function edit(Cabin $cabin)
    {
        return view('admin.cabins.edit', compact('cabin'));
    }

    public function update(Request $request, Cabin $cabin)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        $cabin->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);
        
        // Handle new image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('cabins', 's3');
                
                $cabin->images()->create([
                    'path' => $path
                ]);
            }
        }
        
        return redirect()->route('cabins.index')->with('success', 'Cabin updated successfully');
    }

    public function destroy(Cabin $cabin)
    {
        // Delete images from S3
        foreach ($cabin->images as $image) {
            Storage::disk('s3')->delete($image->path);
        }
        
        // Delete cabin and related images
        $cabin->images()->delete();
        $cabin->delete();
        
        return response()->json(['success' => true, 'message' => 'Cabin deleted successfully']);
    }
    
    public function deleteImage(Image $image)
    {
        Storage::disk('s3')->delete($image->path);
        $image->delete();
        
        return response()->json(['success' => true]);
    }
}
