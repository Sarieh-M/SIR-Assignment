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

<body class="min-h-screen bg-cyan-50 font-sans antialiased">
     <!-- Navigation -->
     <header class="bg-slate-800 text-white shadow-md">
        <div class="container mx-auto flex justify-between p-4">
            <h1 class="text-lg font-bold">Question App</h1>
            <nav class="flex space-x-4">
                <a
                    href="{{ route('search') }}"
                    class="transition hover:text-cyan-100 {{ request()->routeIs('search') ? 'underline' : '' }}"
                >
                    Search Questions
                </a>
                <a
                    href="{{ route('add') }}"
                    class="transition hover:text-cyan-100 {{ request()->routeIs('add-question') ? 'underline' : '' }}"
                >
                    Add Question
                </a>
            </nav>
        </div>
    </header>
    <livewire:add/>
    <!-- Body Scripts -->
    @livewireScriptConfig
    @stack('body-scripts')
</body>

</html>
