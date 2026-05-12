@props(['amount' => null, 'placeholder' => '—'])

@php
    $rendered = $placeholder;
    if ($amount !== null && $amount !== '') {
        $numeric = (float) $amount;
        if (class_exists(\NumberFormatter::class)) {
            $fmt = new \NumberFormatter('en_IN', \NumberFormatter::CURRENCY);
            $fmt->setAttribute(\NumberFormatter::FRACTION_DIGITS, fmod($numeric, 1) === 0.0 ? 0 : 2);
            $rendered = $fmt->formatCurrency($numeric, 'INR');
        } else {
            $rendered = '₹'.number_format($numeric, 0, '.', ',');
        }
    }
@endphp

<span {{ $attributes->merge(['class' => 'tabular-nums']) }}>{{ $rendered }}</span>
