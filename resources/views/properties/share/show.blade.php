@php
    $tenureLabels = [
        'freehold' => 'Freehold',
        'leasehold' => 'Leasehold',
        'rented_in' => 'Rented in',
        'pagri' => 'Pagri',
        'other' => 'Other',
    ];
    $occupancyLabels = [
        'rented_out' => 'Rented out',
        'self_use' => 'Self use',
        'vacant_plot' => 'Vacant plot',
        'vacant_built' => 'Vacant (built)',
    ];
    $areaUnitLabels = [
        'sqm' => 'sq m',
        'sqft' => 'sq ft',
        'sqyd' => 'sq yd',
    ];

    $addressLines = array_filter([
        $property->address_line1,
        $property->address_line2,
        trim(($property->city ?: '').($property->state ? ', '.$property->state : '')),
        $property->pincode,
    ]);

    $contactsList = $property->share_contacts
        ? collect($property->contacts ?? [])
            ->filter(fn ($c) => trim(($c['name'] ?? '').($c['phone'] ?? '').($c['role'] ?? '').($c['notes'] ?? '')) !== '')
            ->values()
        : collect();

    $initialsOf = function (?string $name): string {
        if (! $name) { return '?'; }
        $parts = preg_split('/\s+/', trim($name));
        $first = mb_substr($parts[0] ?? '', 0, 1);
        $second = isset($parts[1]) ? mb_substr($parts[1], 0, 1) : '';
        return strtoupper($first.$second) ?: '?';
    };

    $hasNotesSection = ($property->share_keys_location && $property->keys_location)
        || ($property->share_extra_notes && $property->extra_notes);
@endphp

