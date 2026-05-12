@props([
    'active' => false,
    'href' => null,
])

@php
    $tag = $href ? 'a' : 'button';
    $base = 'inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition ease-spring duration-150';
    $state = $active
        ? 'bg-primary-600 text-white shadow-glow-sm'
        : 'bg-white dark:bg-gray-800 text-ink-600 dark:text-ink-300 ring-1 ring-ink-200 dark:ring-ink-700 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-primary-950/40 dark:hover:text-primary-200';
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => "{$base} {$state}"]) }}>
    {{ $slot }}
</{{ $tag }}>
