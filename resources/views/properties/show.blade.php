@php
    $tenureLabels = [
        'freehold' => 'Freehold',
        'leasehold' => 'Leasehold',
        'rented_in' => 'Rented in',
        'pagri' => 'Pagri',
        'other' => 'Other',
    ];
    $tenureTone = [
        'freehold' => 'success',
        'leasehold' => 'primary',
        'rented_in' => 'accent',
        'pagri' => 'warning',
        'other' => 'neutral',
    ];
    $occupancyLabels = [
        'rented_out' => 'Rented out',
        'self_use' => 'Self use',
        'vacant_plot' => 'Vacant plot',
        'vacant_built' => 'Vacant (built)',
    ];
    $occupancyTone = [
        'rented_out' => 'accent',
        'self_use' => 'primary',
        'vacant_plot' => 'neutral',
        'vacant_built' => 'neutral',
    ];

    $addressLines = array_filter([
        $property->address_line1,
        $property->address_line2,
        trim(($property->city ?: '').($property->state ? ', '.$property->state : '')),
        $property->pincode,
    ]);

    $initial = strtoupper(mb_substr($property->name, 0, 1));

    $contactsList = collect($property->contacts ?? [])
        ->filter(fn ($c) => trim(($c['name'] ?? '').($c['phone'] ?? '').($c['role'] ?? '').($c['notes'] ?? '')) !== '')
        ->values();

    $initialsOf = function (?string $name): string {
        if (! $name) return '?';
        $parts = preg_split('/\s+/', trim($name));
        $first = mb_substr($parts[0] ?? '', 0, 1);
        $second = isset($parts[1]) ? mb_substr($parts[1], 0, 1) : '';
        return strtoupper($first.$second) ?: '?';
    };
@endphp

