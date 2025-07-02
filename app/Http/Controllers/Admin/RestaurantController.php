<?php

namespace App\Http\Controllers\Admin;

use App\Models\Restaurant;
use App\Models\City;
use App\Models\Food;
use Illuminate\Http\Request;
use DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class RestaurantController extends Controller
{
     public function index(Request $request)
    {
        if ($request->ajax()) {
            $restaurants = Restaurant::query()->with(['city', 'images'])
                ->when($request->search['value'] ?? null, function ($query, $search) {
                    $query->where('name', 'like', '%'.$search.'%')
                          ->orWhere('address', 'like', '%'.$search.'%');
                });

            return DataTables::of($restaurants)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editUrl = route('restaurants.edit', $row->id);
                    $deleteUrl = route('restaurants.destroy', $row->id);
                    
                    return '
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ri-more-fill align-middle"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="'.$editUrl.'"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>
                            <li class="dropdown-divider"></li>
                            <li>
                                <a href="javascript:void(0)" class="dropdown-item delete-btn" data-id="'.$row->id.'" data-url="'.$deleteUrl.'">
                                    <i class="ri-delete-bin-fill text-muted me-2 align-bottom"></i> Delete
                                </a>
                            </li>
                            <li><a class="dropdown-item" href="'.route('restaurants.foods.index', $row->id)
                            .'"><i class="ri-restaurant-fill align-bottom me-2 text-muted"></i> Manage Foods</a></li>

                        </ul>
                    </div>';
                })
                ->addColumn('location', function ($row) {
                    return '
                    <a href="https://www.google.com/maps?q='.$row->latitude.','.$row->longitude.'" 
                       target="_blank" class="text-primary">
                       <i class="ri-map-pin-line align-middle me-1"></i>
                       View Map
                    </a>';
                })
                ->addColumn('images', function ($row) {
                    $images = $row->images;
                    if ($images->isEmpty()) {
                        return '<span class="text-muted">No images</span>';
                    }
                    
                    $html = '<div class="d-flex flex-wrap gap-1">';
                    foreach ($images as $image) {
                        $html .= '<a href="'.Storage::disk('s3')->url($image->path).'" target="_blank">
                                  <img src="'.Storage::disk('s3')->url($image->path).'" 
                                       class="img-thumbnail" style="width:50px;height:50px;object-fit:cover">
                                  </a>';
                    }
                    $html .= '</div>';
                    return $html;
                })
                ->editColumn('city', function($row) {
                    return $row->city? $row->city->name : '-';
                })
                ->editColumn('created_at', function($row) {
                    return $row->created_at->format('Y-m-d H:i:s');
                })
                ->rawColumns(['action', 'location', 'images'])
                ->toJson();
        }
        
        return view('admin.restaurants.index');
    }

    public function create()
    {
        $cities = City::all();
        return view('admin.restaurants.create', compact('cities'));
    }

  public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'images' => 'nullable|array', // Changed to accept multiple images
        'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Validation for each image
        'latitude' => 'nullable|numeric|between:-90,90',
        'longitude' => 'nullable|numeric|between:-180,180',
        'address' => 'required|string',
        'city_id' => 'required|exists:cities,id',
    ]);

    // Create restaurant first to get ID
    $restaurant = Restaurant::create($request->except('images'));

    // Handle multiple images
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
      $path = $image->store('uploads', 's3');
            
            $restaurant->images()->create([
                'path' => $path
            ]);
        }
    }

    return redirect()->route('restaurants.index')->with('success', 'Restaurant created successfully.');
}

    public function edit(Restaurant $restaurant)
    {
        $cities = City::all();
        return view('admin.restaurants.edit', compact('restaurant', 'cities'));
    }

    public function update(Request $request, Restaurant $restaurant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'address' => 'required|string',
            'city_id' => 'required|exists:cities,id',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            // Delete old image
            if ($restaurant->image) {
                Storage::delete('public/'.$restaurant->image);
            }
            
            $path = $request->file('image')->store('public/restaurants');
            $data['image'] = str_replace('public/', '', $path);
        }

        $restaurant->update($data);

        return redirect()->route('restaurants.index')->with('success', 'Restaurant updated successfully.');
    }

    public function destroy(Restaurant $restaurant)
    {
        // Delete associated foods
        $restaurant->foods()->delete();
        
        // Delete image
        if ($restaurant->image) {
            Storage::delete('public/'.$restaurant->image);
        }
        
        $restaurant->delete();
        
        return response()->json(['success' => true, 'message' => 'Restaurant deleted successfully.']);
    }

    // Food Management Methods
    public function foodsIndex(Restaurant $restaurant, Request $request)
    {
        if ($request->ajax()) {
            $data = $restaurant->foods();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editUrl = route('restaurants.foods.edit', [$row->restaurant_id, $row->id]);
                    $deleteUrl = route('restaurants.foods.destroy', [$row->restaurant_id, $row->id]);

                    $html = '<div class="dropdown d-inline-block">';
                    $html .= '<button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">';
                    $html .= '<i class="ri-more-fill align-middle"></i>';
                    $html .= '</button>';
                    $html .= '<ul class="dropdown-menu dropdown-menu-end">';
                    
                    $html .= '<li><a class="dropdown-item edit-item-btn" href="'.$editUrl.'">';
                    $html .= '<i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>';
                    
                    $html .= '<li class="dropdown-divider"></li>';
                    
                    $html .= '<li><a href="javascript:void(0)" class="dropdown-item remove-item-btn delete-btn" ';
                    $html .= 'data-id="'.$row->id.'" data-url="'.$deleteUrl.'">';
                    $html .= '<i class="ri-delete-bin-fill text-muted me-2 align-bottom"></i> Delete</a></li>';
                    
                    $html .= '</ul></div>';

                    return $html;
                })
                ->editColumn('image', function($row) {
                    return $row->image 
                        ? '<img src="'.asset('storage/'.$row->image).'" alt="'.$row->name.'" class="img-thumbnail" width="60">' 
                        : '<div class="avatar-sm bg-light rounded"><span class="avatar-title rounded">'.substr($row->name, 0, 1).'</span></div>';
                })
                ->editColumn('price', function($row) {
                    return number_format($row->price, 2);
                })
                ->rawColumns(['action', 'image'])
                ->make(true);
        }

        return view('admin.restaurants.foods.index', compact('restaurant'));
    }

    public function foodsCreate(Restaurant $restaurant)
    {
        return view('admin.restaurants.foods.create', compact('restaurant'));
    }

    public function foodsStore(Request $request, Restaurant $restaurant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('image');
        $data['restaurant_id'] = $restaurant->id;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/foods');
            $data['image'] = str_replace('public/', '', $path);
        }

        Food::create($data);

        return redirect()->route('restaurants.foods.index', $restaurant->id)->with('success', 'Food item added successfully.');
    }

    public function foodsEdit(Restaurant $restaurant, Food $food)
    {
        return view('admin.restaurants.foods.edit', compact('restaurant', 'food'));
    }

    public function foodsUpdate(Request $request, Restaurant $restaurant, Food $food)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            // Delete old image
            if ($food->image) {
                Storage::delete('public/'.$food->image);
            }
            
            $path = $request->file('image')->store('public/foods');
            $data['image'] = str_replace('public/', '', $path);
        }

        $food->update($data);

        return redirect()->route('restaurants.foods.index', $restaurant->id)->with('success', 'Food item updated successfully.');
    }

    public function foodsDestroy(Restaurant $restaurant, Food $food)
    {
        if ($food->image) {
            Storage::delete('public/'.$food->image);
        }
        
        $food->delete();
        
        return response()->json(['success' => true, 'message' => 'Food item deleted successfully.']);
    }
}