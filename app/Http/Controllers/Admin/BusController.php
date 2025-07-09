<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Bus;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\City;

class BusController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
              $data = Bus::query()->with('city','translations') // Eager load
            ->when($request->search['value'] ?? null, function ($query, $search) {
                $query->where('owner_name', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%')
                    ->orWhere('address', 'like', '%'.$search.'%');
            });


            return DataTables::of($data)
                ->addIndexColumn()
                ->setRowClass(fn($row) => 'align-middle')
                ->addColumn('action', function ($row) {
                    $editUrl = route('buses.edit', $row->id);
                    $deleteUrl = route('buses.destroy', $row->id);

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
                ->editColumn('location', function($row) {
                    return '<span class="">'.$row->latitude.', '.$row->longitude.'</span>';
                })
                ->editColumn('created_at', fn($row) => $row->created_at->format('M d, Y h:i A'))
               ->editColumn('city', function($row) {
                // Safely get translated name with fallback
                return $row->city->name 
                    ? $row->city->getTranslation('name', 'en', true)->name // Spatie example
                    : '-';
            })
            ->addColumn('name', function ($row) {
            return $row->getTranslation('owner_name', 'en', true)->onwer_name ?? '-';
                })
                ->rawColumns(['action', 'location', 'city', 'created_at','name'])
                ->make(true);
        }

        return view('admin.buses.index');
    }

    public function create()
    {

        $cities = City::with(['translations' => function ($query) {
        $query->where('locale', 'en');
        }])->get();

        return view('admin.buses.create',compact('cities'));
    }

    public function store(Request $request)
    {
        try {
            //code...
        
        $validated = $request->validate([
            'phone' => 'required|string|max:20|unique:buses,phone',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'required|string|max:255',
            'name' => 'required|array', // changed from string
            'name.*' => 'required|string|max:255', 
            'city_id' => 'required|exists:cities,id',
        ]);
       
       $bus= Bus::create([
            'phone' => $validated['phone'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'address' => $validated['address'],
            'city_id' => $validated['city_id'],
        ]); 
          foreach ($request->name as $locale => $name) {
          $bus->translateOrNew($locale)->owner_name = $name;
          }

         $bus->save();
        return redirect()->route('buses.index')
            ->with('success', 'Bus created successfully.');
            } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }

    public function edit(Bus $bus)
    {
        return view('admin.buses.edit', compact('bus'));
    }

    public function update(Request $request, Bus $bus)
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:20|unique:buses,phone,'.$bus->id,
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
        ]);

        $bus->update($validated);

        return redirect()->route('buses.index')
            ->with('success', 'Bus updated successfully.');
    }

    public function destroy(Bus $bus)
    {
        $bus->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bus deleted successfully.'
        ]);
    }
}
