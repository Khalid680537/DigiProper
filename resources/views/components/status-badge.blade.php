@props(['tone' => 'neutral', 'label' => null, 'dot' => true])

@php
    $tones = [
        'primary' => ['bg' => 'bg-primary-100/80 dark:bg-primary-900/40', 'text' => 'text-primary-700 dark:text-primary-200', 'ring' => 'ring-primary-200/60 dark:ring-primary-800/60', 'dot' => 'bg-primary-500'],
        'accent'  => ['bg' => 'bg-accent-100/80 dark:bg-accent-900/40',   'text' => 'text-accent-700 dark:text-accent-200',   'ring' => 'ring-accent-200/60 dark:ring-accent-800/60',   'dot' => 'bg-accent-500'],
        'success' => ['bg' => 'bg-emerald-100/80 dark:bg-emerald-900/40', 'text' => 'text-emerald-700 dark:text-emerald-200', 'ring' => 'ring-emerald-200/60 dark:ring-emerald-800/60', 'dot' => 'bg-emerald-500'],
        'warning' => ['bg' => 'bg-amber-100/80 dark:bg-amber-900/40',     'text' => 'text-amber-700 dark:text-amber-200',     'ring' => 'ring-amber-200/60 dark:ring-amber-800/60',     'dot' => 'bg-amber-500'],
        'neutral' => ['bg' => 'bg-ink-100/80 dark:bg-ink-800/60',         'text' => 'text-ink-700 dark:text-ink-200',         'ring' => 'ring-ink-200/60 dark:ring-ink-700/60',         'dot' => 'bg-ink-400'],
    ];
    $t = $tones[$tone] ?? $tones['neutral'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset {$t['bg']} {$t['text']} {$t['ring']}"]) }}>
    @if ($dot)
        <span class="h-1.5 w-1.5 rounded-full {{ $t['dot'] }} animate-pulse-dot"></span>
    @endif
    {{ $label ?? $slot }}
</span>
