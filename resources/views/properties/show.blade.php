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
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <a href="{{ route('properties.index') }}" class="text-xs text-gray-500 dark:text-gray-400 hover:underline">← All properties</a>
                <h1 class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $property->name }}</h1>
                @if ($addressLines)
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ implode(' · ', $addressLines) }}</p>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('properties.edit', $property) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition">
                    Edit
                </a>
                <button type="button" x-data @click="$dispatch('open-modal', 'confirm-property-delete')" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition">
                    Delete
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        @if (session('status') === 'property-created')
            <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 px-4 py-3 text-sm text-emerald-800 dark:text-emerald-200">
                Property created.
            </div>
        @elseif (session('status') === 'property-updated')
            <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 px-4 py-3 text-sm text-emerald-800 dark:text-emerald-200">
                Saved.
            </div>
        @elseif (session('status') === 'document-uploaded')
            <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 px-4 py-3 text-sm text-emerald-800 dark:text-emerald-200">
                Document uploaded.
            </div>
        @elseif (session('status') === 'document-deleted')
            <div class="rounded-xl bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                Document removed.
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                {{-- Summary --}}
                <section class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 rounded-2xl p-6 sm:p-8">
                    <h2 class="text-sm font-semibold uppercase tracking-widest text-primary-700 dark:text-primary-300">Summary</h2>
                    <dl class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Imputed value</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100"><x-inr :amount="$property->imputed_value_inr" /></dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Rent / year</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100"><x-inr :amount="$property->rent_yearly_inr" /></dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Yield</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100 tabular-nums">
                                {{ $property->effective_yield_percent ? $property->effective_yield_percent.'%' : '—' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Area</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                @if ($property->area_value)
                                    {{ rtrim(rtrim((string) $property->area_value, '0'), '.') }} {{ $property->area_unit ?: '' }}
                                @else
                                    —
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Tenure</dt>
                            <dd class="mt-1">
                                @if ($property->tenure)
                                    <x-status-badge :tone="$tenureTone[$property->tenure] ?? 'neutral'" :label="$tenureLabels[$property->tenure] ?? $property->tenure" />
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Occupancy</dt>
                            <dd class="mt-1">
                                @if ($property->occupancy_status)
                                    <x-status-badge :tone="$occupancyTone[$property->occupancy_status] ?? 'neutral'" :label="$occupancyLabels[$property->occupancy_status] ?? $property->occupancy_status" />
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </section>

                {{-- Tenure & Title --}}
                <section class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 rounded-2xl p-6 sm:p-8">
                    <h2 class="text-sm font-semibold uppercase tracking-widest text-primary-700 dark:text-primary-300">Title & tenure</h2>
                    <dl class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Title holder</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $property->title_holder ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">RERA number</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $property->rera_number ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Authority / landlord</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $property->tenure_authority ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Tenant / occupant</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $property->tenant_or_occupant ?: '—' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Construction</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $property->construction ?: '—' }}</dd>
                        </div>
                    </dl>
                </section>

                {{-- Notes --}}
                @if ($property->keys_location || $property->extra_notes)
                    <section class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 rounded-2xl p-6 sm:p-8">
                        <h2 class="text-sm font-semibold uppercase tracking-widest text-primary-700 dark:text-primary-300">Notes</h2>
                        <dl class="mt-4 space-y-4 text-sm">
                            @if ($property->keys_location)
                                <div>
                                    <dt class="text-xs text-gray-500 dark:text-gray-400">Keys & documents location</dt>
                                    <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $property->keys_location }}</dd>
                                </div>
                            @endif
                            @if ($property->extra_notes)
                                <div>
                                    <dt class="text-xs text-gray-500 dark:text-gray-400">Extra notes</dt>
                                    <dd class="mt-1 text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ $property->extra_notes }}</dd>
                                </div>
                            @endif
                        </dl>
                    </section>
                @endif

                @include('properties._documents', ['property' => $property])
            </div>

            <aside class="space-y-6">
                {{-- Contacts --}}
                <section class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 rounded-2xl p-6">
                    <h2 class="text-sm font-semibold uppercase tracking-widest text-primary-700 dark:text-primary-300">Contacts</h2>
                    @if (empty($property->contacts))
                        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400 italic">No contacts on file.</p>
                    @else
                        <ul class="mt-3 space-y-3">
                            @foreach ($property->contacts as $contact)
                                @php
                                    $name = trim($contact['name'] ?? '');
                                    $phone = trim($contact['phone'] ?? '');
                                    $role = trim($contact['role'] ?? '');
                                    $notes = trim($contact['notes'] ?? '');
                                    if ($name === '' && $phone === '' && $role === '' && $notes === '') continue;
                                @endphp
                                <li class="text-sm">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $name ?: '—' }}</div>
                                    @if ($role)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $role }}</div>
                                    @endif
                                    @if ($phone)
                                        <a href="tel:{{ preg_replace('/[^+0-9]/', '', $phone) }}" class="block text-primary-700 dark:text-primary-300 hover:underline tabular-nums">{{ $phone }}</a>
                                    @endif
                                    @if ($notes)
                                        <div class="text-xs text-gray-600 dark:text-gray-300 mt-0.5">{{ $notes }}</div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                {{-- Audit --}}
                <section class="bg-gray-50 dark:bg-gray-900/40 ring-1 ring-gray-900/5 dark:ring-white/10 rounded-2xl p-5 text-xs text-gray-500 dark:text-gray-400 space-y-2">
                    <div class="flex items-center justify-between">
                        <span>Status</span>
                        @if ($property->is_data_complete)
                            <x-status-badge tone="success" label="Complete" />
                        @else
                            <x-status-badge tone="neutral" label="Draft" />
                        @endif
                    </div>
                    @if ($property->creator)
                        <div>Created by <span class="text-gray-700 dark:text-gray-300">{{ $property->creator->name }}</span> · {{ $property->created_at?->diffForHumans() }}</div>
                    @else
                        <div>Created {{ $property->created_at?->diffForHumans() }}</div>
                    @endif
                    @if ($property->updater && $property->updated_by !== $property->created_by)
                        <div>Last updated by <span class="text-gray-700 dark:text-gray-300">{{ $property->updater->name }}</span> · {{ $property->updated_at?->diffForHumans() }}</div>
                    @endif
                </section>
            </aside>
        </div>

        <x-modal name="confirm-property-delete" focusable>
            <form method="POST" action="{{ route('properties.destroy', $property) }}" class="p-6">
                @csrf
                @method('DELETE')
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Delete this property?</h2>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    This soft-deletes the property — its data stays in the database for audit, but it disappears from the list.
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close')" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:underline">Cancel</button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 transition">
                        Delete
                    </button>
                </div>
            </form>
        </x-modal>
    </div>
</x-app-layout>
