@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>{{ $title ? "$title | " . config('app.name') : config('app.name') }}</title>

    <!-- Fonts -->
    <link
        href="https://fonts.bunny.net"
        rel="preconnect"
    >
    <link
        href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
        rel="stylesheet"
    />
    @stack('fonts')

    <!-- Styles -->
    @livewireStyles
    @vite('resources/css/app.css')
    @stack('styles')

    <!-- Head Scripts -->
    @vite('resources/js/app.js')
    @stack('head-scripts')
</head>

<body class="min-h-screen font-sans antialiased">
    <main>
        {{ $slot }} <!-- Content from Livewire component will be injected here -->
    </main>
    {{-- <livewire:search /> --}}
    {{-- <livewire:search-form /> --}}
    <!-- Body Scripts -->
    @livewireScriptConfig
    @stack('body-scripts')
</body>

</html>
