@props(['title', 'description' => null, 'icon' => null])

<section class="rounded-3xl bg-white dark:bg-gray-800 shadow-soft ring-1 ring-gray-900/5 dark:ring-white/10 p-6 sm:p-8">
    <header class="mb-6 flex items-start gap-3">
        @if ($icon)
            <span class="h-10 w-10 rounded-xl bg-primary-50 dark:bg-primary-950/40 text-primary-600 dark:text-primary-300 flex items-center justify-center shrink-0">
                <x-icon :name="$icon" class="h-5 w-5" />
            </span>
        @endif
        <div class="min-w-0">
            <h2 class="text-base sm:text-lg font-semibold text-ink-900 dark:text-ink-50">{{ $title }}</h2>
            @if ($description)
                <p class="mt-0.5 text-sm text-ink-500 dark:text-ink-400">{{ $description }}</p>
            @endif
        </div>
    </header>

    <div class="space-y-5">
        {{ $slot }}
    </div>
</section>
