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
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $cabins = Cabin::query()->with('city') // Eager load
            ->when($request->search['value'] ?? null, function ($query, $search) {
                $query->where('phone', 'like', '%'.$search.'%')
                    ->orWhere('address', 'like', '%'.$search.'%');
            });
;
            
            return DataTables::of($cabins)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editUrl = route('cabins.edit', $row->id);
                    $deleteUrl = route('cabins.destroy', $row->id);
                    
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
                // Safely get translated name with fallback
                return $row->city->name 
                    ? $row->city->getTranslation('name', 'en', true)->name // Spatie example
                    : '-';
            })
                ->rawColumns(['action', 'location', 'images', 'city'])
                ->toJson();
        }
        
        return view('admin.cabins.index');
    }

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
