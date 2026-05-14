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

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="relative min-h-dvh flex flex-col justify-center items-center px-4 py-12 overflow-hidden bg-gradient-to-br from-primary-50 via-white to-primary-100 dark:from-gray-950 dark:via-gray-900 dark:to-primary-950/40">
            <div aria-hidden="true" class="pointer-events-none absolute -top-40 -left-40 h-[28rem] w-[28rem] rounded-full bg-primary-400/30 dark:bg-primary-500/20 blur-3xl"></div>
            <div aria-hidden="true" class="pointer-events-none absolute -bottom-40 -right-40 h-[32rem] w-[32rem] rounded-full bg-accent-400/30 dark:bg-accent-500/15 blur-3xl"></div>
            <div aria-hidden="true" class="pointer-events-none absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 h-[24rem] w-[24rem] rounded-full bg-sky-200/20 dark:bg-sky-500/10 blur-3xl"></div>

            <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_right,rgba(99,102,241,0.06)_1px,transparent_1px),linear-gradient(to_bottom,rgba(99,102,241,0.06)_1px,transparent_1px)] bg-[size:48px_48px] [mask-image:radial-gradient(ellipse_at_center,black_30%,transparent_75%)] dark:bg-[linear-gradient(to_right,rgba(165,180,252,0.05)_1px,transparent_1px),linear-gradient(to_bottom,rgba(165,180,252,0.05)_1px,transparent_1px)]"></div>

            <div class="relative w-full sm:max-w-sm flex flex-col items-center">
                <a href="/" class="relative z-10 -mb-8 inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-500 to-primary-700 text-white shadow-lg shadow-primary-500/30 ring-1 ring-white/20">
                    <x-application-logo class="h-9 w-9 fill-current" />
                </a>

                <div class="w-full px-8 pt-14 pb-8 bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-xl ring-1 ring-gray-900/5 dark:ring-white/10">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
