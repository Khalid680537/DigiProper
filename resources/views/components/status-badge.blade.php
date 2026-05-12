@props(['tone' => 'neutral', 'label' => null])

@php
    $tones = [
        'primary' => 'bg-primary-100 text-primary-700 dark:bg-primary-900/40 dark:text-primary-200 ring-primary-200/60 dark:ring-primary-800/60',
        'accent'  => 'bg-accent-100 text-accent-700 dark:bg-accent-900/40 dark:text-accent-200 ring-accent-200/60 dark:ring-accent-800/60',
        'success' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200 ring-emerald-200/60 dark:ring-emerald-800/60',
        'warning' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-200 ring-amber-200/60 dark:ring-amber-800/60',
        'neutral' => 'bg-gray-100 text-gray-700 dark:bg-gray-700/40 dark:text-gray-200 ring-gray-200/60 dark:ring-gray-700/60',
    ];
    $classes = $tones[$tone] ?? $tones['neutral'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset $classes"]) }}>
    {{ $label ?? $slot }}
</span>