<x-public-layout :title="$property->name.' — DigiProper'">
    <div class="space-y-6">
        {{-- Hero --}}
        @php $primaryPhoto = $property->primaryPhoto; @endphp
        <x-page-hero tone="purple"
            :image="$primaryPhoto ? route('properties.photos.show', [$property, $primaryPhoto]) : null"
            :image-alt="$primaryPhoto ? $property->name : ''">
            <div class="flex flex-col lg:flex-row lg:items-center gap-6">
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] uppercase tracking-widest font-bold text-white/70">Property</p>
                    <h1 class="mt-1 text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white">{{ $property->name }}</h1>

                    @if ($addressLines)
                        <p class="mt-2 text-sm text-white/85 flex items-center gap-1.5">
                            <x-icon name="map-pin" class="h-4 w-4 shrink-0" />
                            <span>{{ implode(' · ', $addressLines) }}</span>
                        </p>
                    @endif

                    <div class="mt-4 flex flex-wrap gap-1.5">
                        @if ($property->tenure)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 backdrop-blur-sm ring-1 ring-white/25 text-white text-xs font-semibold px-3 py-1">
                                <span class="h-1.5 w-1.5 rounded-full bg-white animate-pulse-dot"></span>
                                {{ $tenureLabels[$property->tenure] ?? $property->tenure }}
                            </span>
                        @endif
                        @if ($property->occupancy_status)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 backdrop-blur-sm ring-1 ring-white/25 text-white text-xs font-semibold px-3 py-1">
                                <span class="h-1.5 w-1.5 rounded-full bg-white animate-pulse-dot"></span>
                                {{ $occupancyLabels[$property->occupancy_status] ?? $property->occupancy_status }}
                            </span>
                        @endif
                        @if ($property->area_value)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 backdrop-blur-sm ring-1 ring-white/25 text-white text-xs font-semibold px-3 py-1">
                                {{ rtrim(rtrim((string) $property->area_value, '0'), '.') }} {{ $areaUnitLabels[$property->area_unit] ?? $property->area_unit }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="shrink-0">
                    <x-qr-thumb :property="$property" :size="140" :linked="false" />
                </div>
            </div>
        </x-page-hero>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                {{-- Financials (only when toggled on) --}}
                @if ($property->share_financials && ($property->imputed_value_inr || $property->rent_yearly_inr || $property->effective_yield_percent))
                    <section class="rounded-3xl bg-gradient-to-br from-emerald-50 to-sky-50 dark:from-emerald-950/30 dark:to-sky-950/30 ring-1 ring-emerald-100 dark:ring-white/10 p-6 sm:p-8 shadow-soft">
                        <div class="flex items-center gap-2">
                            <span class="h-9 w-9 rounded-xl bg-white dark:bg-gray-800 text-emerald-600 dark:text-emerald-300 flex items-center justify-center shadow-soft">
                                <x-icon name="currency-rupee" class="h-5 w-5" />
                            </span>
                            <h2 class="text-base font-semibold text-emerald-800 dark:text-emerald-200">Financials</h2>
                        </div>
                        <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-5">
                            @if ($property->imputed_value_inr)
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">Imputed value</p>
                                    <p class="mt-2 text-2xl sm:text-3xl font-extrabold tracking-tight text-ink-900 dark:text-ink-50 tabular-nums"><x-inr :amount="$property->imputed_value_inr" /></p>
                                </div>
                            @endif
                            @if ($property->rent_yearly_inr)
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">Rent / year</p>
                                    <p class="mt-2 text-2xl sm:text-3xl font-extrabold tracking-tight text-ink-900 dark:text-ink-50 tabular-nums"><x-inr :amount="$property->rent_yearly_inr" /></p>
                                </div>
                            @endif
                            @if ($property->effective_yield_percent)
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">Yield</p>
                                    <p class="mt-2 text-2xl sm:text-3xl font-extrabold tracking-tight text-ink-900 dark:text-ink-50 tabular-nums">{{ $property->effective_yield_percent }}%</p>
                                </div>
                            @endif
                        </div>
                    </section>
                @endif

                {{-- Overview --}}
                <x-section-card title="Overview" icon="information-circle">
                    <dl class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @if ($property->share_title_holder && $property->title_holder)
                            <div class="rounded-2xl bg-surface-100 dark:bg-gray-900/40 p-4">
                                <dt class="text-[10px] font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">Title holder</dt>
                                <dd class="mt-1 text-sm font-medium text-ink-900 dark:text-ink-50 break-words">{{ $property->title_holder }}</dd>
                            </div>
                        @endif
                        @if ($property->rera_number)
                            <div class="rounded-2xl bg-surface-100 dark:bg-gray-900/40 p-4">
                                <dt class="text-[10px] font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">RERA number</dt>
                                <dd class="mt-1 text-sm font-medium text-ink-900 dark:text-ink-50 break-words tabular-nums">{{ $property->rera_number }}</dd>
                            </div>
                        @endif
                        @if ($property->tenure_authority)
                            <div class="rounded-2xl bg-surface-100 dark:bg-gray-900/40 p-4">
                                <dt class="text-[10px] font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">Authority</dt>
                                <dd class="mt-1 text-sm font-medium text-ink-900 dark:text-ink-50 break-words">{{ $property->tenure_authority }}</dd>
                            </div>
                        @endif
                        @if ($property->area_value)
                            <div class="rounded-2xl bg-surface-100 dark:bg-gray-900/40 p-4">
                                <dt class="text-[10px] font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">Area</dt>
                                <dd class="mt-1 text-sm font-medium text-ink-900 dark:text-ink-50">
                                    {{ rtrim(rtrim((string) $property->area_value, '0'), '.') }} {{ $areaUnitLabels[$property->area_unit] ?? $property->area_unit }}
                                </dd>
                            </div>
                        @endif
                        @if ($property->construction)
                            <div class="rounded-2xl bg-surface-100 dark:bg-gray-900/40 p-4 col-span-2 sm:col-span-3">
                                <dt class="text-[10px] font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">Construction</dt>
                                <dd class="mt-1 text-sm font-medium text-ink-900 dark:text-ink-50 break-words">{{ $property->construction }}</dd>
                            </div>
                        @endif
                    </dl>
                </x-section-card>

                {{-- Notes --}}
                @if ($hasNotesSection)
                    <x-section-card title="Notes" icon="document-text">
                        <dl class="space-y-4 text-sm">
                            @if ($property->share_keys_location && $property->keys_location)
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400 flex items-center gap-1.5">
                                        <x-icon name="key" class="h-3.5 w-3.5" /> Keys & documents location
                                    </dt>
                                    <dd class="mt-1 text-ink-900 dark:text-ink-50">{{ $property->keys_location }}</dd>
                                </div>
                            @endif
                            @if ($property->share_extra_notes && $property->extra_notes)
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">Extra notes</dt>
                                    <dd class="mt-1 text-ink-900 dark:text-ink-50 whitespace-pre-line">{{ $property->extra_notes }}</dd>
                                </div>
                            @endif
                        </dl>
                    </x-section-card>
                @endif

                {{-- Photos gallery --}}
                @if ($property->photos->isNotEmpty())
                    <x-section-card title="Photos" icon="photo">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach ($property->photos as $photo)
                                <div class="aspect-square overflow-hidden rounded-2xl ring-1 ring-ink-100 dark:ring-ink-800 bg-surface-100 dark:bg-gray-900/40">
                                    <img src="{{ route('properties.photos.show', [$property, $photo]) }}"
                                         alt="{{ $property->name }}"
                                         loading="lazy"
                                         class="h-full w-full object-cover" />
                                </div>
                            @endforeach
                        </div>
                    </x-section-card>
                @endif
            </div>

            <aside class="space-y-6">
                {{-- Contacts (when toggled) --}}
                @if ($property->share_contacts)
                    <x-section-card title="Contacts" icon="users">
                        @if ($contactsList->isEmpty())
                            <p class="text-sm text-ink-500 dark:text-ink-400 italic">No contacts on file.</p>
                        @else
                            <ul class="space-y-3">
                                @foreach ($contactsList as $contact)
                                    <li class="rounded-2xl ring-1 ring-ink-100 dark:ring-ink-800 p-3.5 flex items-start gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-primary-400 to-primary-700 text-white font-bold text-sm flex items-center justify-center shrink-0">
                                            {{ $initialsOf($contact['name'] ?? null) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-semibold text-ink-900 dark:text-ink-50 truncate">{{ trim($contact['name'] ?? '') ?: '—' }}</p>
                                            @if (! empty($contact['role']))
                                                <div class="mt-1">
                                                    <x-status-badge tone="primary" :label="$contact['role']" :dot="false" />
                                                </div>
                                            @endif
                                            @if (! empty($contact['phone']))
                                                <a href="tel:{{ preg_replace('/[^+0-9]/', '', $contact['phone']) }}"
                                                   class="mt-1 inline-flex items-center gap-1 text-sm text-primary-700 dark:text-primary-300 hover:underline tabular-nums">
                                                    {{ $contact['phone'] }}
                                                </a>
                                            @endif
                                            @if (! empty($contact['notes']))
                                                <p class="mt-1 text-xs text-ink-500 dark:text-ink-400">{{ $contact['notes'] }}</p>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </x-section-card>
                @endif

                {{-- QR + share URL --}}
                <x-section-card title="Scan to revisit" icon="qr-code">
                    <div class="flex flex-col items-center gap-4">
                        <x-qr-thumb :property="$property" :size="180" :linked="false" />
                        <div class="text-center text-xs text-ink-500 dark:text-ink-400">
                            Save this QR or take a screenshot to revisit this page later.
                        </div>
                        <a href="{{ route('properties.share.qr.png', $property->share_token) }}"
                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary-700 dark:text-primary-300 hover:underline">
                            <x-icon name="arrow-down-tray" class="h-3.5 w-3.5" /> Download QR (PNG)
                        </a>
                    </div>
                </x-section-card>
            </aside>
        </div>
    </div>
</x-public-layout>
