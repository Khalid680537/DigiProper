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
        'vacant_built' => 'Vacant',
    ];
    $occupancyTone = [
        'rented_out' => 'accent',
        'self_use' => 'primary',
        'vacant_plot' => 'neutral',
        'vacant_built' => 'neutral',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Properties</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Your portfolio at a glance.</p>
            </div>
            <a href="{{ route('properties.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 dark:bg-primary-500 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 dark:hover:bg-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition">
                + New property
            </a>
        </div>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        @if (session('status') === 'property-deleted')
            <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 px-4 py-3 text-sm text-emerald-800 dark:text-emerald-200">
                Property deleted.
            </div>
        @endif

        <form method="GET" action="{{ route('properties.index') }}" class="flex gap-3">
            <x-text-input
                type="search"
                name="q"
                value="{{ $q }}"
                placeholder="Search by name, city, or address…"
                class="flex-1"
            />
            <x-primary-button type="submit">Search</x-primary-button>
            @if ($q !== '')
                <a href="{{ route('properties.index') }}" class="inline-flex items-center px-3 py-2 text-sm text-gray-600 dark:text-gray-300 hover:underline">Clear</a>
            @endif
        </form>

        @if ($properties->total() === 0)
            <x-empty-state
                title="{{ $q !== '' ? 'No matches' : 'No properties yet' }}"
                description="{{ $q !== '' ? 'Try a different search.' : 'Add your first property to start tracking your portfolio.' }}"
                :ctaHref="$q === '' ? route('properties.create') : null"
                :ctaLabel="$q === '' ? '+ New property' : null"
            />
        @else
            {{-- Desktop table --}}
            <div class="hidden md:block overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50/80 dark:bg-gray-900/50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Location</th>
                            <th class="px-4 py-3">Tenure</th>
                            <th class="px-4 py-3">Occupancy</th>
                            <th class="px-4 py-3 text-right">Value</th>
                            <th class="px-4 py-3 text-right">Rent / yr</th>
                            <th class="px-4 py-3 text-right">Yield</th>
                            <th class="px-4 py-3"><span class="sr-only">Status</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        @foreach ($properties as $property)
                            <tr class="hover:bg-primary-50/40 dark:hover:bg-primary-950/20 cursor-pointer" onclick="window.location='{{ route('properties.show', $property) }}'">
                                <td class="px-4 py-3">
                                    <a href="{{ route('properties.show', $property) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:text-primary-700 dark:hover:text-primary-300">
                                        {{ $property->name }}
                                    </a>
                                    @if ($property->title_holder)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ $property->title_holder }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                    {{ $property->city ?: '—' }}
                                    @if ($property->state)
                                        <span class="text-gray-400">·</span> {{ $property->state }}
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($property->tenure)
                                        <x-status-badge :tone="$tenureTone[$property->tenure] ?? 'neutral'" :label="$tenureLabels[$property->tenure] ?? $property->tenure" />
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($property->occupancy_status)
                                        <x-status-badge :tone="$occupancyTone[$property->occupancy_status] ?? 'neutral'" :label="$occupancyLabels[$property->occupancy_status] ?? $property->occupancy_status" />
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right text-gray-900 dark:text-gray-100">
                                    <x-inr :amount="$property->imputed_value_inr" />
                                </td>
                                <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">
                                    <x-inr :amount="$property->rent_yearly_inr" />
                                </td>
                                <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300 tabular-nums">
                                    {{ $property->effective_yield_percent ? $property->effective_yield_percent.'%' : '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    @if ($property->is_data_complete)
                                        <x-status-badge tone="success" label="Complete" />
                                    @else
                                        <x-status-badge tone="neutral" label="Draft" />
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile cards --}}
            <div class="md:hidden space-y-3">
                @foreach ($properties as $property)
                    <a href="{{ route('properties.show', $property) }}" class="block rounded-2xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 p-4 hover:ring-primary-300 dark:hover:ring-primary-700 transition">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $property->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $property->city ?: '—' }}{{ $property->state ? ' · '.$property->state : '' }}</p>
                            </div>
                            @if ($property->occupancy_status)
                                <x-status-badge :tone="$occupancyTone[$property->occupancy_status] ?? 'neutral'" :label="$occupancyLabels[$property->occupancy_status] ?? $property->occupancy_status" />
                            @endif
                        </div>
                        <dl class="mt-4 grid grid-cols-3 gap-2 text-xs">
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Value</dt>
                                <dd class="font-medium text-gray-900 dark:text-gray-100"><x-inr :amount="$property->imputed_value_inr" /></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Rent / yr</dt>
                                <dd class="font-medium text-gray-900 dark:text-gray-100"><x-inr :amount="$property->rent_yearly_inr" /></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Yield</dt>
                                <dd class="font-medium text-gray-900 dark:text-gray-100 tabular-nums">{{ $property->effective_yield_percent ? $property->effective_yield_percent.'%' : '—' }}</dd>
                            </div>
                        </dl>
                    </a>
                @endforeach
            </div>

            <div>
                {{ $properties->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
