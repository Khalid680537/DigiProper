@props([
    'title' => null,
    'icon' => null,
    'description' => null,
    'padding' => 'p-6 sm:p-8',
])

<section {{ $attributes->merge(['class' => "rounded-3xl bg-white dark:bg-gray-800 shadow-soft ring-1 ring-gray-900/5 dark:ring-white/10 {$padding}"]) }}>
    @if ($title || $icon || $description || isset($headerActions))
        <header class="flex items-start justify-between gap-3 mb-5">
            <div class="flex items-start gap-3 min-w-0">
                @if ($icon)
                    <span class="h-10 w-10 rounded-xl bg-primary-50 dark:bg-primary-950/40 text-primary-600 dark:text-primary-300 flex items-center justify-center shrink-0">
                        <x-icon :name="$icon" class="h-5 w-5" />
                    </span>
                @endif
                <div class="min-w-0">
                    @if ($title)
                        <h2 class="text-base sm:text-lg font-semibold text-ink-900 dark:text-ink-50">{{ $title }}</h2>
                    @endif
                    @if ($description)
                        <p class="mt-0.5 text-sm text-ink-500 dark:text-ink-400">{{ $description }}</p>
                    @endif
                </div>
            </div>
            @isset($headerActions)
                <div class="shrink-0">{{ $headerActions }}</div>
            @endisset
        </header>
    @endif

    {{ $slot }}
</section>
