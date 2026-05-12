@props([
    'title',
    'description' => null,
    'ctaHref' => null,
    'ctaLabel' => null,
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 bg-white/60 dark:bg-gray-800/40 p-10 text-center']) }}>
    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $title }}</h3>
    @if ($description)
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
    @endif

    @if ($ctaHref && $ctaLabel)
        <a href="{{ $ctaHref }}" class="mt-5 inline-flex items-center px-4 py-2 bg-primary-600 dark:bg-primary-500 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 dark:hover:bg-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition">
            {{ $ctaLabel }}
        </a>
    @endif

    {{ $slot ?? '' }}
</div>
