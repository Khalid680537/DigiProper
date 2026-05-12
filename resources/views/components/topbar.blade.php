@props(['title' => null, 'subtitle' => null])

<header {{ $attributes->merge(['class' => 'sticky top-0 z-30 items-center gap-4 h-16 px-6 lg:px-8 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-ink-100 dark:border-ink-800']) }}>
    <div class="min-w-0 flex-1">
        @if ($title)
            <p class="text-sm font-semibold text-ink-800 dark:text-ink-100 truncate">{{ $title }}</p>
        @endif
        @if ($subtitle)
            <p class="text-xs text-ink-500 dark:text-ink-400 truncate">{{ $subtitle }}</p>
        @endif
    </div>

    <div class="flex items-center gap-2">
        {{-- Search affordance (visual only for v1) --}}
        <button type="button" class="hidden lg:inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-surface-100 dark:bg-gray-800 ring-1 ring-ink-100 dark:ring-ink-700 text-xs text-ink-500 dark:text-ink-400 hover:bg-surface-200 dark:hover:bg-gray-700 transition">
            <x-icon name="search" class="h-3.5 w-3.5" />
            <span>Search</span>
            <kbd class="ml-2 px-1.5 py-0.5 rounded bg-white dark:bg-gray-900 ring-1 ring-ink-200 dark:ring-ink-700 text-[10px] font-semibold text-ink-600 dark:text-ink-300">⌘K</kbd>
        </button>

        <x-icon-button tone="ghost" title="Notifications">
            <x-icon name="bell" class="h-5 w-5" />
        </x-icon-button>
    </div>
</header>
