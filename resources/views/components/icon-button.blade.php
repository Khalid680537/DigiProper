@props([
    'tone' => 'ghost',
    'as' => 'button',
    'href' => null,
    'size' => 'md',
])

@php
    $tag = $href ? 'a' : $as;
    $sizes = [
        'sm' => 'h-9 w-9',
        'md' => 'h-11 w-11',
        'lg' => 'h-12 w-12',
    ];
    $base = 'inline-flex items-center justify-center '.($sizes[$size] ?? $sizes['md']).' rounded-full transition ease-spring duration-150 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900';
    $tones = [
        'ghost'   => 'text-ink-500 dark:text-ink-300 hover:bg-ink-100 dark:hover:bg-ink-800 hover:text-ink-700 dark:hover:text-ink-100',
        'primary' => 'text-white bg-primary-600 hover:bg-primary-500 shadow-glow-sm hover:shadow-glow',
        'danger'  => 'text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40 hover:text-red-700',
        'overlay' => 'text-white bg-ink-950/60 hover:bg-ink-950/80 backdrop-blur-sm',
    ];
    $classes = $base.' '.($tones[$tone] ?? $tones['ghost']);
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</{{ $tag }}>
