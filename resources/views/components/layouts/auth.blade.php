<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - {{config('app.name')}}</title>
    <link rel="stylesheet" href="{{url('assets/css/main/app.css')}}">
    <link rel="stylesheet" href="{{url('assets/css/pages/auth.css')}}">
    <link rel="shortcut icon" href="{{url('assets/images/logo/favicon.svg')}}" type="image/x-icon">
    <link rel="shortcut icon" href="{{url('assets/images/logo/favicon.png')}}" type="image/png">
</head>

<body>
<div id="auth">
    @yield('content')
</div>


<script>
    @if (session()->has('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 3000
    })
    @elseif (session()->has('error'))
    Swal.fire({
        icon: 'error',
        text: 'Failed!',
        title: '{{ session('error') }}',
        showConfirmButton: false,
        timer: 3000
    })
    @endif
</script>
</body>

</html>
