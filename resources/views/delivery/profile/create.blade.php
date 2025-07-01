@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Create Delivery Partner Profile</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('delivery.profile.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_name">Company Name *</label>
                                    <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                           id="company_name" name="company_name" value="{{ old('company_name') }}" required>
                                    @error('company_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_person">Contact Person *</label>
                                    <input type="text" class="form-control @error('contact_person') is-invalid @enderror" 
                                           id="contact_person" name="contact_person" value="{{ old('contact_person') }}" required>
                                    @error('contact_person')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone Number *</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address">Address *</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                            @error('address')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="vehicle_type">Vehicle Type *</label>
                                    <select class="form-control @error('vehicle_type') is-invalid @enderror" 
                                            id="vehicle_type" name="vehicle_type" required>
                                        <option value="">Select Vehicle Type</option>
                                        <option value="Motorcycle" {{ old('vehicle_type') == 'Motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                                        <option value="Car" {{ old('vehicle_type') == 'Car' ? 'selected' : '' }}>Car</option>
                                        <option value="Van" {{ old('vehicle_type') == 'Van' ? 'selected' : '' }}>Van</option>
                                        <option value="Truck" {{ old('vehicle_type') == 'Truck' ? 'selected' : '' }}>Truck</option>
                                        <option value="Bicycle" {{ old('vehicle_type') == 'Bicycle' ? 'selected' : '' }}>Bicycle</option>
                                    </select>
                                    @error('vehicle_type')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="vehicle_number">Vehicle Number *</label>
                                    <input type="text" class="form-control @error('vehicle_number') is-invalid @enderror" 
                                           id="vehicle_number" name="vehicle_number" value="{{ old('vehicle_number') }}" required>
                                    @error('vehicle_number')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="license_number">License Number *</label>
                                    <input type="text" class="form-control @error('license_number') is-invalid @enderror" 
                                           id="license_number" name="license_number" value="{{ old('license_number') }}" required>
                                    @error('license_number')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Service Areas *</label>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="service_areas[]" value="Downtown" 
                                               {{ in_array('Downtown', old('service_areas', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label">Downtown</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="service_areas[]" value="North Side" 
                                               {{ in_array('North Side', old('service_areas', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label">North Side</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="service_areas[]" value="South Side" 
                                               {{ in_array('South Side', old('service_areas', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label">South Side</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="service_areas[]" value="East Side" 
                                               {{ in_array('East Side', old('service_areas', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label">East Side</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="service_areas[]" value="West Side" 
                                               {{ in_array('West Side', old('service_areas', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label">West Side</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="service_areas[]" value="Suburbs" 
                                               {{ in_array('Suburbs', old('service_areas', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label">Suburbs</label>
                                    </div>
                                </div>
                            </div>
                            @error('service_areas')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="availability">Availability *</label>
                                    <select class="form-control @error('availability') is-invalid @enderror" 
                                            id="availability" name="availability" required>
                                        <option value="">Select Availability</option>
                                        <option value="full_time" {{ old('availability') == 'full_time' ? 'selected' : '' }}>Full Time</option>
                                        <option value="part_time" {{ old('availability') == 'part_time' ? 'selected' : '' }}>Part Time</option>
                                        <option value="on_demand" {{ old('availability') == 'on_demand' ? 'selected' : '' }}>On Demand</option>
                                    </select>
                                    @error('availability')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="experience_years">Years of Experience *</label>
                                    <input type="number" class="form-control @error('experience_years') is-invalid @enderror" 
                                           id="experience_years" name="experience_years" value="{{ old('experience_years') }}" 
                                           min="0" max="50" required>
                                    @error('experience_years')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary">
                                Create Profile
                            </button>
                            <a href="{{ route('delivery.dashboard') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 