@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Create Retail Store Profile</h4>
                    <p class="card-category">Set up your retail store information</p>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('retailer.profile.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="store_name">Store Name *</label>
                                    <input type="text" class="form-control @error('store_name') is-invalid @enderror" 
                                           id="store_name" name="store_name" value="{{ old('store_name') }}" required>
                                    @error('store_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone Number *</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="opening_time">Opening Time *</label>
                                    <input type="time" class="form-control @error('opening_time') is-invalid @enderror" 
                                           id="opening_time" name="opening_time" value="{{ old('opening_time', '08:00') }}" required>
                                    @error('opening_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="closing_time">Closing Time *</label>
                                    <input type="time" class="form-control @error('closing_time') is-invalid @enderror" 
                                           id="closing_time" name="closing_time" value="{{ old('closing_time', '20:00') }}" required>
                                    @error('closing_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address">Store Address *</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Create Store Profile
                            </button>
                            <a href="{{ route('retailer.dashboard') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (!form) return;
    function to24Hour(timeStr) {
        if (!timeStr) return '';
        // If already in H:i:s format, return as is
        if (/^\d{2}:\d{2}:\d{2}$/.test(timeStr)) return timeStr;
        // If in H:i format, add seconds
        if (/^\d{2}:\d{2}$/.test(timeStr)) return timeStr + ':00';
        // If in 12-hour format with AM/PM
        let [time, modifier] = timeStr.split(' ');
        let [hours, minutes] = time.split(':');
        hours = parseInt(hours, 10);
        if (modifier === 'PM' && hours < 12) hours += 12;
        if (modifier === 'AM' && hours === 12) hours = 0;
        return (hours < 10 ? '0' : '') + hours + ':' + minutes + ':00';
    }
    form.addEventListener('submit', function(e) {
        let opening = document.getElementById('opening_time');
        let closing = document.getElementById('closing_time');
        if (opening && opening.value && !opening.value.match(/^\d{2}:\d{2}:\d{2}$/)) opening.value = to24Hour(opening.value);
        if (closing && closing.value && !closing.value.match(/^\d{2}:\d{2}:\d{2}$/)) closing.value = to24Hour(closing.value);
    });
});
</script>
@endsection 