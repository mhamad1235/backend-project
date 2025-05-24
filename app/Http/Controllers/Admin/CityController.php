<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\City;
use App\Http\Requests\CityRequest;
class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
{
    if ($request->ajax()) {
        $data = City::with('translations')
            ->when($request->search, function($query) use ($request) {
                $query->whereHas('translations', function($q) use ($request) {
                    $q->where('name', 'like', '%'.$request->search.'%');
                });
            })
            ->select('cities.*');

        return DataTables::of($data)
            ->addIndexColumn()
            ->setRowClass(fn($row) => 'align-middle')
            ->addColumn('action', function ($row) {
                $td = '<td>';
                $td .= '<div class="dropdown d-inline-block">';
                $td .= '<button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">';
                $td .= '<i class="ri-more-fill align-middle"></i>';
                $td .= '</button>';
                $td .= '<ul class="dropdown-menu dropdown-menu-end">';

                // Edit button
                $td .= '<li><a class="dropdown-item edit-item-btn" href="'.route('cities.edit', $row->id).'"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>';

                // Delete button
                $td .= '<li class="dropdown-divider"></li>';
                $td .= '<li><a href="javascript:void(0)" data-id="'.$row->id.'" data-url="'.route('cities.destroy', $row->id).'" class="dropdown-item remove-item-btn delete-btn"><i class="ri-delete-bin-fill text-muted me-2 align-bottom"></i> Delete</a></li>';

                $td .= '</ul>';
                $td .= '</div>';
                $td .= '</td>';

                return $td;
            })
            ->addColumn("name", function($row) {
                // Display Arabic name if exists, otherwise first available translation
                return $row->translate('en')->name ?? $row->name;
            })
            ->editColumn("created_at", fn($row) => $row->created_at->format('Y-m-d H:i:s'))
            ->rawColumns(['action', 'status', 'name'])
            ->make(true);
    }

    return view('admin.cities.index');
}
    public function create()
    {



        return view('admin.cities.create');
    }

public function transformed(CityRequest $request): array
{
    $data = $request->validated();

    return [
        'name' => [
            'en' => $data['en']['name'],
            'ar' => $data['ar']['name'],
            'ku' => $data['ku']['name'],
        ],
        'cost' => $data['cost'],
        'is_delivery' => $data['is_delivery'] ?? false,
    ];
}

    public function store(CityRequest $request)
    {
        //check permission


        return redirect()->route('cities.index')->with([
            "message" => "You can't add new city",
            "icon" => "warning",
        ]);

        try {
            City::create($request->transformed());


            return redirect()->route('cities.index')->with([
                "message" =>  "Data has been saved successfully",
                "icon" => "success",
            ]);
        } catch (\Throwable $th) {

            throw $th;
            return redirect()->back()->with([
                "message" =>  $th->getMessage(),
                "icon" => "error",
            ]);
        }
    }

    public function edit(City $city)
    {
        //check permission


        return redirect()->route('cities.index')->with([
            "message" => "You can't add new city",
            "icon" => "warning",
        ]);

        return view('admin.cities.edit', compact('city'));
    }

    public function update(CityRequest $request, City $city)
    {

        try {
            $city->update($request->validated());

            return redirect()->route('cities.index')->with([
                "message" =>  "Data has been updated successfully",
                "icon" => "success",
            ]);
        } catch (\Throwable $th) {

            return redirect()->back()->with([
                "message" =>  $th->getMessage(),
                "icon" => "error",
            ]);
        }
    }

    public function destroy(City $city)
    {
        //check permission

        $city->delete();

        return 1;
    }

    public function changeStatus(Request $request, City $city)
    {


        $city->status = $request->status;
        $city->save();

        return 1;
    }
}
