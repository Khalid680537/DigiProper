@props([
    'property',
    'size' => 64,
    'tone' => 'light',
    'linked' => true,
    'title' => null,
])

@php
    $svg = $property->qrSvg($size);
@endphp

@if ($svg)
    @php
        $frame = $size + 12;
        $bg = $tone === 'dark'
            ? 'bg-ink-900 ring-ink-700 dark:bg-ink-800 dark:ring-ink-600'
            : 'bg-white ring-ink-100 dark:bg-ink-50 dark:ring-white/10';
        $resolvedTitle = $title ?? ('Scan to view '.$property->name);
    @endphp

    @if ($linked)
        <a href="{{ $property->share_url }}"
           target="_blank"
           rel="noopener"
           {{ $attributes->merge([
                'class' => 'inline-flex items-center justify-center rounded-xl p-1.5 ring-1 shadow-soft transition hover:shadow-soft-lg hover:-translate-y-0.5 ease-spring duration-150 '.$bg,
                'title' => $resolvedTitle,
                'aria-label' => $resolvedTitle,
                'style' => 'width: '.$frame.'px; height: '.$frame.'px;',
           ]) }}
           @click.stop>
            {!! $svg !!}
        </a>
    @else
        <span {{ $attributes->merge([
                'class' => 'inline-flex items-center justify-center rounded-xl p-1.5 ring-1 shadow-soft '.$bg,
                'title' => $resolvedTitle,
                'aria-label' => $resolvedTitle,
                'style' => 'width: '.$frame.'px; height: '.$frame.'px;',
           ]) }}>
            {!! $svg !!}
        </span>
    @endif
@endif
