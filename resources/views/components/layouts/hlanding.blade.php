<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>HRGA Information System</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="{{ asset('favicon.ico') }}" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Red+Rose:wght@600;700&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">

    <!-- Libraries Stylesheet -->
    <link href="{{ url('landing-page/lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ url('landing-page/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ url('landing-page/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{ url('landing-page/css/style.css') }}" rel="stylesheet">
</head>

<body>

    @yield('landing')

        <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{('landing-page/lib/wow/wow.min.js')}}"></script>
    <script src="{{('landing-page/lib/easing/easing.min.js')}}"></script>
    <script src="{{('landing-page/lib/waypoints/waypoints.min.js')}}"></script>
    <script src="{{('landing-page/lib/counterup/counterup.min.js')}}"></script>
    <script src="{{('landing-page/lib/owlcarousel/owl.carousel.min.js')}}"></script>

    <!-- Template Javascript -->
    <script src="{{('landing-page/js/main.js')}}"></script>

 <x-layouts.flanding />
</body>
