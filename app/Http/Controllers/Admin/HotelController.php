<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
use App\Models\City;
use App\Models\Account;
use App\Models\room;
use App\Models\HotelRoom;
class HotelController extends Controller
{
      public function index(Request $request)
    {
        
        if ($request->ajax()) {
            $hotels = Hotel::query()->with('city','translation') // Eager load
            ->when($request->search['value'] ?? null, function ($query, $search) {
                $query->where('phone', 'like', '%'.$search.'%')
                    ->orWhere('address', 'like', '%'.$search.'%');
            });
;
            
            return DataTables::of($hotels)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editUrl = route('hotels.edit', $row->id);
                    $deleteUrl = route('hotels.destroy', $row->id);
                    
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
                ->addColumn('name', function ($row) {
                    return $row->getTranslation('name', 'en', true)->name ?? '-';
                })
                ->rawColumns(['action', 'location', 'images', 'city', 'name'])
                ->toJson();
        }
        
        return view('admin.hotels.index');
    }

    public function create()
    {
        $cities = City::with(['translations' => function ($query) {
        $query->where('locale', 'en');
        }])->get();
        $account=Account::where('role_type', 'hotel')->get();
       
        return view('admin.hotels.create',compact('cities','account'));
    }

      public function detail($id)
    {
        $hotel = Hotel::where('id',$id)->with('rooms')->first();
        
    
        return view('admin.hotels.detail',compact('hotel'));
    }
    public function unit($hotel_id,$room_id){
    $hotel = Hotel::findOrFail($hotel_id); // get hotel
    $room = HotelRoom::with('units')
        ->where('hotel_id', $hotel_id)
        ->where('id', $room_id)
        ->firstOrFail();

    return view('admin.hotels.unit', compact('room','hotel'));
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
        'account_id' => 'required|exists:accounts,id', // Ensure account exists
    ]);

    // Store non-translatable fields
    $hotel = Hotel::create([
        'phone' => $request->phone,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'city_id' => $request->city_id,
        'account_id' => $request->account_id, // Link to the account
    ]);

    // Store translations
    foreach ($request->name as $locale => $name) {
        $hotel->translateOrNew($locale)->name = $name;
        $hotel->translateOrNew($locale)->description = $request->description[$locale] ?? null;
    }

    $hotel->save();

    // Handle images (if any)
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $path = $image->store('uploads', 's3');

            $hotel->images()->create([
                'path' => $path
            ]);
        }
    }

    return redirect()->route('hotels.index')->with('success', 'Hotel created successfully');
}


    public function edit(Hotel $hotel)
    {
        return view('admin.hotels.edit', compact('hotel'));
    }

    public function update(Request $request, Hotel $hotel)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        $hotel->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);
        
        // Handle new image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('cabins', 's3');
                
                $hotel->images()->create([
                    'path' => $path
                ]);
            }
        }
        
        return redirect()->route('hotels.index')->with('success', 'Cabin updated successfully');
    }

    public function destroy(Hotel $hotel)
    {
        // Delete images from S3
        foreach ($hotel->images as $image) {
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
