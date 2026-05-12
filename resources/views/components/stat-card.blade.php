@props([
    'label',
    'value' => null,
    'hint' => null,
    'icon' => null,
    'tint' => 'primary',
])

@php
    $tints = [
        'primary' => ['bg' => 'bg-primary-50 dark:bg-primary-950/40', 'icon' => 'bg-primary-100 text-primary-700 dark:bg-primary-900/60 dark:text-primary-200', 'stripe' => 'bg-primary-500'],
        'accent'  => ['bg' => 'bg-accent-50 dark:bg-accent-950/40',   'icon' => 'bg-accent-100 text-accent-700 dark:bg-accent-900/60 dark:text-accent-200',     'stripe' => 'bg-accent-500'],
        'emerald' => ['bg' => 'bg-emerald-50 dark:bg-emerald-950/40', 'icon' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-200', 'stripe' => 'bg-emerald-500'],
        'sky'     => ['bg' => 'bg-sky-50 dark:bg-sky-950/40',         'icon' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/60 dark:text-sky-200',                 'stripe' => 'bg-sky-500'],
        'neutral' => ['bg' => 'bg-surface-100 dark:bg-gray-800',      'icon' => 'bg-white text-ink-600 dark:bg-gray-700 dark:text-ink-200',                     'stripe' => 'bg-ink-400'],
    ];
    $t = $tints[$tint] ?? $tints['primary'];
@endphp

<div {{ $attributes->merge(['class' => "relative rounded-3xl {$t['bg']} p-5 ring-1 ring-gray-900/5 dark:ring-white/10 shadow-soft hover:-translate-y-0.5 hover:shadow-soft-lg transition ease-spring duration-150 overflow-hidden"]) }}>
    <span aria-hidden="true" class="absolute inset-y-0 left-0 w-1 {{ $t['stripe'] }}"></span>

    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-xs font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">{{ $label }}</p>
            <p class="mt-2 text-2xl sm:text-3xl font-extrabold tracking-tight text-ink-900 dark:text-ink-50 tabular-nums truncate">{{ $value }}</p>
            @if ($hint)
                <p class="mt-1 text-xs text-ink-500 dark:text-ink-400">{{ $hint }}</p>
            @endif
        </div>

        @if ($icon)
            <span class="h-10 w-10 rounded-xl {{ $t['icon'] }} flex items-center justify-center shrink-0">
                <x-icon :name="$icon" class="h-5 w-5" />
            </span>
        @endif
    </div>
</div>
