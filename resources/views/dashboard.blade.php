<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Dashboard</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Welcome back, {{ auth()->user()->name }}.</p>
        </div>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        {{-- Stat cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card label="Properties" :value="number_format($totalCount)" />
            <x-stat-card label="Total value">
                <x-slot name="value">
                    <x-inr :amount="$totalValue" />
                </x-slot>
            </x-stat-card>
            <x-stat-card label="Rent / yr">
                <x-slot name="value">
                    <x-inr :amount="$totalRent" />
                </x-slot>
            </x-stat-card>
            <x-stat-card
                label="Avg yield"
                :value="$averageYield !== null ? $averageYield.'%' : '—'"
                hint="Total rent ÷ total value"
            />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Recent properties --}}
            <section class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 rounded-2xl p-6 sm:p-8">
                <header class="flex items-center justify-between gap-3 mb-4">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Recent properties</h2>
                    @if ($totalCount > 0)
                        <a href="{{ route('properties.index') }}" class="text-xs font-medium text-primary-700 dark:text-primary-300 hover:underline">View all →</a>
                    @endif
                </header>

                @if ($recent->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">No properties yet. Add your first one to get started.</p>
                @else
                    <ul class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        @foreach ($recent as $property)
                            <li>
                                <a href="{{ route('properties.show', $property) }}" class="flex items-center justify-between gap-3 py-3 -mx-2 px-2 rounded-lg hover:bg-primary-50/40 dark:hover:bg-primary-950/20 transition">
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900 dark:text-gray-100 truncate">{{ $property->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $property->city ?: '—' }}{{ $property->state ? ' · '.$property->state : '' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100"><x-inr :amount="$property->imputed_value_inr" /></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $property->created_at?->diffForHumans() }}</p>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            {{-- CTA card --}}
            <section class="rounded-2xl p-6 sm:p-8 bg-gradient-to-br from-primary-600 to-primary-800 dark:from-primary-700 dark:to-primary-900 text-white shadow-md ring-1 ring-primary-900/10 flex flex-col">
                <h2 class="text-lg font-semibold">Add a property</h2>
                <p class="mt-2 text-sm text-primary-100">
                    Capture address, tenure, financials, contacts, and documents in one place — searchable forever.
                </p>
                <div class="mt-auto pt-6">
                    <a href="{{ route('properties.create') }}" class="inline-flex items-center px-4 py-2 bg-white text-primary-700 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-accent-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-700 transition">
                        + New property
                    </a>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
