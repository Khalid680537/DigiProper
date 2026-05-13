@props([
    'eyebrow' => null,
    'title' => null,
    'subtitle' => null,
    'tone' => 'purple',
    'compact' => false,
    'image' => null,
    'imageAlt' => '',
])

@php
    $bgMap = [
        'purple' => 'bg-hero-purple',
        'warm'   => 'bg-hero-warm',
        'night'  => 'bg-hero-night',
    ];
    $bg = $bgMap[$tone] ?? $bgMap['purple'];
    $padding = $compact ? 'px-6 py-6 sm:px-8 sm:py-7' : 'px-6 py-8 sm:px-10 sm:py-12';
@endphp

<section {{ $attributes->merge(['class' => "relative overflow-hidden rounded-3xl {$bg} text-white {$padding} shadow-glow"]) }}>
    @if ($image)
        <img src="{{ $image }}" alt="{{ $imageAlt }}" class="pointer-events-none absolute inset-0 h-full w-full object-cover" />
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-gradient-to-t from-ink-950/80 via-ink-950/45 to-ink-950/25"></div>
    @endif

    {{-- Decorative blur blobs --}}
    <div aria-hidden="true" class="pointer-events-none absolute -top-16 -right-16 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
    <div aria-hidden="true" class="pointer-events-none absolute -bottom-16 -left-10 h-48 w-48 rounded-full bg-white/10 blur-3xl"></div>

    {{-- Faint brand watermark --}}
    @unless ($image)
        <x-application-logo aria-hidden="true"
            class="pointer-events-none absolute top-5 right-5 sm:top-6 sm:right-6 h-12 w-12 sm:h-16 sm:w-16 text-white/10 fill-current" />
    @endunless

    <div class="relative">
        @if ($slot->isNotEmpty())
            {{ $slot }}
        @else
            @if ($eyebrow)
                <p class="text-xs font-semibold uppercase tracking-widest text-white/70">{{ $eyebrow }}</p>
            @endif
            @if ($title)
                <h1 class="mt-2 text-3xl sm:text-4xl font-extrabold tracking-tight">{{ $title }}</h1>
            @endif
            @if ($subtitle)
                <p class="mt-2 max-w-2xl text-sm sm:text-base text-white/85">{{ $subtitle }}</p>
            @endif
        @endif

        @isset($actions)
            <div class="mt-5 flex flex-wrap gap-3">{{ $actions }}</div>
        @endisset
    </div>
</section>
