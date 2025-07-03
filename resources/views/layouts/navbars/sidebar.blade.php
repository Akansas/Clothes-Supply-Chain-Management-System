
<div class="sidebar" data-image="{{ asset('light-bootstrap/img/sidebar-5.jpg') }}">
    <!--
Tip 1: You can change the color of the sidebar using: data-color="purple | blue | green | orange | red"

Tip 2: you can also add an image using data-image tag
-->
    <div class="sidebar-wrapper">
        <div class="logo">
            <a href="#" class="simple-text">
                {{ __("Retailer Panel") }}
            </a>
        </div>
        <ul class="nav">
            <li class="nav-item @if($activePage == 'retailer_dashboard') active @endif">
                <a class="nav-link" href="{{ route('retailer.dashboard') }}">
                    <i class="nc-icon nc-shop"></i>
                    <p>Retailer Dashboard</p>
                </a>
            </li>
            <li class="nav-item {{ $activePage == 'inventory' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('retailer.inventory') }}">
                    <i class="nc-icon nc-box"></i>
                    <p>Inventory</p>
                </a>
            </li>
            <li class="nav-item {{ $activePage == 'chat' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('chat.index') }}">
                    <i class="nc-icon nc-chat-33"></i>
                    <p>Chat</p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('orders.index') }}">
                    <i class="nc-icon nc-cart-simple"></i>
                    <p>Orders</p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('retailer.analytics') }}">
                    <i class="nc-icon nc-chart-bar-32"></i>
                    <p>Sales Analytics</p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('retailer.recommendations') }}">
                    <i class="nc-icon nc-bulb-63"></i>
                    <p>Recommendations</p>
                </a>
            </li>
            <li class="nav-item {{ $activePage == 'profile' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('profile.edit') }}">
                    <i class="nc-icon nc-single-02"></i>
                    <p>Profile/Settings</p>
                </a>
            </li>
            <li class="nav-item {{ $activePage == 'notifications' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('notifications.index') }}">
                    <i class="nc-icon nc-bell-55"></i>
                    <p>Notifications <span class="badge badge-danger" id="notification-bell" style="display: none;"></span></p>
                </a>
            </li>
        </ul>
    </div>
</div>

