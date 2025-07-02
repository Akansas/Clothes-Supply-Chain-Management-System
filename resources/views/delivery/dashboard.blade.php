@extends('layouts.app')

@section('content')
<div id="delivery-dashboard-app"
     data-role="{{ $role }}"
     data-user-id="{{ auth()->id() }}">
</div>
@endsection 