<x-app-layout :title="$property->name">
    <div class="space-y-6">
        {{-- Hero --}}
        @php $primaryPhoto = $property->primaryPhoto; @endphp
        <x-page-hero tone="purple"
            :image="$primaryPhoto ? route('properties.photos.show', [$property, $primaryPhoto]) : null"
            :image-alt="$primaryPhoto ? $property->name : ''">
            <div class="flex flex-col sm:flex-row sm:items-center gap-5">
                <div class="h-16 w-16 rounded-2xl bg-white/15 ring-2 ring-white/25 flex items-center justify-center text-2xl font-extrabold text-white shrink-0 backdrop-blur-sm">
                    {{ $initial }}
                </div>
                <div class="min-w-0 flex-1">
                    <a href="{{ route('properties.index') }}" class="inline-flex items-center gap-1 text-xs text-white/70 hover:text-white transition">
                        <x-icon name="arrow-left" class="h-3 w-3" /> All properties
                    </a>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white truncate">{{ $property->name }}</h1>
                    @if ($addressLines)
                        <p class="mt-1 text-sm text-white/80 truncate flex items-center gap-1">
                            <x-icon name="map-pin" class="h-3.5 w-3.5 shrink-0" />
                            <span class="truncate">{{ implode(' · ', $addressLines) }}</span>
                        </p>
                    @endif
                    <div class="mt-3 flex flex-wrap gap-1.5">
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
                        @if ($property->is_data_complete)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/30 backdrop-blur-sm ring-1 ring-emerald-200/40 text-white text-xs font-semibold px-3 py-1">
                                <x-icon name="check-circle" class="h-3 w-3" /> Complete
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 backdrop-blur-sm ring-1 ring-white/20 text-white/80 text-xs font-semibold px-3 py-1">
                                Draft
                            </span>
                        @endif
                    </div>
                </div>
                <div class="flex gap-2 shrink-0">
                    <button type="button" x-data @click="$dispatch('open-modal', 'property-share')"
                            aria-label="Share property"
                            class="inline-flex items-center justify-center gap-2 h-11 w-11 sm:h-auto sm:w-auto sm:px-4 sm:py-2.5 rounded-full sm:rounded-2xl bg-white text-primary-700 hover:bg-white/90 text-sm font-semibold shadow-soft ring-1 ring-white/40 transition">
                        <x-icon name="share" class="h-5 w-5 sm:h-4 sm:w-4" />
                        <span class="hidden sm:inline">Share</span>
                    </button>
                    <a href="{{ route('properties.edit', $property) }}"
                       aria-label="Edit property"
                       class="inline-flex items-center justify-center gap-2 h-11 w-11 sm:h-auto sm:w-auto sm:px-4 sm:py-2.5 rounded-full sm:rounded-2xl bg-white/15 hover:bg-white/25 text-white text-sm font-semibold backdrop-blur-sm ring-1 ring-white/20 transition">
                        <x-icon name="pencil" class="h-5 w-5 sm:h-4 sm:w-4" />
                        <span class="hidden sm:inline">Edit</span>
                    </a>
                    <button type="button" x-data @click="$dispatch('open-modal', 'confirm-property-delete')"
                            aria-label="Delete property"
                            class="inline-flex items-center justify-center gap-2 h-11 w-11 sm:h-auto sm:w-auto sm:px-4 sm:py-2.5 rounded-full sm:rounded-2xl bg-red-500/90 hover:bg-red-500 text-white text-sm font-semibold ring-1 ring-red-300/40 transition">
                        <x-icon name="trash" class="h-5 w-5 sm:h-4 sm:w-4" />
                        <span class="hidden sm:inline">Delete</span>
                    </button>
                </div>
            </div>
        </x-page-hero>

        {{-- Flash messages --}}
        @php
            $flash = match (session('status')) {
                'property-created' => ['emerald', 'Property created.'],
                'property-updated' => ['emerald', 'Saved.'],
                'document-uploaded' => ['emerald', 'Document uploaded.'],
                'document-deleted' => ['neutral', 'Document removed.'],
                'photo-uploaded' => ['emerald', 'Photo uploaded.'],
                'photo-primary-set' => ['emerald', 'Primary photo updated.'],
                'photo-deleted' => ['neutral', 'Photo removed.'],
                'share-enabled' => ['emerald', 'Sharing enabled.'],
                'share-rotated' => ['emerald', 'Share link rotated. The previous QR no longer works.'],
                'share-disabled' => ['neutral', 'Sharing disabled.'],
                'share-visibility-updated' => ['emerald', 'Share visibility updated.'],
                default => null,
            };
        @endphp
        @if ($flash)
            @if ($flash[0] === 'emerald')
                <div class="rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 ring-1 ring-emerald-200 dark:ring-emerald-800 px-4 py-3 text-sm text-emerald-800 dark:text-emerald-200 flex items-center gap-2">
                    <x-icon name="check-circle" class="h-4 w-4" /> {{ $flash[1] }}
                </div>
            @else
                <div class="rounded-2xl bg-surface-100 dark:bg-gray-800 ring-1 ring-ink-100 dark:ring-ink-700 px-4 py-3 text-sm text-ink-700 dark:text-ink-300 flex items-center gap-2">
                    <x-icon name="information-circle" class="h-4 w-4" /> {{ $flash[1] }}
                </div>
            @endif
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                {{-- Financials stats hero --}}
                <section class="rounded-3xl bg-gradient-to-br from-emerald-50 to-sky-50 dark:from-emerald-950/30 dark:to-sky-950/30 ring-1 ring-emerald-100 dark:ring-white/10 p-6 sm:p-8 shadow-soft">
                    <div class="flex items-center gap-2">
                        <span class="h-9 w-9 rounded-xl bg-white dark:bg-gray-800 text-emerald-600 dark:text-emerald-300 flex items-center justify-center shadow-soft">
                            <x-icon name="currency-rupee" class="h-5 w-5" />
                        </span>
                        <h2 class="text-base font-semibold text-emerald-800 dark:text-emerald-200">Financials</h2>
                    </div>
                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">Imputed value</p>
                            <p class="mt-2 text-2xl sm:text-3xl font-extrabold tracking-tight text-ink-900 dark:text-ink-50 tabular-nums"><x-inr :amount="$property->imputed_value_inr" /></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">Rent / year</p>
                            <p class="mt-2 text-2xl sm:text-3xl font-extrabold tracking-tight text-ink-900 dark:text-ink-50 tabular-nums"><x-inr :amount="$property->rent_yearly_inr" /></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">Yield</p>
                            <p class="mt-2 text-2xl sm:text-3xl font-extrabold tracking-tight text-ink-900 dark:text-ink-50 tabular-nums">
                                {{ $property->effective_yield_percent ? $property->effective_yield_percent.'%' : '—' }}
                            </p>
                        </div>
                    </div>
                </section>

                {{-- Overview --}}
                <x-section-card title="Overview" icon="information-circle">
                    <dl class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <div class="rounded-2xl bg-surface-100 dark:bg-gray-900/40 p-4">
                            <dt class="text-[11px] sm:text-xs font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">Title holder</dt>
                            <dd class="mt-1 text-sm font-medium text-ink-900 dark:text-ink-50 break-words">{{ $property->title_holder ?: '—' }}</dd>
                        </div>
                        <div class="rounded-2xl bg-surface-100 dark:bg-gray-900/40 p-4">
                            <dt class="text-[11px] sm:text-xs font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">RERA number</dt>
                            <dd class="mt-1 text-sm font-medium text-ink-900 dark:text-ink-50 break-words">{{ $property->rera_number ?: '—' }}</dd>
                        </div>
                        <div class="rounded-2xl bg-surface-100 dark:bg-gray-900/40 p-4">
                            <dt class="text-[11px] sm:text-xs font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">Authority</dt>
                            <dd class="mt-1 text-sm font-medium text-ink-900 dark:text-ink-50 break-words">{{ $property->tenure_authority ?: '—' }}</dd>
                        </div>
                        <div class="rounded-2xl bg-surface-100 dark:bg-gray-900/40 p-4">
                            <dt class="text-[11px] sm:text-xs font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">Tenant / occupant</dt>
                            <dd class="mt-1 text-sm font-medium text-ink-900 dark:text-ink-50 break-words">{{ $property->tenant_or_occupant ?: '—' }}</dd>
                        </div>
                        <div class="rounded-2xl bg-surface-100 dark:bg-gray-900/40 p-4">
                            <dt class="text-[11px] sm:text-xs font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">Area</dt>
                            <dd class="mt-1 text-sm font-medium text-ink-900 dark:text-ink-50">
                                @if ($property->area_value)
                                    {{ rtrim(rtrim((string) $property->area_value, '0'), '.') }} {{ $property->area_unit ?: '' }}
                                @else
                                    —
                                @endif
                            </dd>
                        </div>
                        <div class="rounded-2xl bg-surface-100 dark:bg-gray-900/40 p-4 col-span-2 sm:col-span-3">
                            <dt class="text-[11px] sm:text-xs font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">Construction</dt>
                            <dd class="mt-1 text-sm font-medium text-ink-900 dark:text-ink-50 break-words">{{ $property->construction ?: '—' }}</dd>
                        </div>
                    </dl>
                </x-section-card>

                {{-- Notes --}}
                @if ($property->keys_location || $property->extra_notes)
                    <x-section-card title="Notes" icon="document-text">
                        <dl class="space-y-4 text-sm">
                            @if ($property->keys_location)
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400 flex items-center gap-1.5">
                                        <x-icon name="key" class="h-3.5 w-3.5" /> Keys & documents location
                                    </dt>
                                    <dd class="mt-1 text-ink-900 dark:text-ink-50">{{ $property->keys_location }}</dd>
                                </div>
                            @endif
                            @if ($property->extra_notes)
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">Extra notes</dt>
                                    <dd class="mt-1 text-ink-900 dark:text-ink-50 whitespace-pre-line">{{ $property->extra_notes }}</dd>
                                </div>
                            @endif
                        </dl>
                    </x-section-card>
                @endif

                @include('properties._documents', ['property' => $property])

                @include('properties._photos', ['property' => $property])
            </div>

            <aside class="space-y-6">
                {{-- Contacts --}}
                <x-section-card title="Contacts" icon="users" padding="p-6">
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
                                    @if (! empty($contact['phone']))
                                        <x-icon-button
                                            tone="primary"
                                            :href="'tel:'.preg_replace('/[^+0-9]/', '', $contact['phone'])"
                                            aria-label="Call {{ $contact['name'] ?? 'contact' }}"
                                            class="shrink-0"
                                        >
                                            <x-icon name="phone" class="h-5 w-5" />
                                        </x-icon-button>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-section-card>

                {{-- Audit --}}
                <section class="rounded-3xl bg-surface-100 dark:bg-gray-900/40 ring-1 ring-ink-100 dark:ring-ink-800 p-5 text-xs text-ink-500 dark:text-ink-400 space-y-2">
                    @if ($property->creator)
                        <div class="flex items-center gap-1.5">
                            <x-icon name="user" class="h-3 w-3" />
                            <span>Created by <span class="font-semibold text-ink-700 dark:text-ink-200">{{ $property->creator->name }}</span> · {{ $property->created_at?->diffForHumans() }}</span>
                        </div>
                    @else
                        <div class="flex items-center gap-1.5">
                            <x-icon name="calendar" class="h-3 w-3" />
                            <span>Created {{ $property->created_at?->diffForHumans() }}</span>
                        </div>
                    @endif
                    @if ($property->updater && $property->updated_by !== $property->created_by)
                        <div class="flex items-center gap-1.5">
                            <x-icon name="pencil" class="h-3 w-3" />
                            <span>Updated by <span class="font-semibold text-ink-700 dark:text-ink-200">{{ $property->updater->name }}</span> · {{ $property->updated_at?->diffForHumans() }}</span>
                        </div>
                    @endif
                </section>
            </aside>
        </div>

        @include('properties._share-modal', ['property' => $property])

        <x-modal name="confirm-property-delete" focusable>
            <form method="POST" action="{{ route('properties.destroy', $property) }}" class="p-7">
                @csrf
                @method('DELETE')
                <div class="flex items-start gap-4">
                    <span class="h-12 w-12 rounded-2xl bg-red-100 dark:bg-red-950/40 text-red-600 dark:text-red-300 flex items-center justify-center shrink-0">
                        <x-icon name="exclamation-triangle" class="h-6 w-6" />
                    </span>
                    <div class="flex-1">
                        <h2 class="text-lg font-bold text-ink-900 dark:text-ink-50">Delete this property?</h2>
                        <p class="mt-2 text-sm text-ink-500 dark:text-ink-400">
                            This soft-deletes <span class="font-semibold text-ink-700 dark:text-ink-200">{{ $property->name }}</span> — its data stays in the database for audit, but it disappears from the list.
                        </p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2.5 text-sm font-semibold text-ink-600 dark:text-ink-300 hover:bg-surface-100 dark:hover:bg-gray-700 rounded-xl transition">Cancel</button>
                    <x-danger-button type="submit">Delete property</x-danger-button>
                </div>
            </form>
        </x-modal>
    </div>
</x-app-layout>
