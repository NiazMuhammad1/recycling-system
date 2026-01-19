<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="{{ asset('vendor/adminlte/css/adminlte.min.css') }}">
    <script src="{{ asset('vendor/adminlte/js/adminlte.min.js') }}"></script>

    <!-- Optional -->
    @stack('styles')
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

    @include('layouts.navbar')
    @include('layouts.sidebar')

    <div class="content-wrapper">
        <section class="content p-3">
            @yield('content')
        </section>
    </div>

</div>

<!-- jQuery FIRST -->
<script src="{{ asset('vendor/adminlte/plugins/jquery/jquery.min.js') }}"></script>

<!-- Bootstrap -->
<script src="{{ asset('vendor/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- AdminLTE -->
<script src="{{ asset('vendor/adminlte/js/adminlte.min.js') }}"></script>

@stack('scripts')
</body>
</html>
