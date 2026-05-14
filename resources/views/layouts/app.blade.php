<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'DigiProper') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

        <!-- PWA -->
        <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
        <meta name="theme-color" content="#4b2fbf" media="(prefers-color-scheme: light)">
        <meta name="theme-color" content="#160641" media="(prefers-color-scheme: dark)">
        <meta name="application-name" content="DigiProper">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="DigiProper">
        <link rel="apple-touch-icon" href="{{ asset('brand/digiproper-icon@512.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-surface-50 dark:bg-gray-950 text-ink-800 dark:text-ink-100">
        <x-background-ribbons />

        <div class="relative min-h-dvh md:flex">
            <x-sidebar class="hidden md:flex" />

            <div class="flex-1 flex flex-col min-w-0">
                <x-topbar class="hidden md:flex" :title="$title ?? null" />

                {{-- Mobile-only brand header --}}
                <a href="{{ route('dashboard') }}"
                   class="md:hidden sticky top-0 z-30 flex items-center gap-3 h-14 px-4 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md border-b border-ink-100 dark:border-ink-800">
                    <span class="h-8 w-8 rounded-lg bg-hero-purple flex items-center justify-center shadow-glow-sm ring-1 ring-white/20">
                        <x-application-logo class="h-4 w-4 fill-current text-white" />
                    </span>
                    <span class="inline-flex items-center gap-0.5">
                        <span class="font-extrabold text-base tracking-tight text-accent-600 dark:text-accent-400">Digi<span class="text-accent-700 dark:text-accent-300 underline decoration-blue-500 decoration-2 underline-offset-2">Proper</span></span>
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="h-3 w-3 -translate-y-1.5 text-primary-500 dark:text-primary-400">
                            <path d="M12 2Q13 11 22 12Q13 13 12 22Q11 13 2 12Q11 11 12 2Z"/>
                        </svg>
                    </span>
                </a>

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

        @auth
            <x-command-palette />
        @endauth
    </body>
</html>
