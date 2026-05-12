@props(['rounded' => 'rounded-xl'])

<div {{ $attributes->merge(['class' => "shimmer-overlay bg-ink-100 dark:bg-ink-800 {$rounded}"]) }}></div>
