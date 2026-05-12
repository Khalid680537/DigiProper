@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-semibold uppercase tracking-wide text-ink-600 dark:text-ink-300']) }}>
    {{ $value ?? $slot }}
</label>
