@props([
    'tone' => 'ghost',
    'as' => 'button',
    'href' => null,
])

@php
    $tag = $href ? 'a' : $as;
    $base = 'inline-flex items-center justify-center h-10 w-10 rounded-full transition ease-spring duration-150 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900';
    $tones = [
        'ghost'   => 'text-ink-500 dark:text-ink-300 hover:bg-ink-100 dark:hover:bg-ink-800 hover:text-ink-700 dark:hover:text-ink-100',
        'primary' => 'text-white bg-primary-600 hover:bg-primary-500 shadow-glow-sm hover:shadow-glow',
        'danger'  => 'text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40 hover:text-red-700',
    ];
    $classes = $base . ' ' . ($tones[$tone] ?? $tones['ghost']);
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</{{ $tag }}>
