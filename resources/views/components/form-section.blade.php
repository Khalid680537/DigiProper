@props(['title', 'description' => null])

<section class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 rounded-2xl p-6 sm:p-8">
    <header class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $title }}</h2>
        @if ($description)
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
        @endif
    </header>

    <div class="space-y-5">
        {{ $slot }}
    </div>
</section>
