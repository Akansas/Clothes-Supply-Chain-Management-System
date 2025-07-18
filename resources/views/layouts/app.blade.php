<!-- 
=========================================================
 Light Bootstrap Dashboard - v2.0.1
=========================================================

 Product Page: https://www.creative-tim.com/product/light-bootstrap-dashboard
 Copyright 2019 Creative Tim (https://www.creative-tim.com) & Updivision (https://www.updivision.com)
 Licensed under MIT (https://github.com/creativetimofficial/light-bootstrap-dashboard/blob/master/LICENSE)

 Coded by Creative Tim & Updivision

=========================================================

 The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.  -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'GenZ FashionZ Supply Chain' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/nucleo-icons@2.0.0/css/nucleo-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa !important; }
        .navbar-brand { font-weight: bold; }
        .container { margin-top: 40px; }
    </style>
    @vite('resources/js/app.js')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">GenZ FashionZ Supply Chain</a>
        <div class="collapse navbar-collapse justify-content-end align-items-center">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Dashboard</a></li>
                    @if(auth()->check() && auth()->user()->role && auth()->user()->role->name === 'admin')
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
                    @endif
                    <li class="nav-item"><a class="nav-link" href="{{ route('profile.edit') }}">My Account</a></li>
                    <li class="nav-item d-flex align-items-center">
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link text-danger m-0 p-0" style="background:none; border:none;">Logout</button>
                        </form>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
<div id="app">
    <div class="container" style="padding-top: 80px;">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('impersonate'))
            <div style="background: #ffc107; color: #222; padding: 12px; text-align: center; position: relative; z-index: 1000;">
                <strong>Impersonation Mode:</strong> You are impersonating another user.
                <form id="stop-impersonate-form" action="{{ route('admin.stopImpersonate') }}" method="POST" style="display:inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger ms-3">Stop Impersonating</button>
                </form>
            </div>
            <script>
                document.getElementById('stop-impersonate-form').addEventListener('submit', function(e) {
                    setTimeout(function() {
                        window.location.href = '/admin/dashboard';
                    }, 300);
                });
            </script>
        @endif
        
        @yield('content')
    </div>
{{-- Shared Support Footer --}}
@include('layouts.footer.support')
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Force Welcome Dashboard Styles - JavaScript Backup -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Force background color
    document.body.style.backgroundColor = '#f8f9fa';
    
    // Force hero section styles
    const heroSection = document.querySelector('.hero-section');
    if (heroSection) {
        heroSection.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
        heroSection.style.color = 'white';
        heroSection.style.padding = '80px 0 60px 0';
        heroSection.style.borderRadius = '1rem';
        heroSection.style.marginBottom = '2rem';
    }
    
    // Force feature icons
    const featureIcons = document.querySelectorAll('.feature-icon');
    featureIcons.forEach(icon => {
        icon.style.fontSize = '2.5rem';
        icon.style.color = '#764ba2';
        icon.style.marginBottom = '1rem';
    });
    
    // Force role icons
    const roleIcons = document.querySelectorAll('.role-icon');
    roleIcons.forEach(icon => {
        icon.style.fontSize = '2rem';
        icon.style.color = '#667eea';
        icon.style.marginBottom = '0.5rem';
    });
    
    // Force hover effects
    const featureCards = document.querySelectorAll('.card.feature-hover');
    featureCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.04)';
            this.style.boxShadow = '0 8px 32px rgba(118,75,162,0.18), 0 1.5px 6px rgba(0,0,0,0.08)';
            this.style.zIndex = '2';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = '';
            this.style.boxShadow = '';
            this.style.zIndex = '';
        });
    });
    
    const roleCards = document.querySelectorAll('.role-hover');
    roleCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.07)';
            this.style.boxShadow = '0 8px 32px rgba(102,126,234,0.18), 0 1.5px 6px rgba(0,0,0,0.08)';
            this.style.zIndex = '2';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = '';
            this.style.boxShadow = '';
            this.style.zIndex = '';
        });
    });
});
</script>
</body>
</html>