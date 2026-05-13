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

<x-app-layout :title="'Properties'">
    <div class="space-y-6">
        {{-- Hero --}}
        <x-page-hero tone="purple" compact>
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-white/70">Your portfolio</p>
                    <h1 class="mt-1 text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                        {{ $properties->total() }} {{ \Illuminate\Support\Str::plural('property', $properties->total()) }}
                    </h1>
                </div>
                <a href="{{ route('properties.create') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white text-primary-700 px-5 py-2.5 text-sm font-semibold shadow-soft hover:-translate-y-0.5 hover:shadow-glow transition ease-spring duration-150">
                    <x-icon name="plus" class="h-4 w-4" />
                    New property
                </a>
            </div>
        </x-page-hero>

        {{-- Flash --}}
        @if (session('status') === 'property-deleted')
            <div class="rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 ring-1 ring-emerald-200 dark:ring-emerald-800 px-4 py-3 text-sm text-emerald-800 dark:text-emerald-200 flex items-center gap-2">
                <x-icon name="check-circle" class="h-4 w-4" />
                Property deleted.
            </div>
        @endif

        {{-- Search --}}
        <form method="GET" action="{{ route('properties.index') }}" class="relative">
            <x-icon name="search" class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-ink-400" />
            <input
                type="search"
                name="q"
                value="{{ $q }}"
                placeholder="Search by name, city, or address…"
                class="block w-full rounded-2xl border-ink-200 dark:border-ink-700 dark:bg-gray-900 dark:text-ink-100 placeholder:text-ink-400 pl-12 pr-32 py-3.5 text-sm shadow-soft focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 transition"
            />
            <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 inline-flex items-center gap-1.5 rounded-xl bg-primary-600 hover:bg-primary-500 text-white px-4 py-2 text-xs font-semibold shadow-glow-sm transition">
                Search
            </button>
            @if ($q !== '')
                <a href="{{ route('properties.index') }}" class="absolute -bottom-7 right-0 inline-flex items-center gap-1 text-xs text-ink-500 hover:text-primary-600">
                    <x-icon name="x-mark" class="h-3 w-3" /> Clear search
                </a>
            @endif
        </form>

        @if ($properties->total() === 0)
            <x-empty-state
                icon="home-modern"
                :title="$q !== '' ? 'No matches' : 'Let\'s add your first property'"
                :description="$q !== '' ? 'Try a different search term.' : 'Capture address, tenure, financials, contacts and documents — all in one place, searchable forever.'"
                :ctaHref="$q === '' ? route('properties.create') : null"
                :ctaLabel="$q === '' ? 'Add property' : null"
            />
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach ($properties as $property)
                    @php $initial = strtoupper(mb_substr($property->name, 0, 1)); @endphp
                    <a href="{{ route('properties.show', $property) }}"
                       class="group rounded-3xl bg-white dark:bg-gray-800 ring-1 ring-gray-900/5 dark:ring-white/10 shadow-soft hover:-translate-y-0.5 hover:shadow-glow transition ease-spring duration-150 overflow-hidden block">
                        {{-- Top strip: photo when available, gradient otherwise --}}
                        @php $primary = $property->primaryPhoto; @endphp
                        <div class="relative {{ $primary ? 'h-28' : 'h-20' }} @if (! $primary) bg-gradient-to-br from-primary-500 via-primary-600 to-primary-800 @endif">
                            @if ($primary)
                                <img src="{{ route('properties.photos.show', [$property, $primary]) }}"
                                     alt="{{ $property->name }}"
                                     class="absolute inset-0 h-full w-full object-cover" />
                                <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-t from-ink-950/45 via-transparent to-transparent"></div>
                            @else
                                <div aria-hidden="true" class="absolute -top-8 -right-8 h-24 w-24 rounded-full bg-white/15 blur-2xl"></div>
                            @endif
                            <div class="absolute -bottom-5 left-5 h-14 w-14 rounded-2xl bg-white dark:bg-gray-800 ring-4 ring-white dark:ring-gray-800 flex items-center justify-center text-xl font-extrabold text-primary-700 dark:text-primary-300 shadow-soft">
                                {{ $initial }}
                            </div>
                            @if ($property->is_data_complete)
                                <span class="absolute top-3 left-3 inline-flex items-center gap-1 rounded-full bg-white/20 backdrop-blur-sm text-white text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 ring-1 ring-white/30">
                                    <x-icon name="check-circle" class="h-3 w-3" /> Complete
                                </span>
                            @else
                                <span class="absolute top-3 left-3 inline-flex items-center rounded-full bg-white/15 backdrop-blur-sm text-white text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 ring-1 ring-white/30">
                                    Draft
                                </span>
                            @endif
                            <div class="absolute top-3 right-3">
                                <x-qr-thumb :property="$property" :size="44" :linked="false" />
                            </div>
                        </div>

                        <div class="pt-8 p-5">
                            <h3 class="font-semibold text-ink-900 dark:text-ink-50 truncate group-hover:text-primary-700 dark:group-hover:text-primary-300 transition">
                                {{ $property->name }}
                            </h3>
                            <p class="mt-0.5 text-xs text-ink-500 dark:text-ink-400 truncate flex items-center gap-1">
                                <x-icon name="map-pin" class="h-3 w-3 shrink-0" />
                                {{ $property->city ?: '—' }}{{ $property->state ? ' · '.$property->state : '' }}
                            </p>

                            <dl class="mt-4 grid grid-cols-3 gap-2">
                                <div>
                                    <dt class="text-[10px] font-semibold uppercase tracking-wide text-ink-400 dark:text-ink-500">Value</dt>
                                    <dd class="mt-0.5 text-sm font-semibold text-ink-900 dark:text-ink-50 truncate"><x-inr :amount="$property->imputed_value_inr" /></dd>
                                </div>
                                <div>
                                    <dt class="text-[10px] font-semibold uppercase tracking-wide text-ink-400 dark:text-ink-500">Rent / yr</dt>
                                    <dd class="mt-0.5 text-sm font-semibold text-ink-900 dark:text-ink-50 truncate"><x-inr :amount="$property->rent_yearly_inr" /></dd>
                                </div>
                                <div>
                                    <dt class="text-[10px] font-semibold uppercase tracking-wide text-ink-400 dark:text-ink-500">Yield</dt>
                                    <dd class="mt-0.5 text-sm font-semibold text-ink-900 dark:text-ink-50 tabular-nums">
                                        {{ $property->effective_yield_percent ? $property->effective_yield_percent.'%' : '—' }}
                                    </dd>
                                </div>
                            </dl>

                            <div class="mt-4 flex flex-wrap gap-1.5">
                                @if ($property->tenure)
                                    <x-status-badge :tone="$tenureTone[$property->tenure] ?? 'neutral'" :label="$tenureLabels[$property->tenure] ?? $property->tenure" />
                                @endif
                                @if ($property->occupancy_status)
                                    <x-status-badge :tone="$occupancyTone[$property->occupancy_status] ?? 'neutral'" :label="$occupancyLabels[$property->occupancy_status] ?? $property->occupancy_status" />
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="pt-2">
                {{ $properties->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
