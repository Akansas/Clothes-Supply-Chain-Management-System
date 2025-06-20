<footer class="footer">
    <div class="container @auth-fluid @endauth">
        <nav>
            <ul class="footer-menu">
                <li>
                    <a href="{{ route('home') }}" class="nav-link">{{ __('Home') }}</a>
                </li>
                <li>
                    <a href="{{ route('retailer.dashboard') }}" class="nav-link">{{ __('Dashboard') }}</a>
                </li>
                <li>
                    <a href="#" class="nav-link" target="_blank">{{ __('Company') }}</a>
                </li>
            </ul>
            <p class="copyright text-center">
                ©
                <script>
                    document.write(new Date().getFullYear())
                </script>
                <a href="#">{{ __('Clothes Supply Chain') }}</a>{{ __(', all rights reserved.') }}
            </p>
        </nav>
    </div>
</footer>