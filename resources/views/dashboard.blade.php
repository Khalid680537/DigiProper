@php
    $hour = (int) now()->setTimezone(config('app.timezone'))->format('G');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $user = auth()->user();
    $initial = strtoupper(mb_substr($user->name, 0, 1));
@endphp

<x-app-layout :title="'Dashboard'">
    <div class="space-y-7">
        {{-- Greeting hero --}}
        <x-page-hero tone="purple">
            <div class="flex flex-col sm:flex-row sm:items-center gap-5">
                <div class="h-14 w-14 rounded-2xl bg-white/15 ring-2 ring-white/25 flex items-center justify-center text-2xl font-extrabold text-white shrink-0">
                    {{ $initial }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm text-white/80">{{ $greeting }},</p>
                    <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white truncate">{{ $user->name }}.</h1>
                    <p class="mt-1 text-sm text-white/85">
                        @if ($totalCount === 0)
                            Ready to add your first property?
                        @else
                            {{ $totalCount }} {{ \Illuminate\Support\Str::plural('property', $totalCount) }} ·
                            <span class="font-semibold"><x-inr :amount="$totalValue" /></span> total value
                        @endif
                    </p>
                </div>
                <a href="{{ route('properties.create') }}"
                   class="sm:ml-auto inline-flex items-center justify-center gap-2 rounded-2xl bg-white text-primary-700 px-5 py-3 text-sm font-semibold shadow-soft hover:-translate-y-0.5 hover:shadow-glow transition ease-spring duration-150 whitespace-nowrap">
                    <x-icon name="plus" class="h-4 w-4" />
                    Add property
                </a>
            </div>
        </x-page-hero>

        {{-- Stat strip --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card icon="building-office" tint="primary" label="Properties" :value="number_format($totalCount)"
                :href="route('properties.index')" />
            <x-stat-card icon="currency-rupee" tint="accent" label="Total value"
                :href="route('properties.index', ['sort' => 'value_desc'])">
                <x-slot name="value"><x-inr :amount="$totalValue" /></x-slot>
            </x-stat-card>
            <x-stat-card icon="banknotes" tint="emerald" label="Rent / yr"
                :href="route('properties.index', ['occupancy' => 'rented_out'])">
                <x-slot name="value"><x-inr :amount="$totalRent" /></x-slot>
            </x-stat-card>
            <x-stat-card icon="sparkles" tint="sky" label="Avg yield"
                :value="$averageYield !== null ? $averageYield.'%' : '—'"
                hint="Rent ÷ value"
                :href="route('properties.index', ['sort' => 'yield_desc'])" />
        </div>

        {{-- Quick action tiles --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <a href="{{ route('properties.create') }}"
               class="group rounded-3xl p-4 sm:p-5 bg-gradient-to-br from-primary-500 to-primary-700 text-white shadow-soft hover:-translate-y-0.5 hover:shadow-glow transition ease-spring duration-150 ring-1 ring-white/10 active:scale-[0.98]">
                <span class="h-10 w-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <x-icon name="plus" class="h-5 w-5" />
                </span>
                <p class="mt-3 text-sm font-semibold">Add property</p>
                <p class="text-xs text-white/80">Capture every detail</p>
            </a>
            <a href="{{ route('properties.index', ['has' => 'documents']) }}"
               class="group rounded-3xl p-4 sm:p-5 bg-gradient-to-br from-accent-500 to-accent-700 text-white shadow-soft hover:-translate-y-0.5 hover:shadow-glow transition ease-spring duration-150 ring-1 ring-white/10 active:scale-[0.98]">
                <span class="h-10 w-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <x-icon name="folder-open" class="h-5 w-5" />
                </span>
                <p class="mt-3 text-sm font-semibold">Documents</p>
                <p class="text-xs text-white/80">Title deeds, leases</p>
            </a>
            <a href="{{ route('properties.index', ['sort' => 'recent']) }}"
               class="group rounded-3xl p-4 sm:p-5 bg-gradient-to-br from-emerald-500 to-emerald-700 text-white shadow-soft hover:-translate-y-0.5 hover:shadow-glow transition ease-spring duration-150 ring-1 ring-white/10 active:scale-[0.98]">
                <span class="h-10 w-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <x-icon name="calendar" class="h-5 w-5" />
                </span>
                <p class="mt-3 text-sm font-semibold">Recent</p>
                <p class="text-xs text-white/80">Latest additions</p>
            </a>
            <button type="button" x-data x-on:click="$dispatch('open-command-palette')"
                    class="group text-left rounded-3xl p-4 sm:p-5 bg-gradient-to-br from-sky-500 to-sky-700 text-white shadow-soft hover:-translate-y-0.5 hover:shadow-glow transition ease-spring duration-150 ring-1 ring-white/10 active:scale-[0.98]">
                <span class="h-10 w-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <x-icon name="search" class="h-5 w-5" />
                </span>
                <p class="mt-3 text-sm font-semibold">Search</p>
                <p class="text-xs text-white/80">Find by city or name</p>
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Recent properties --}}
            <section class="lg:col-span-2 rounded-3xl bg-white dark:bg-gray-800 shadow-soft ring-1 ring-gray-900/5 dark:ring-white/10 p-5 sm:p-7">
                <header class="flex items-center justify-between gap-3 mb-4">
                    <div class="flex items-center gap-3">
                        <span class="h-9 w-9 rounded-xl bg-primary-50 dark:bg-primary-950/40 text-primary-600 dark:text-primary-300 flex items-center justify-center">
                            <x-icon name="building-office" class="h-5 w-5" />
                        </span>
                        <h2 class="text-base font-semibold text-ink-900 dark:text-ink-50">Recent properties</h2>
                    </div>
                    @if ($totalCount > 0)
                        <a href="{{ route('properties.index') }}" class="text-xs font-semibold text-primary-700 dark:text-primary-300 hover:underline whitespace-nowrap">View all →</a>
                    @endif
                </header>

                @if ($recent->isEmpty())
                    <div class="rounded-2xl bg-surface-100 dark:bg-gray-900/40 p-6 text-center">
                        <p class="text-sm text-ink-500 dark:text-ink-400">No properties yet. Add your first one to get started.</p>
                        <a href="{{ route('properties.create') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-primary-700 dark:text-primary-300 hover:underline">
                            <x-icon name="plus" class="h-4 w-4" />
                            Add property
                        </a>
                    </div>
                @else
                    {{-- Mobile carousel --}}
                    <div class="lg:hidden relative">
                        <div class="flex gap-3 overflow-x-auto snap-x snap-mandatory scrollbar-hide -mx-5 px-5 pb-1">
                        @foreach ($recent as $property)
                            @php
                                $bgInit = strtoupper(mb_substr($property->name, 0, 1));
                                $recentPrimary = $property->primaryPhoto;
                            @endphp
                            <a href="{{ route('properties.show', $property) }}"
                               class="shrink-0 w-[78vw] max-w-[300px] snap-start rounded-2xl bg-surface-50 dark:bg-gray-900/50 ring-1 ring-ink-100 dark:ring-ink-800 p-4 hover:ring-primary-300 dark:hover:ring-primary-700 transition active:scale-[0.99]">
                                <div class="flex items-center gap-3">
                                    @if ($recentPrimary)
                                        <img src="{{ route('properties.photos.show', [$property, $recentPrimary]) }}"
                                             alt="{{ $property->name }}"
                                             class="h-10 w-10 rounded-xl object-cover shrink-0 ring-1 ring-ink-100 dark:ring-ink-800" />
                                    @else
                                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-primary-400 to-primary-700 text-white font-bold flex items-center justify-center shrink-0">{{ $bgInit }}</div>
                                    @endif
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold text-ink-900 dark:text-ink-50 truncate">{{ $property->name }}</p>
                                        <p class="text-xs text-ink-500 dark:text-ink-400 truncate">{{ $property->city ?: '—' }}{{ $property->state ? ' · '.$property->state : '' }}</p>
                                    </div>
                                </div>
                                <div class="mt-3 flex items-end justify-between">
                                    <p class="text-sm font-semibold text-ink-900 dark:text-ink-50"><x-inr :amount="$property->imputed_value_inr" /></p>
                                    <p class="text-[11px] text-ink-500 dark:text-ink-400">{{ $property->created_at?->diffForHumans() }}</p>
                                </div>
                            </a>
                        @endforeach
                        </div>
                        @if ($recent->count() > 1)
                            <div aria-hidden="true" class="pointer-events-none absolute right-0 top-0 bottom-1 w-8 bg-gradient-to-l from-white dark:from-gray-800 to-transparent"></div>
                            <p class="mt-2 text-[11px] text-ink-400 dark:text-ink-500 inline-flex items-center gap-1">
                                <x-icon name="arrow-right" class="h-3 w-3" /> Swipe for more
                            </p>
                        @endif
                    </div>

                    {{-- Desktop vertical list --}}
                    <ul class="hidden lg:block divide-y divide-ink-100 dark:divide-ink-800">
                        @foreach ($recent as $property)
                            @php
                                $bgInit = strtoupper(mb_substr($property->name, 0, 1));
                                $recentPrimary = $property->primaryPhoto;
                            @endphp
                            <li>
                                <a href="{{ route('properties.show', $property) }}" class="flex items-center justify-between gap-3 py-3 -mx-2 px-3 rounded-xl hover:bg-primary-50/60 dark:hover:bg-primary-950/30 transition">
                                    <div class="flex items-center gap-3 min-w-0">
                                        @if ($recentPrimary)
                                            <img src="{{ route('properties.photos.show', [$property, $recentPrimary]) }}"
                                                 alt="{{ $property->name }}"
                                                 class="h-10 w-10 rounded-xl object-cover shrink-0 ring-1 ring-ink-100 dark:ring-ink-800" />
                                        @else
                                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-primary-400 to-primary-700 text-white font-bold flex items-center justify-center shrink-0">{{ $bgInit }}</div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="font-semibold text-ink-900 dark:text-ink-50 truncate">{{ $property->name }}</p>
                                            <p class="text-xs text-ink-500 dark:text-ink-400 truncate">{{ $property->city ?: '—' }}{{ $property->state ? ' · '.$property->state : '' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 shrink-0">
                                        <div class="text-right">
                                            <p class="text-sm font-semibold text-ink-900 dark:text-ink-50"><x-inr :amount="$property->imputed_value_inr" /></p>
                                            <p class="text-xs text-ink-500 dark:text-ink-400">{{ $property->created_at?->diffForHumans() }}</p>
                                        </div>
                                        <x-qr-thumb :property="$property" :size="36" :linked="false" />
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            {{-- Tip card --}}
            <section class="rounded-3xl p-6 bg-gradient-to-br from-accent-50 to-primary-50 dark:from-accent-950/40 dark:to-primary-950/40 ring-1 ring-primary-100 dark:ring-primary-900/40 shadow-soft">
                <span class="h-10 w-10 rounded-xl bg-white dark:bg-gray-800 text-primary-600 dark:text-primary-300 flex items-center justify-center shadow-soft">
                    <x-icon name="light-bulb" class="h-5 w-5" />
                </span>
                <h3 class="mt-4 font-semibold text-ink-900 dark:text-ink-50">Tip</h3>
                <p class="mt-1 text-sm text-ink-600 dark:text-ink-300">
                    Properties marked <span class="font-semibold text-primary-700 dark:text-primary-300">Complete</span> get yield calculations automatically — set rent and value and tick the box when editing.
                </p>
            </section>
        </div>
    </div>
</x-app-layout>
