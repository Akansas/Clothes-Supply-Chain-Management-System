@extends('layouts.app', ['activePage' => 'user', 'title' => 'Edit Profile', 'navName' => 'User Profile', 'activeButton' => 'laravel'])

@section('content')
    <div class="content">
        <div class="container-fluid">
                <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Edit Profile</h4>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                @include('alerts.success')

                                <div class="row">
                                    <div class="col-md-12 text-center mb-4">
                                        <div class="avatar-wrapper">
                                            <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="profile-avatar">
                                            <div class="avatar-controls mt-2">
                                                <label for="avatar" class="btn btn-sm btn-primary">
                                                    Change Avatar
                                                    <input type="file" name="avatar" id="avatar" class="d-none" accept="image/*">
                                        </label>
                                                @if(auth()->user()->avatar)
                                                    <a href="{{ route('profile.avatar.delete') }}" class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Are you sure you want to remove your avatar?')">
                                                        Remove Avatar
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 pr-1">
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}">
                                            @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-1">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}">
                                            @error('email')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 pr-1">
                                        <div class="form-group">
                                            <label>Phone</label>
                                            <input type="tel" name="phone" class="form-control" value="{{ old('phone', auth()->user()->phone) }}">
                                            @error('phone')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-1">
                                        <div class="form-group">
                                            <label>Address</label>
                                            <input type="text" name="address" class="form-control" value="{{ old('address', auth()->user()->address) }}">
                                            @error('address')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                    <div class="form-group">
                                            <label>Bio</label>
                                            <textarea name="bio" class="form-control" rows="4">{{ old('bio', auth()->user()->bio) }}</textarea>
                                            @error('bio')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    </div>
        
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title">Notification Preferences</h5>
                                            </div>
                                            <div class="card-body">
                                                @php
                                                    $preferences = auth()->user()->notification_preferences ?? ['email' => true, 'push' => true, 'chat' => true];
                                                @endphp
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="emailNotifications" 
                                                           name="notification_preferences[email]" 
                                                           {{ $preferences['email'] ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="emailNotifications">Email Notifications</label>
                                                </div>
                                                <div class="custom-control custom-switch mt-2">
                                                    <input type="checkbox" class="custom-control-input" id="pushNotifications" 
                                                           name="notification_preferences[push]" 
                                                           {{ $preferences['push'] ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="pushNotifications">Push Notifications</label>
                                                </div>
                                                <div class="custom-control custom-switch mt-2">
                                                    <input type="checkbox" class="custom-control-input" id="chatNotifications" 
                                                           name="notification_preferences[chat]" 
                                                           {{ $preferences['chat'] ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="chatNotifications">Chat Notifications</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-info btn-fill pull-right">Update Profile</button>
                                <div class="clearfix"></div>
                            </form>
                        </div>
                    </div>
                </div>
                    <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Change Password</h4>
                            </div>
                            <div class="card-body">
                            <form method="post" action="{{ route('profile.password') }}">
                                @csrf
                                @method('put')
                                @include('alerts.success', ['key' => 'password_status'])

                                <div class="form-group">
                                    <label>Current Password</label>
                                    <input type="password" name="old_password" class="form-control" required>
                                    @error('old_password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>New Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                            </div>

                                <div class="form-group">
                                    <label>Confirm New Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                            </div>

                                <button type="submit" class="btn btn-info btn-fill pull-right">Change Password</button>
                                <div class="clearfix"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
document.getElementById('avatar').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('.profile-avatar').src = e.target.result;
        };
        reader.readAsDataURL(e.target.files[0]);
    }
});

// Save notification preferences via AJAX
const notificationSwitches = document.querySelectorAll('[name^="notification_preferences"]');
notificationSwitches.forEach(switch => {
    switch.addEventListener('change', function() {
        const preferences = {
            email: document.getElementById('emailNotifications').checked,
            push: document.getElementById('pushNotifications').checked,
            chat: document.getElementById('chatNotifications').checked
        };

        fetch('{{ route('profile.notifications.update') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ notification_preferences: preferences })
        })
        .then(response => response.json())
        .then(data => {
            // Show success notification
            const notification = document.createElement('div');
            notification.className = 'alert alert-success';
            notification.textContent = data.message;
            document.querySelector('.card-body').insertBefore(notification, document.querySelector('form'));
            
            // Remove notification after 3 seconds
            setTimeout(() => notification.remove(), 3000);
        })
        .catch(error => console.error('Error:', error));
    });
});
</script>

<style>
.avatar-wrapper {
    position: relative;
    display: inline-block;
}

.profile-avatar {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.avatar-controls {
    margin-top: 10px;
}

.custom-switch {
    padding-left: 2.25rem;
}

.custom-control-input {
    position: absolute;
    left: 0;
    z-index: -1;
    width: 1.5rem;
    height: 1.5rem;
    opacity: 0;
}

.custom-control-label {
    position: relative;
    margin-bottom: 0;
    vertical-align: top;
}

.custom-control-label::before {
    position: absolute;
    top: 0.25rem;
    left: -2.25rem;
    display: block;
    width: 2rem;
    height: 1.25rem;
    content: "";
    background-color: #fff;
    border: 1px solid #adb5bd;
    border-radius: 0.625rem;
    transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.custom-control-label::after {
    position: absolute;
    top: calc(0.25rem + 2px);
    left: calc(-2.25rem + 2px);
    display: block;
    width: calc(1.25rem - 4px);
    height: calc(1.25rem - 4px);
    content: "";
    background-color: #adb5bd;
    border-radius: 0.625rem;
    transition: transform 0.15s ease-in-out, background-color 0.15s ease-in-out;
}

.custom-control-input:checked ~ .custom-control-label::before {
    color: #fff;
    border-color: #007bff;
    background-color: #007bff;
}

.custom-control-input:checked ~ .custom-control-label::after {
    background-color: #fff;
    transform: translateX(0.75rem);
}
</style>
@endpush