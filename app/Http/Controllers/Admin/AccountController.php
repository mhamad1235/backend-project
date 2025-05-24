<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Enums\RoleType;
use App\Enums\AccountStatus;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rule;
class AccountController extends Controller
{
public function index(Request $request)
{
    if ($request->ajax()) {
        $data = Account::query()
            ->when($request->search['value'] ?? null, function ($query, $search) {
                $query->where('name', 'like', '%'.$search.'%')
                      ->orWhere('phone', 'like', '%'.$search.'%');
            });

       return DataTables::of($data)
    ->addIndexColumn()
    ->setRowClass(fn($row) => 'align-middle')
    ->addColumn('action', function ($row) {
        $editUrl = route('accounts.edit', $row->id);
        $deleteUrl = route('accounts.destroy', $row->id);

        $html = '<div class="dropdown d-inline-block">';
        $html .= '<button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">';
        $html .= '<i class="ri-more-fill align-middle"></i>';
        $html .= '</button>';
        $html .= '<ul class="dropdown-menu dropdown-menu-end">';

        // Edit button - only show if user has permission
            $html .= '<li><a class="dropdown-item edit-item-btn" href="'.$editUrl.'">';
            $html .= '<i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>';
            $html .= '<li class="dropdown-divider"></li>';


        // Delete button - only show if user has permission

            $html .= '<li><a href="javascript:void(0)" class="dropdown-item remove-item-btn delete-btn" ';
            $html .= 'data-id="'.$row->id.'" data-url="'.$deleteUrl.'">';
            $html .= '<i class="ri-delete-bin-fill text-muted me-2 align-bottom"></i> Delete</a></li>';


        // Additional actions can be added here
        // Example: View button
        /*
        if (auth()->user()->can('account_view')) {
            $html .= '<li><a class="dropdown-item view-item-btn" href="'.route('accounts.show', $row->id).'">';
            $html .= '<i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>';
        }
        */

        $html .= '</ul></div>';

        return $html;
    })
    ->editColumn('status', function($row) {
        $statusClass = $row->status->value === 'active' ? '' : '';
        return '<span class=" '.$statusClass.'">'.ucfirst($row->status->value).'</span>';
    })
    ->editColumn('role_type', fn($row) => '<span class="">'.ucfirst($row->role_type->value).'</span>')
    ->editColumn('created_at', fn($row) => $row->created_at->format('M d, Y h:i A'))
    ->rawColumns(['action', 'status', 'role_type', 'created_at'])
    ->make(true);
    }

    return view('admin.accounts.index');
}
public function create()
{
    return view('admin.accounts.create');
}

public function store(Request $request)
{

$validated = $request->validate([
    'name' => 'required|string|max:255',
    'phone' => 'required|string|max:20|unique:accounts,phone',
    'password' => 'required|string|min:8|confirmed',
    'role_type' => ['required','string',Rule::in(RoleType::values())],  // Correct enum rule for role_type
    'status' => ['required', 'string', Rule::in(AccountStatus::values())],  // Correct for status
]);
    $validated['password'] = bcrypt($validated['password']);

    Account::create($validated);

    return redirect()->route('accounts.index')
        ->with('success', 'Account created successfully.');
}

public function edit(Account $account)
{
    return view('admin.accounts.edit', compact('account'));
}

public function update(Request $request, Account $account)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20|unique:accounts,phone,' . $account->id,
        'password' => 'nullable|string|min:8|confirmed',
        'role_type' => 'required|string|in:' . implode(',', RoleType::values()),
        'status' => 'required|string|in:' . implode(',', AccountStatus::values()),
    ]);

    if ($request->filled('password')) {
        $validated['password'] = bcrypt($validated['password']);
    } else {
        unset($validated['password']);
    }

    $account->update($validated);

    return redirect()->route('accounts.index')
        ->with('success', 'Account updated successfully.');
}

public function destroy(Account $account)
{
    $account->delete();

    return response()->json([
        'success' => true,
        'message' => 'Account deleted successfully.'
    ]);
}
}
