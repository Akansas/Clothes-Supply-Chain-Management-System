@if(auth()->check() && auth()->user()->hasRole('manufacturer'))
    <li class="nav-item">
        <a class="nav-link" href="{{ route('manufacturer.profile') }}">Manufacturer Profile</a>
    </li>
@endif 