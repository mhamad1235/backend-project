@extends('layouts.master')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Accounts @endslot
    @slot('li_2') {{ route('accounts.index') }} @endslot
    @slot('title') Edit Account @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Edit Account</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('accounts.update', $account->id) }}" method="POST" id="account-form">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $account->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $account->phone) }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Leave blank if you don't want to change the password</small>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        </div>
                        <div class="col-md-6">
                            <label for="role_type" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role_type') is-invalid @enderror" id="role_type" name="role_type" required>
                                @foreach(App\Enums\RoleType::cases() as $role)
                                    <option value="{{ $role->value }}" @selected(old('role_type', $account->role_type->value) == $role->value)>{{ ucfirst($role->value) }}</option>
                                @endforeach
                            </select>
                            @error('role_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                     <div class="col-md-6">
    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
<select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
    @foreach(App\Enums\AccountStatus::cases() as $status)
        <option value="{{ $status->value }}"
            @selected(old('status', $account->status->value ?? '') == $status->value)>
            {{ $status->label() }}
        </option>
    @endforeach
</select>

@error('status')
    <div class="invalid-feedback">{{ $message }}</div>
@enderror

    @error('status')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

                        <div class="col-12">
                            <div class="text-end">
                                <a href="{{ route('accounts.index') }}" class="btn btn-light me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#role_type, #status').select2({
        placeholder: "Select an option",
        allowClear: true
    });
});
</script>
@endsection
