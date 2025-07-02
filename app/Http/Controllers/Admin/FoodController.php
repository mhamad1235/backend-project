<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class FoodController extends Controller
{
public function index(Request $request, Restaurant $restaurant)
{
    if ($request->ajax()) {
        $data = $restaurant->foods()->with('images'); 

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('images', function ($row) {
                $images = $row->images;

                if ($images->isEmpty()) {
                    return '<span class="text-muted">No images</span>';
                }

                $html = '<div class="d-flex flex-wrap gap-1">';
                foreach ($images as $image) {
                    $url = Storage::disk('s3')->url($image->path);
                    $html .= '<a href="'.$url.'" target="_blank">
                                <img src="'.$url.'" class="img-thumbnail" style="width:50px;height:50px;object-fit:cover">
                              </a>';
                }
                $html .= '</div>';

                return $html;
            })
            ->editColumn('price', fn($row) => number_format($row->price, 2))
            ->addColumn('action', function ($row) {
                $editUrl = route('restaurants.foods.edit', [$row->restaurant_id, $row->id]);
                $deleteUrl = route('restaurants.foods.destroy', [$row->restaurant_id, $row->id]);

                return '
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown">
                            <i class="ri-more-fill align-middle"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item edit-item-btn" href="'.$editUrl.'">
                                <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>
                            <li class="dropdown-divider"></li>
                            <li><a href="javascript:void(0)" class="dropdown-item remove-item-btn delete-btn" 
                                   data-id="'.$row->id.'" data-url="'.$deleteUrl.'">
                                <i class="ri-delete-bin-fill text-muted me-2 align-bottom"></i> Delete</a></li>
                        </ul>
                    </div>';
            })
            ->rawColumns(['images', 'action'])
            ->make(true);
    }

    return view('admin.restaurants.foods.index', compact('restaurant'));
}


    public function create(Restaurant $restaurant)
    {
        return view('admin.restaurants.foods.create', compact('restaurant'));
    }

   public function store(Request $request, Restaurant $restaurant)
{
    try {
      
    $data = $request->validate([
        'name' => 'required|string',
        'price' => 'required|numeric',
        'category' => 'required|string',
      'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);
    
    $food = $restaurant->foods()->create($data);
    
    if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('uploads', 's3');
                
                $food->images()->create([
                    'path' => $path
                ]);
            }
            
        }

    return redirect()->route('restaurants.foods.index', $restaurant->id)
                     ->with('success', 'Food created successfully.');
                       //code...
    } catch (\Throwable $th) {
        dd($th->getMessage());
    }
}

    public function edit(Restaurant $restaurant, Food $food)
    {
        return view('admin.foods.edit', compact('restaurant', 'food'));
    }

    public function update(Request $request, Restaurant $restaurant, Food $food)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category' => 'required|in:popular,starters,mains,drinks,desserts',
            'image' => 'nullable|image|max:2048',
        ]);

        $food->update($validated);

        if ($request->hasFile('image')) {
            if ($food->image) {
                Storage::disk('s3')->delete($food->image->path);
                $food->image()->delete();
            }
            $path = $request->file('image')->store('foods', 's3');
            Storage::disk('s3')->setVisibility($path, 'public');
            $food->image()->create(['path' => $path]);
        }

        return redirect()->route('restaurants.foods.index', $restaurant)->with('success', 'Food updated.');
    }

    public function destroy(Restaurant $restaurant, Food $food)
    {
        if ($food->image) {
            Storage::disk('s3')->delete($food->image->path);
            $food->image()->delete();
        }
        $food->delete();

        return response()->json(['success' => true]);
    }

}