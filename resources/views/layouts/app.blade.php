<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'DigiProper') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-surface-50 dark:bg-gray-950 text-ink-800 dark:text-ink-100">
        <div class="min-h-screen md:flex">
            <x-sidebar class="hidden md:flex" />

            <div class="flex-1 flex flex-col min-w-0">
                <x-topbar class="hidden md:flex" :title="$title ?? null" />

                {{-- Mobile-only legacy header slot (deprecated; pages should use <x-page-hero>) --}}
                @isset($header)
                    <header class="md:hidden bg-white dark:bg-gray-900 shadow-soft">
                        <div class="px-4 py-4">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main class="flex-1 pb-24 md:pb-10 pt-4 md:pt-6">
                    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 animate-fade-up">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        <x-bottom-nav class="md:hidden" />
    </body>
</html>
