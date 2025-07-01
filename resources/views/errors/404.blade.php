@extends('layouts.app')
@section('title', 'Page Not Found')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1 class="display-1 text-warning">404</h1>
            <h2 class="mb-4">Page Not Found</h2>
            <p class="lead">Sorry, the page you are looking for could not be found.</p>
            <a href="/" class="btn btn-primary mt-3">Go Home</a>
        </div>
    </div>
</div>
@endsection 