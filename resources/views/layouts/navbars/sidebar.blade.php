<div class="sidebar" data-image="{{ asset('light-bootstrap/img/sidebar-5.jpg') }}">
    <!--
Tip 1: You can change the color of the sidebar using: data-color="purple | blue | green | orange | red"

Tip 2: you can also add an image using data-image tag
-->
    <div class="sidebar-wrapper">
        <div class="logo">
            <a href="http://www.creative-tim.com" class="simple-text">
                {{ __("Creative Tim") }}
            </a>
        </div>
        <ul class="nav">
            <li class="nav-item @if($activePage == 'dashboard') active @endif">
                <a class="nav-link" href="{{route('dashboard')}}">
                    <i class="nc-icon nc-chart-pie-35"></i>
                    <p>{{ __("Dashboard") }}</p>
                </a>
            </li>
            @auth
                @php $role = auth()->user()->role ? auth()->user()->role->name : null; @endphp
                @if($role === 'customer')
                    <li class="nav-item"><a class="nav-link" href="#">Browse Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Place Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Order Tracking</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Chat with Vendors</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Order History</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Notifications</a></li>
                @elseif($role === 'retailer')
                    <li class="nav-item"><a class="nav-link" href="#">Browse Vendor Listings</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Place Bulk Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Order Status Tracking</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Chat with Vendors</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Reports</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Notifications</a></li>
                @elseif($role === 'vendor')
                    <li class="nav-item"><a class="nav-link" href="#">Manage Product Listings</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Bulk Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Inventory Sync</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Sales Analytics</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Chat</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Reports</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Notifications</a></li>
                @elseif($role === 'manufacturer')
                    <li class="nav-item"><a class="nav-link" href="#">Raw Material Inventory</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Production Management</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Send Finished Goods</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Order Requests</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Chat</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Analytics</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Notifications</a></li>
                @elseif($role === 'warehouse_manager')
                    <li class="nav-item"><a class="nav-link" href="#">Inventory Management</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Order Queue</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Assign Deliveries</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Low Stock Alerts</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Chat</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Reports</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Notifications</a></li>
                @elseif($role === 'delivery_personnel')
                    <li class="nav-item"><a class="nav-link" href="#">Assigned Deliveries</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Route Optimization</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Update Delivery Status</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Chat</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Performance Analytics</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Notifications</a></li>
                @elseif($role === 'raw_material_supplier')
                    <li class="nav-item"><a class="nav-link" href="#">Manage Raw Material Inventory</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">View Stock Requests</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Order Fulfillment</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Payments</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Chat with Manufacturers</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Reports</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Notifications</a></li>
                @elseif($role === 'admin')
                    <li class="nav-item"><a class="nav-link" href="#">User & Role Management</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">System Logs</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Reports & Analytics</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Validation & Audits</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Notifications</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Chat Oversight</a></li>
                @endif
            @endauth
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#laravelExamples" @if($activeButton =='laravel') aria-expanded="true" @endif>
                    <i>
                        <img src="{{ asset('light-bootstrap/img/laravel.svg') }}" style="width:25px">
                    </i>
                    <p>
                        {{ __('Laravel example') }}
                        <b class="caret"></b>
                    </p>
                </a>
                <div class="collapse @if($activeButton =='laravel') show @endif" id="laravelExamples">
                    <ul class="nav">
                        <li class="nav-item @if($activePage == 'user') active @endif">
                            <a class="nav-link" href="{{route('profile.edit')}}">
                                <i class="nc-icon nc-single-02"></i>
                                <p>{{ __("User Profile") }}</p>
                            </a>
                        </li>
                        <li class="nav-item @if($activePage == 'user-management') active @endif">
                            <a class="nav-link" href="{{route('user.index')}}">
                                <i class="nc-icon nc-circle-09"></i>
                                <p>{{ __("User Management") }}</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item @if($activePage == 'table') active @endif">
                <a class="nav-link" href="{{route('page.index', 'table')}}">
                    <i class="nc-icon nc-notes"></i>
                    <p>{{ __("Table List") }}</p>
                </a>
            </li>
            <li class="nav-item @if($activePage == 'typography') active @endif">
                <a class="nav-link" href="{{route('page.index', 'typography')}}">
                    <i class="nc-icon nc-paper-2"></i>
                    <p>{{ __("Typography") }}</p>
                </a>
            </li>
            <li class="nav-item @if($activePage == 'icons') active @endif">
                <a class="nav-link" href="{{route('page.index', 'icons')}}">
                    <i class="nc-icon nc-atom"></i>
                    <p>{{ __("Icons") }}</p>
                </a>
            </li>
            <li class="nav-item @if($activePage == 'maps') active @endif">
                <a class="nav-link" href="{{route('page.index', 'maps')}}">
                    <i class="nc-icon nc-pin-3"></i>
                    <p>{{ __("Maps") }}</p>
                </a>
            </li>
            <li class="nav-item @if($activePage == 'notifications') active @endif">
                <a class="nav-link" href="{{route('page.index', 'notifications')}}">
                    <i class="nc-icon nc-bell-55"></i>
                    <p>{{ __("Notifications") }}</p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active bg-danger" href="{{route('page.index', 'upgrade')}}">
                    <i class="nc-icon nc-alien-33"></i>
                    <p>{{ __("Upgrade to PRO") }}</p>
                </a>
            </li>
        </ul>
    </div>
</div>
