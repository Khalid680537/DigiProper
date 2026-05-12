@props([
    'title',
    'description' => null,
    'ctaHref' => null,
    'ctaLabel' => null,
    'icon' => 'home-modern',
])

<div {{ $attributes->merge(['class' => 'rounded-3xl bg-white dark:bg-gray-800 ring-1 ring-gray-900/5 dark:ring-white/10 shadow-soft p-10 sm:p-14 text-center']) }}>
    <div class="mx-auto h-16 w-16 rounded-2xl bg-gradient-to-br from-primary-50 to-accent-50 dark:from-primary-950/40 dark:to-accent-950/40 text-primary-600 dark:text-primary-300 flex items-center justify-center ring-1 ring-primary-100 dark:ring-primary-900/40">
        <x-icon :name="$icon" class="h-7 w-7" />
    </div>
    <h3 class="mt-5 text-lg font-semibold text-ink-900 dark:text-ink-50">{{ $title }}</h3>
    @if ($description)
        <p class="mt-2 text-sm text-ink-500 dark:text-ink-400 max-w-sm mx-auto">{{ $description }}</p>
    @endif

    @if ($ctaHref && $ctaLabel)
        <a href="{{ $ctaHref }}" class="mt-6 inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-primary-600 rounded-xl font-semibold text-sm text-white hover:bg-primary-500 hover:-translate-y-0.5 hover:shadow-glow shadow-glow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition ease-spring duration-150">
            <x-icon name="plus" class="h-4 w-4" />
            {{ $ctaLabel }}
        </a>
    @endif

    {{ $slot ?? '' }}
</div>
