<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">

        <title>{{ $title ?? config('app.name', 'DigiProper') }}</title>

        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-surface-50 dark:bg-gray-950 text-ink-800 dark:text-ink-100">
        <x-background-ribbons />

        <div class="relative min-h-screen flex flex-col">
            <header class="sticky top-0 z-30 backdrop-blur-md bg-white/80 dark:bg-gray-900/80 border-b border-ink-100 dark:border-ink-800">
                <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5">
                        <span class="h-8 w-8 rounded-lg bg-hero-purple flex items-center justify-center shadow-glow-sm ring-1 ring-white/20">
                            <x-application-logo class="h-4 w-4 fill-current text-white" />
                        </span>
                        <span class="inline-flex items-center gap-0.5">
                            <span class="font-extrabold text-base tracking-tight text-accent-600 dark:text-accent-400">Digi<span class="text-accent-700 dark:text-accent-300 underline decoration-blue-500 decoration-2 underline-offset-2">Proper</span></span>
                        </span>
                    </a>
                    <span class="text-[10px] uppercase tracking-widest font-bold text-ink-500 dark:text-ink-400">Shared property</span>
                </div>
            </header>

            <main class="flex-1 pb-16 pt-6 md:pt-10 animate-fade-up">
                <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>

            <footer class="border-t border-ink-100 dark:border-ink-800 mt-10">
                <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 text-xs text-ink-500 dark:text-ink-400">
                    <span>Shared via <span class="font-semibold text-ink-700 dark:text-ink-200">DigiProper</span> — property management for India.</span>
                    <span>Only the fields the owner chose to share are visible on this page.</span>
                </div>
            </footer>
        </div>
    </body>
</html>
