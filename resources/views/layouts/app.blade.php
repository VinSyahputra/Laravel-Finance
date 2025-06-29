<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Finance App')</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />

    @stack('styles')

    {{-- Livewire styles --}}
    @livewireStyles

    <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}" data-navigate-track></script>
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}" data-navigate-track></script>
    <script src="{{ asset('assets/js/sidebarmenu.js') }}" data-navigate-track></script>
    <script src="{{ asset('assets/js/app.min.js') }}" data-navigate-track></script>
    <script src="{{ asset('assets/js/functions.js') }}" data-navigate-track></script>
    <script src="{{ asset('assets/libs/simplebar/dist/simplebar.js') }}" data-navigate-track></script>
    @stack('scripts')
    {{-- Livewire scripts --}}
    @livewireScripts
</head>

<body id="{{ str_replace('.', '-', Route::currentRouteName()) }}">
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        @include('partials.sidebar')

        <div class="body-wrapper">
            @include('partials.header')

            {{-- Main content area where Livewire components or Blade views render --}}
            @yield('content')
        </div>

    </div>




</body>

</html>
