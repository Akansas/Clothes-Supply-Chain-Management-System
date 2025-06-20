@extends('layouts.app', ['activePage' => 'retailer_dashboard', 'title' => 'Retailer Dashboard', 'navName' => 'Retailer Dashboard', 'activeButton' => 'retailer'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Inventory Summary Card -->
        <div class="col-lg-3 col-sm-6">
            <div class="card card-stats">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center icon-warning">
                                <i class="nc-icon nc-box text-warning"></i>
                            </div>
                        </div>
                        <div class="col-7 d-flex align-items-center">
                            <div class="numbers">
                                <p class="card-category">Inventory</p>
                                <p class="card-title">1,250</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sales Analytics Card -->
        <div class="col-lg-3 col-sm-6">
            <div class="card card-stats">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center icon-success">
                                <i class="nc-icon nc-chart-bar-32 text-success"></i>
                            </div>
                        </div>
                        <div class="col-7 d-flex align-items-center">
                            <div class="numbers">
                                <p class="card-category">Sales</p>
                                <p class="card-title">$8,400</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Orders Card -->
        <div class="col-lg-3 col-sm-6">
            <div class="card card-stats">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center icon-danger">
                                <i class="nc-icon nc-cart-simple text-danger"></i>
                            </div>
                        </div>
                        <div class="col-7 d-flex align-items-center">
                            <div class="numbers">
                                <p class="card-category">Orders</p>
                                <p class="card-title">32</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Recommendations Card -->
        <div class="col-lg-3 col-sm-6">
            <div class="card card-stats">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center icon-primary">
                                <i class="nc-icon nc-bulb-63 text-primary"></i>
                            </div>
                        </div>
                        <div class="col-7 d-flex align-items-center">
                            <div class="numbers">
                                <p class="card-category">Recommendations</p>
                                <p class="card-title">Restock T-Shirts</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Orders Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Order History</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class=" text-primary">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1001</td>
                                    <td>2024-06-01</td>
                                    <td>Shipped</td>
                                    <td>$250</td>
                                </tr>
                                <tr>
                                    <td>1002</td>
                                    <td>2024-06-03</td>
                                    <td>Pending</td>
                                    <td>$180</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Recommendations Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Personalized Recommendations</h4>
                </div>
                <div class="card-body">
                    <ul>
                        <li>Restock T-Shirts (Low Inventory)</li>
                        <li>Promote Summer Collection</li>
                        <li>Review sales of new arrivals</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 