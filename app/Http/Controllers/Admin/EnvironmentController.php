<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Environment;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
use App\Models\City;
use App\Enums\RestaurantType;
use App\Models\UnavailableSlot;

class EnvironmentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $cabins = Environment::query()->with('city','translations') // Eager load
            ->when($request->search['value'] ?? null, function ($query, $search) {
                $query->where('phone', 'like', '%'.$search.'%')
                    ->orWhere('address', 'like', '%'.$search.'%');
            });
;
            
            return DataTables::of($cabins)
                ->addIndexColumn()
            ->addColumn('action', function ($row) {
    $editUrl = route('environments.edit', $row->id);
    $deleteUrl = route('environments.destroy', $row->id);
    $slotsUrl = route('environments.slots.index', $row->id); // NEW
    
    return '
    <div class="dropdown d-inline-block">
        <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="ri-more-fill align-middle"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="'.$editUrl.'"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>
            <!-- NEW SLOT MANAGEMENT LINK -->
            <li><a class="dropdown-item" href="'.$slotsUrl.'">
                <i class="ri-time-line align-bottom me-2 text-muted"></i> Manage Slots
            </a></li>
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
                        $html .= '<a href="'.url($image->path).'" target="_blank">
                                  <img src="'.url($image->path).'" 
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
             ->addColumn('name', function ($row) {
                    return $row->getTranslation('name', 'en', true)->name ?? '-';
                })
                ->rawColumns(['action', 'location', 'images', 'city'])
                ->toJson();
        }
        
        return view('admin.environments.index');
    }

    public function create()
    {
        $cities = City::with(['translations' => function ($query) {
        $query->where('locale', 'en');
        }])->get();
        return view('admin.environments.create',compact('cities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|array', // changed from string
            'name.*' => 'required|string|max:255', // validate each translation
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
            'phone' => 'required|string|max:20',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'city_id' => 'required|exists:cities,id',
            'type' => 'required',
        ]);
        
        $enviroment = Environment::create([
            'phone' => $request->phone,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'city_id' => $request->city_id,
            'type' => $request->type,
        ]);
        foreach ($request->name as $locale => $name) {
        $enviroment->translateOrNew($locale)->name = $name;
        $enviroment->translateOrNew($locale)->description = $request->description[$locale] ?? null;
    }

    $enviroment->save();
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('uploads', 's3');
                
                $enviroment->images()->create([
                    'path' => $path
                ]);
            }
        }
        
        return redirect()->route('environments.index')->with('success', 'Cabin created successfully');
    }

    public function edit(Environment $environment)
    { $cities = City::with(['translations' => function ($query) {
        $query->where('locale', 'en');
        }])->get();
        return view('admin.environments.edit', compact('environment', 'cities'));
    }

public function update(Request $request, Environment $environment)
{
    $request->validate([
        'name' => 'required|array',
        'name.en' => 'required|string|max:255',
        'name.ku' => 'required|string|max:255',
        'name.ar' => 'required|string|max:255',
        'description' => 'required|array',
        'description.en' => 'required|string',
        'description.ku' => 'required|string',
        'description.ar' => 'required|string',
        'type' => 'required|string',
        'phone' => 'required|string|max:20',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'city_id' => 'required|exists:cities,id',
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
    ]);

    // Update non-translatable fields
    $environment->update([
        'type' => $request->type,
        'phone' => $request->phone,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'city_id' => $request->city_id,
    ]);

    // Update translations
    foreach (['en', 'ku', 'ar'] as $locale) {
        $environment->translateOrNew($locale)->name = $request->name[$locale];
        $environment->translateOrNew($locale)->description = $request->description[$locale];
    }

    $environment->save();

    // Handle new image uploads
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $path = $image->store('uploads', 's3');
            
            $environment->images()->create([
                'path' => $path
            ]);
        }
    }

    return redirect()->route('environments.index')->with('success', 'Cabin updated successfully');
}
    public function destroy(Environment $cabin)
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
    try {
 
        if (Storage::disk('s3')->exists($image->path)) {
            Storage::disk('s3')->delete($image->path);
        }
        $image->delete();

        return response()->json(['success' => true, 'message' => 'Image deleted successfully']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Error deleting image: ' . $e->getMessage()], 500);
    }
}
    public function slotsIndex(Environment $environment)
{
    $slots = $environment->unavailableSlots()->latest()->get();
    return view('admin.environments.slots', compact('environment', 'slots'));
}

public function storeSlot(Request $request, Environment $environment)
{
    $request->validate([
        'start_time' => 'required|date',
        'end_time' => 'required|date|after:start_time',
    ]);

    $environment->unavailableSlots()->create([
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'unavailable_date' => \Carbon\Carbon::parse($request->start_time)->format('Y-m-d'),
    ]);

    return redirect()->back()->with('success', 'Slot added successfully');
}

public function updateSlot(Request $request, Environment $environment, UnavailableSlot $slot)
{
    $request->validate([
        'start_time' => 'required|date',
        'end_time' => 'required|date|after:start_time',
    ]);

    $slot->update([
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'unavailable_date' => \Carbon\Carbon::parse($request->start_time)->format('Y-m-d'),
    ]);

    return redirect()->back()->with('success', 'Slot updated successfully');
}

}
