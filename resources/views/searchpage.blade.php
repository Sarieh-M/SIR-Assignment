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
    <header class="bg-slate-800 text-white shadow-md">
        <div class="container mx-auto flex justify-between p-4">
            <h1 class="text-lg font-bold">Question App</h1>
            <nav class="flex space-x-4">
                <a
                    class="{{ request()->routeIs('search') ? 'underline' : '' }} transition hover:text-cyan-100"
                    href="{{ route('search') }}"
                >
                    Search Questions
                </a>
                <a
                    class="{{ request()->routeIs('add-question') ? 'underline' : '' }} transition hover:text-cyan-100"
                    href="{{ route('add') }}"
                >
                    Add Question
                </a>
            </nav>
        </div>
    </header>
    <livewire:search-form />
    @livewireScriptConfig
    @stack('body-scripts')
</body>

</html>
