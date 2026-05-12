@props(['label', 'value', 'hint' => null])

<div {{ $attributes->merge(['class' => 'rounded-2xl bg-gradient-to-br from-primary-50 to-white dark:from-primary-950/40 dark:to-gray-800 p-5 ring-1 ring-primary-900/5 dark:ring-white/10 shadow-sm']) }}>
    <p class="text-xs font-medium uppercase tracking-widest text-primary-700 dark:text-primary-300">{{ $label }}</p>
    <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $value }}</p>
    @if ($hint)
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $hint }}</p>
    @endif
</div>
