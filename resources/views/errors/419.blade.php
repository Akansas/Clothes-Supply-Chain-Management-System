@extends('layouts.app')
@section('title', 'Session Expired')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1 class="display-1 text-info">419</h1>
            <h2 class="mb-4">Session Expired</h2>
            <p class="lead">Your session has expired. Please refresh the page and try again.</p>
            <a href="/login" class="btn btn-primary mt-3">Login Again</a>
        </div>
    </div>
</div>
@endsection 