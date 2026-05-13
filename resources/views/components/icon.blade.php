{{-- Inline SVG icons. Heroicons v2 (MIT). Outline by default; pass :solid for filled state. --}}
@props(['name', 'solid' => false])

@php
    $stroke = 'fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"';
    $fillAttr = 'fill="currentColor"';
    $attrs = $solid ? $fillAttr : $stroke;

    $paths = match ($name) {
        'home'                => '<path d="M3 11.5 12 4l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-8.5Z"/>',
        'building-office'     => '<path d="M4 21V5a1 1 0 0 1 1-1h7v17M12 21h7a1 1 0 0 0 1-1V10a1 1 0 0 0-1-1h-7m-5 4h2m-2 4h2m6-4h2m-2 4h2"/>',
        'document'            => '<path d="M7 3h7l5 5v13H7V3Zm7 0v5h5"/>',
        'document-text'       => '<path d="M7 3h7l5 5v13H7V3Zm7 0v5h5M9 13h6M9 17h4"/>',
        'plus'                => '<path d="M12 5v14M5 12h14"/>',
        'search'              => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        'magnifying-glass'    => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        'settings'            => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1A2 2 0 1 1 4.3 17l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8L4.2 7A2 2 0 1 1 7 4.2l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1A2 2 0 1 1 19.7 7l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1Z"/>',
        'user'                => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'users'               => '<circle cx="9" cy="8" r="4"/><path d="M3 20a6 6 0 0 1 12 0"/><circle cx="17" cy="9" r="3"/><path d="M15 20a5 5 0 0 1 6.5-4.8"/>',
        'logout'              => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4m7 14 5-5-5-5m5 5H10"/>',
        'chevron-down'        => '<path d="m6 9 6 6 6-6"/>',
        'chevron-right'       => '<path d="m9 6 6 6-6 6"/>',
        'sparkles'            => '<path d="m9 4 1.5 3.5L14 9l-3.5 1.5L9 14l-1.5-3.5L4 9l3.5-1.5L9 4Zm9 8 .9 2.1L21 15l-2.1.9L18 18l-.9-2.1L15 15l2.1-.9L18 12Z"/>',
        'currency-rupee'      => '<path d="M6 4h12M6 8h12M9 4c2.5 0 4 1.5 4 4s-1.5 4-4 4H7l8 8"/>',
        'banknotes'           => '<rect x="3" y="7" width="18" height="12" rx="2"/><circle cx="12" cy="13" r="2.5"/><path d="M6 11v4M18 11v4"/>',
        'map-pin'             => '<path d="M12 22s7-6 7-12a7 7 0 1 0-14 0c0 6 7 12 7 12Z"/><circle cx="12" cy="10" r="3"/>',
        'key'                 => '<circle cx="8" cy="15" r="4"/><path d="m11 12 9-9m-4 4 3 3"/>',
        'calendar'            => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 9h18M8 3v4m8-4v4"/>',
        'upload'              => '<path d="M12 16V4m0 0-4 4m4-4 4 4M5 20h14"/>',
        'arrow-up-tray'       => '<path d="M12 16V4m0 0-4 4m4-4 4 4M5 20h14"/>',
        'arrow-down-tray'     => '<path d="M12 4v12m0 0 4-4m-4 4-4-4M5 20h14"/>',
        'arrow-right'         => '<path d="M5 12h14m0 0-6-6m6 6-6 6"/>',
        'arrow-left'          => '<path d="M19 12H5m0 0 6 6m-6-6 6-6"/>',
        'trash'               => '<path d="M4 7h16M10 11v6m4-6v6M6 7l1 13a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-13M9 7V4h6v3"/>',
        'pencil'              => '<path d="M4 20h4l10-10-4-4L4 16v4Z"/>',
        'check-circle'        => '<circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/>',
        'exclamation-circle'  => '<circle cx="12" cy="12" r="9"/><path d="M12 8v5m0 3v.01"/>',
        'exclamation-triangle' => '<path d="M12 4 2.5 20h19L12 4Zm0 6v4m0 3v.01"/>',
        'information-circle'  => '<circle cx="12" cy="12" r="9"/><path d="M12 8v.01M11 12h1v5h1"/>',
        'light-bulb'          => '<path d="M9 21h6m-5 0v-3m-2-2a7 7 0 1 1 8 0c-1 .6-1.5 1.4-1.5 2.4V18h-5v-1.6c0-1-.5-1.8-1.5-2.4Z"/>',
        'x-mark'              => '<path d="M6 6l12 12M18 6 6 18"/>',
        'menu'                => '<path d="M4 6h16M4 12h16M4 18h16"/>',
        'bell'                => '<path d="M6 8a6 6 0 1 1 12 0c0 7 3 7 3 9H3c0-2 3-2 3-9Zm4 12a2 2 0 0 0 4 0"/>',
        'shield-check'        => '<path d="M12 22s8-3 8-10V5l-8-3-8 3v7c0 7 8 10 8 10Zm-3-11 2 2 4-4"/>',
        'identification'      => '<rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="12" r="2.5"/><path d="M5 17c.5-1.5 2.2-2.5 4-2.5s3.5 1 4 2.5M14 10h5M14 13h5M14 16h3"/>',
        'folder-open'         => '<path d="M3 8a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v1H3V8Zm0 3h18l-2 8a2 2 0 0 1-2 1.5H5a2 2 0 0 1-2-1.5L3 11Z"/>',
        'photo'               => '<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="m3 17 5-5 4 4 3-3 6 6"/>',
        'home-modern'         => '<path d="M3 21h18M5 21V10l7-6 7 6v11M9 21v-6h6v6"/>',
        'eye'                 => '<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>',
        'qr-code'             => '<rect x="3.5" y="3.5" width="6" height="6" rx="1"/><rect x="14.5" y="3.5" width="6" height="6" rx="1"/><rect x="3.5" y="14.5" width="6" height="6" rx="1"/><path d="M6 6h1v1H6Zm11 0h1v1h-1ZM6 17h1v1H6Z"/><path d="M14.5 14.5h2v2m4 0v-2h-2m0 4h2v2h-2m-4 0h2v-2"/>',
        'share'               => '<circle cx="6" cy="12" r="2.5"/><circle cx="18" cy="6" r="2.5"/><circle cx="18" cy="18" r="2.5"/><path d="m8.2 11 7.6-4M8.2 13l7.6 4"/>',
        'arrow-path'          => '<path d="M3 12a9 9 0 0 1 15.5-6.3L21 8M21 3v5h-5M21 12a9 9 0 0 1-15.5 6.3L3 16m0 5v-5h5"/>',
        'clipboard'           => '<rect x="6" y="5" width="12" height="16" rx="2"/><path d="M9 5V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v1"/>',
        'check'               => '<path d="m5 12 5 5 9-11"/>',
        default               => '',
    };
@endphp

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" {!! $attrs !!} {{ $attributes->merge(['class' => 'h-5 w-5', 'aria-hidden' => 'true']) }}>
    {!! $paths !!}
</svg>
