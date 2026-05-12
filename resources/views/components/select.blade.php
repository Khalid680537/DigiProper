@props([
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'disabled' => false,
])

<select @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full border-ink-200 dark:border-ink-700 dark:bg-gray-900 dark:text-ink-100 focus:border-primary-500 dark:focus:border-primary-400 focus:ring-4 focus:ring-primary-500/15 rounded-xl px-4 py-2.5 text-sm shadow-sm transition']) }}>
    @if ($placeholder !== null)
        <option value="">{{ $placeholder }}</option>
    @endif

    @foreach ($options as $value => $label)
        <option value="{{ $value }}" @selected((string) $selected === (string) $value)>{{ $label }}</option>
    @endforeach
</select>
