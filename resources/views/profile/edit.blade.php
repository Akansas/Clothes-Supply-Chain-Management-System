@extends('layouts.app', ['activePage' => 'user', 'title' => 'Light Bootstrap Dashboard Laravel by Creative Tim & UPDIVISION', 'navName' => 'User Profile', 'activeButton' => 'laravel'])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="section-image">
                <!--   you can change the color of the filter page using: data-color="blue | purple | green | orange | red | rose " -->
                <div class="row justify-content-center">
                    <div class="card col-md-8">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h3 class="mb-0">{{ __('Edit Profile') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @php
                                $profileUser = isset($user) ? $user : auth()->user();
                                $isReadonly = isset($readonly) ? $readonly : false;
                            @endphp
                            <form method="post" action="{{ route('profile.update') }}" autocomplete="off"
                                enctype="multipart/form-data">
                                @csrf
                                @method('patch')

                                <h6 class="heading-small text-muted mb-4">{{ __('User information') }}</h6>
                                
                                @include('alerts.success')
                                @include('alerts.error_self_update', ['key' => 'not_allow_profile'])
        
                                <div class="pl-lg-4">
                                    <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-name">
                                            <i class="w3-xxlarge fa fa-user"></i>{{ __('Name') }}
                                        </label>
                                        <input type="text" name="name" id="input-name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" placeholder="{{ __('Name') }}" value="{{ old('name', $profileUser->name) }}" required autofocus @if($isReadonly) readonly @endif>
        
                                        @include('alerts.feedback', ['field' => 'name'])
                                    </div>
                                    <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-email"><i class="w3-xxlarge fa fa-envelope-o"></i>{{ __('Email') }}</label>
                                        <input type="email" name="email" id="input-email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __('Email') }}" value="{{ old('email', $profileUser->email) }}" required @if($isReadonly) readonly @endif>
        
                                        @include('alerts.feedback', ['field' => 'email'])
                                    </div>
                                    @if(!$isReadonly)
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-default mt-4">{{ __('Save') }}</button>
                                    </div>
                                    @endif
                                </div>
                            </form>
                            @if(isset($supplierProfile) && $supplierProfile)
                                <div class="card mb-4">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0">Supplier Profile Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <div class="col-md-6"><strong>Company Name:</strong></div>
                                            <div class="col-md-6">{{ $supplierProfile->company_name }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-6"><strong>Contact Person:</strong></div>
                                            <div class="col-md-6">{{ $supplierProfile->contact_person }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-6"><strong>Email:</strong></div>
                                            <div class="col-md-6">{{ $supplierProfile->email }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-6"><strong>Phone:</strong></div>
                                            <div class="col-md-6">{{ $supplierProfile->phone }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-6"><strong>Address:</strong></div>
                                            <div class="col-md-6">{{ $supplierProfile->address }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-6"><strong>Status:</strong></div>
                                            <div class="col-md-6">{{ ucfirst($supplierProfile->status) }}</div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <a href="{{ route('supplier.profile.edit') }}" class="btn btn-warning btn-block">Edit</a>
                                            </div>
                                            <div class="col-md-6">
                                                <form method="POST" action="{{ route('supplier.profile.destroy') }}" onsubmit="return confirm('Are you sure you want to delete your supplier profile? This action cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-block">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <hr class="my-4" />
                            @if(!$isReadonly)
                            <form method="post" action="{{ route('profile.password') }}">
                                @csrf
                                @method('patch')
        
                                <h6 class="heading-small text-muted mb-4">{{ __('Password') }}</h6>
        
                                @include('alerts.success', ['key' => 'password_status'])
                                @include('alerts.error_self_update', ['key' => 'not_allow_password'])
        
                                <div class="pl-lg-4">
                                    <div class="form-group{{ $errors->has('old_password') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-current-password">
                                            <i class="w3-xxlarge fa fa-eye-slash"></i>{{ __('Current Password') }}
                                        </label>
                                        <input type="password" name="old_password" id="input-current-password" class="form-control{{ $errors->has('old_password') ? ' is-invalid' : '' }}" placeholder="{{ __('Current Password') }}" value="" required>
        
                                        @include('alerts.feedback', ['field' => 'old_password'])
                                    </div>
                                    <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-password">
                                            <i class="w3-xxlarge fa fa-eye-slash"></i>{{ __('New Password') }}
                                        </label>
                                        <input type="password" name="password" id="input-password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ __('New Password') }}" value="" required>
        
                                        @include('alerts.feedback', ['field' => 'password'])
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label" for="input-password-confirmation">
                                            <i class="w3-xxlarge fa fa-eye-slash"></i>{{ __('Confirm New Password') }}
                                        </label>
                                        <input type="password" name="password_confirmation" id="input-password-confirmation" class="form-control" placeholder="{{ __('Confirm New Password') }}" value="" required>
                                    </div>
        
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-default mt-4">{{ __('Change password') }}</button>
                                    </div>
                                </div>
                            </form>
                            @endif
                            @if(!$isReadonly)
                            <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-block mt-4">Delete Account</button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection