<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light" data-sidebar="dark"
    data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default"
    data-theme-colors="default">

<head>
    <meta charset="utf-8" />
    <title>@yield('title') | Sages Home Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Administration Sages Home - gestion des résidences et réservations" name="description" />
    <meta content="Ticafrique" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    {{-- <link rel="shortcut icon" href="{{ URL::asset('build/images/favicon.ico') }}"> --}}
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('assets/img/favicon/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('assets/img/favicon/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('assets/img/favicon/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/favicon/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('assets/img/favicon/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('assets/img/favicon/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('assets/img/favicon/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('assets/img/favicon/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/favicon/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/img/favicon/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('assets/img/favicon/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/img/favicon/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('assets/img/favicon/ms-icon-144x144.png') }}">
    <meta name="theme-color" content="#37542A">
    @include('backend.layouts.head-css')
</head>

<body>
    <div id="sh-progress" aria-hidden="true"></div>
    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('backend.layouts.topbar')
        @include('backend.layouts.sidebar')
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @include('sweetalert::alert')
                    @yield('content')
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            @include('backend.layouts.footer')
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    {{-- @include('backend.layouts.customizer') --}}

    <!-- JAVASCRIPT -->
    @include('backend.layouts.vendor-scripts')

    {{-- Messages flash (success / error / warning / info) en notification --}}
    @php
        $flashes = collect(['success', 'error', 'warning', 'info'])
            ->mapWithKeys(fn ($type) => [$type => session($type)])
            ->filter(fn ($msg) => is_string($msg) && $msg !== '');
        if ($errors->any() && !$flashes->has('error')) {
            $flashes->put('error', $errors->count() > 1
                ? 'Veuillez corriger les ' . $errors->count() . ' champs signalés.'
                : $errors->first());
        }
    @endphp
    @if ($flashes->isNotEmpty())
        <script>
            (function () {
                if (!window.Swal) return;
                var toast = Swal.mixin({
                    toast: true, position: 'top-end', showConfirmButton: false, showCloseButton: true,
                    timer: 5000, timerProgressBar: true
                });
                var queue = @json($flashes);
                Object.keys(queue).reduce(function (p, type) {
                    return p.then(function () { return toast.fire({ icon: type, title: queue[type] }); });
                }, Promise.resolve());
            })();
        </script>
    @endif
</body>

</html>
