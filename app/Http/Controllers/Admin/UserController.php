<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Exports\GeneralExport;
use App\Http\Requests\UserRequest;
use App\Models\City;
use App\Services\DataService;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
   public function index(Request $request)
{
    if ($request->ajax()) {
        $data = User::select('id', 'name', 'phone', 'dob', 'created_at')
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });

        return Datatables::of($data)
            ->addIndexColumn()
            ->editColumn("id", fn($row) => '<a href="#" class="fw-medium link-primary">#' . $row->id . '</a>')
            ->addColumn('dob', fn($row) => $row->dob ? \Carbon\Carbon::parse($row->dob)->format('Y-m-d') : '-')
            ->addColumn('action', function ($row) {
                $html = '<div class="dropdown d-inline-block">';
                $html .= '<button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">';
                $html .= '<i class="ri-more-fill align-middle"></i>';
                $html .= '</button>';
                $html .= '<ul class="dropdown-menu dropdown-menu-end">';
                $html .= '<li><a href="' . route('users.show', $row->id) . '" class="dropdown-item"><i class="ri-eye-fill text-muted me-2 align-bottom"></i> View</a></li>';
                $html .= '<li><a href="' . route('users.edit', $row->id) . '" class="dropdown-item"><i class="ri-pencil-fill text-muted me-2 align-bottom"></i> Edit</a></li>';
                $html .= '<li class="dropdown-divider"></li>';
                $html .= '<li><a href="javascript:void(0)" data-id="' . $row->id . '" data-url="' . route('users.destroy', $row->id) . '" class="dropdown-item remove-item-btn delete-btn"><i class="ri-delete-bin-fill text-muted me-2 align-bottom"></i> Delete</a></li>';
                $html .= '</ul>';
                $html .= '</div>';
                return $html;
            })
            ->editColumn("created_at", fn($row) => $row->created_at->format('Y-m-d H:i'))
            ->rawColumns(['id', 'action'])
            ->make(true);
    }

    return view('admin.users.index');
}





    public function create()
    {


        $cities=City::all();
        return view('admin.users.create',compact('cities'));
    }

    public function store(Request $request)
    {


        try {
            $validated = $request->all();



            User::create($validated);

            return redirect()->route('users.index')->with([
                "message" =>  __('messages.success'),
                "icon" => "success",
            ]);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function show(User $user)
    {

        return view('admin.users.show', compact("user"));
    }

    public function edit(User $user)
    {
        //check permission




        // $roles = Role::all()->pluck('id', 'name')->toArray();
        // if (!auth()->user()->hasRole('super-admin')) {
        //     $roles = Arr::except($roles, 'super-admin');
        // }

        // $roles = array_flip($roles);
        return view('admin.users.edit', compact("user"));
    }

    public function update(Request $request, User $user)
    {
        //check permission


        try {


            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
            ]);

            $user->update($validated);


            return redirect()->route('users.index')->with([
                "message" =>  __('messages.update_message'),
                "icon" => "success",
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                "message" =>  $th->getMessage(),
                "icon" => "error",
            ]);
        }
    }

    public function destroy(Request $request, User $user)
    {
        //check permission


        if ($request->ajax()) {
            Log::info("ajax request");
            $user->delete();
            return 1;
        }
        Log::info("not ajax request");
        $user->delete();

        return 1;
    }

    public function export()
    {


        // get the heading of your file from the table or you can created your own heading
        $table = "users";
        $headers = Schema::getColumnListing($table);

        // query to get the data from the table
        $query = User::all();

        // create file name
        $fileName = "user_export_" .  date('Y-m-d_h:i_a') . ".xlsx";

        return Excel::download(new GeneralExport($query, $headers), $fileName);
    }
}
