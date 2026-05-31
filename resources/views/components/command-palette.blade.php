{{-- Global ⌘K command palette. Single instance mounted from the app layout. --}}
<div
    x-data="commandPalette({
        searchUrl: '{{ route('search') }}',
        quickNav: [
            { label: 'Dashboard',    url: '{{ route('dashboard') }}',          icon: 'home' },
            { label: 'Properties',   url: '{{ route('properties.index') }}',   icon: 'folder-open' },
            { label: 'New property', url: '{{ route('properties.create') }}',  icon: 'plus' },
            { label: 'Profile',      url: '{{ route('profile.edit') }}',       icon: 'user' },
        ],
    })"
    x-on:keydown.window.meta.k.prevent="toggle()"
    x-on:keydown.window.ctrl.k.prevent="toggle()"
    x-on:open-command-palette.window="openPalette()"
    x-on:keydown.escape.window="open && close()"
    x-cloak
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition.opacity.duration.150ms
        class="fixed inset-0 z-40 bg-ink-950/60 backdrop-blur-sm"
        x-on:click="close()"
    ></div>

    {{-- Panel --}}
    <div
        x-show="open"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
        class="fixed inset-x-0 top-20 z-50 mx-auto w-full max-w-2xl px-4"
        x-on:click.self="close()"
        x-on:keydown.down.prevent="moveActive(1)"
        x-on:keydown.up.prevent="moveActive(-1)"
        x-on:keydown.enter.prevent="activate()"
    >
        <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-soft-lg ring-1 ring-ink-100 dark:ring-ink-800">
            {{-- Input row --}}
            <div class="flex items-center gap-3 px-5 py-4 border-b border-ink-100 dark:border-ink-800">
                <x-icon name="search" class="h-5 w-5 text-ink-400 dark:text-ink-500 shrink-0" />
                <input
                    x-ref="searchInput"
                    x-model="query"
                    x-on:input.debounce.150ms="runSearch()"
                    type="text"
                    placeholder="Search properties or jump to a page…"
                    class="flex-1 bg-transparent border-0 focus:ring-0 text-base text-ink-800 dark:text-ink-100 placeholder-ink-400 dark:placeholder-ink-500 p-0"
                    autocomplete="off"
                    spellcheck="false"
                />
                <span x-show="loading" class="text-xs text-ink-400 dark:text-ink-500">Searching…</span>
                <kbd class="hidden sm:inline-flex items-center px-1.5 py-0.5 rounded bg-surface-100 dark:bg-gray-900 ring-1 ring-ink-200 dark:ring-ink-700 text-[10px] font-semibold text-ink-500 dark:text-ink-400">esc</kbd>
                <button
                    type="button"
                    x-on:click="close()"
                    aria-label="Close search"
                    class="shrink-0 rounded-xl p-1.5 text-ink-400 hover:text-ink-700 hover:bg-surface-100 dark:hover:text-ink-200 dark:hover:bg-gray-700 transition"
                >
                    <x-icon name="x-mark" class="h-5 w-5" />
                </button>
            </div>

            {{-- Result list --}}
            <div class="max-h-96 overflow-y-auto py-2">
                {{-- Quick nav (when query is empty) --}}
                <template x-if="query.trim() === ''">
                    <div>
                        <p class="px-5 pt-1 pb-2 text-[11px] font-semibold uppercase tracking-wide text-ink-400 dark:text-ink-500">Jump to</p>
                        <template x-for="(item, idx) in quickNav" :key="'nav-' + idx">
                            <a
                                :href="item.url"
                                :class="activeIndex === idx
                                    ? 'bg-purple-50 dark:bg-purple-900/20 text-ink-900 dark:text-white'
                                    : 'text-ink-700 dark:text-ink-200 hover:bg-surface-100 dark:hover:bg-gray-700/40'"
                                class="flex items-center gap-3 px-5 py-2.5 text-sm transition-colors"
                                x-on:mouseenter="activeIndex = idx"
                            >
                                <span
                                    :class="activeIndex === idx
                                        ? 'bg-hero-purple text-white'
                                        : 'bg-surface-100 dark:bg-gray-700 text-ink-500 dark:text-ink-300'"
                                    class="flex h-7 w-7 items-center justify-center rounded-lg transition-colors"
                                >
                                    <template x-if="item.icon === 'home'"><x-icon name="home" class="h-4 w-4" /></template>
                                    <template x-if="item.icon === 'folder-open'"><x-icon name="folder-open" class="h-4 w-4" /></template>
                                    <template x-if="item.icon === 'plus'"><x-icon name="plus" class="h-4 w-4" /></template>
                                    <template x-if="item.icon === 'user'"><x-icon name="user" class="h-4 w-4" /></template>
                                </span>
                                <span class="flex-1" x-text="item.label"></span>
                                <x-icon name="arrow-right" class="h-4 w-4 text-ink-300 dark:text-ink-600" x-show="activeIndex === idx" />
                            </a>
                        </template>
                    </div>
                </template>

                {{-- Property results (when querying) --}}
                <template x-if="query.trim() !== ''">
                    <div>
                        <template x-if="results.length > 0">
                            <div>
                                <p class="px-5 pt-1 pb-2 text-[11px] font-semibold uppercase tracking-wide text-ink-400 dark:text-ink-500">Properties</p>
                                <template x-for="(item, idx) in results" :key="'prop-' + item.id">
                                    <a
                                        :href="item.url"
                                        :class="activeIndex === idx
                                            ? 'bg-purple-50 dark:bg-purple-900/20 text-ink-900 dark:text-white'
                                            : 'text-ink-700 dark:text-ink-200 hover:bg-surface-100 dark:hover:bg-gray-700/40'"
                                        class="flex items-center gap-3 px-5 py-2.5 text-sm transition-colors"
                                        x-on:mouseenter="activeIndex = idx"
                                    >
                                        <template x-if="item.thumbnail_url">
                                            <img
                                                :src="item.thumbnail_url"
                                                :alt="item.name"
                                                class="h-7 w-7 rounded-lg object-cover ring-1 ring-ink-100 dark:ring-ink-700"
                                            />
                                        </template>
                                        <template x-if="!item.thumbnail_url">
                                            <span
                                                :class="activeIndex === idx
                                                    ? 'bg-hero-purple text-white'
                                                    : 'bg-surface-100 dark:bg-gray-700 text-ink-500 dark:text-ink-300'"
                                                class="flex h-7 w-7 items-center justify-center rounded-lg transition-colors"
                                            >
                                                <x-icon name="home-modern" class="h-4 w-4" />
                                            </span>
                                        </template>
                                        <span class="flex-1 truncate" x-text="item.name"></span>
                                        <span class="text-xs text-ink-400 dark:text-ink-500 truncate max-w-[40%]" x-show="item.city" x-text="item.city"></span>
                                    </a>
                                </template>
                            </div>
                        </template>

                        <template x-if="!loading && results.length === 0">
                            <p class="px-5 py-6 text-center text-sm text-ink-400 dark:text-ink-500">
                                No properties match
                                <span class="font-medium text-ink-600 dark:text-ink-300" x-text="'“' + query + '”'"></span>.
                            </p>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Footer hints --}}
            <div class="flex items-center justify-between gap-3 px-5 py-2.5 border-t border-ink-100 dark:border-ink-800 bg-surface-50 dark:bg-gray-900/40 text-[11px] text-ink-500 dark:text-ink-400">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 rounded bg-white dark:bg-gray-800 ring-1 ring-ink-200 dark:ring-ink-700 font-semibold">↑</kbd>
                        <kbd class="px-1.5 py-0.5 rounded bg-white dark:bg-gray-800 ring-1 ring-ink-200 dark:ring-ink-700 font-semibold">↓</kbd>
                        navigate
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 rounded bg-white dark:bg-gray-800 ring-1 ring-ink-200 dark:ring-ink-700 font-semibold">↵</kbd>
                        open
                    </span>
                </div>
                <span class="hidden sm:inline">
                    <kbd class="px-1.5 py-0.5 rounded bg-white dark:bg-gray-800 ring-1 ring-ink-200 dark:ring-ink-700 font-semibold">⌘</kbd>
                    <kbd class="px-1.5 py-0.5 rounded bg-white dark:bg-gray-800 ring-1 ring-ink-200 dark:ring-ink-700 font-semibold">K</kbd>
                    to toggle
                </span>
            </div>
        </div>
    </div>
</div>
