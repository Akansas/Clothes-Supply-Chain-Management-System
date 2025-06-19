<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SD Admin in Laravel</title>
    <link href="{{assets('vendor/bootstrap/css/bootstrap.min.css')}}"
    rel="stylesheet">
    <link href="{{assets('css/style.css')}}"rel="stylesheet">
</head>
<body class="sb-nav-fixed">
@include('partials.navbar') {{--Top navbar--}}
<div id="layoutSidenav">
@include('partials.sidebar') {{--Sidebar--}}
<div id="layoutSidenav_content">
<main>
@yield('content')
</main>
<div>
</div>
<script src="{{asset('vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
</body>
</html>