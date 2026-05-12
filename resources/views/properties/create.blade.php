<x-app-layout :title="'Add property'">
    <div class="max-w-4xl mx-auto space-y-6">
        <x-page-hero tone="purple" compact>
            <div class="flex items-center gap-4">
                <a href="{{ route('properties.index') }}" class="h-10 w-10 rounded-full bg-white/15 hover:bg-white/25 ring-1 ring-white/20 backdrop-blur-sm flex items-center justify-center text-white transition" aria-label="Back to properties">
                    <x-icon name="arrow-left" class="h-4 w-4" />
                </a>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-white/70">New entry</p>
                    <h1 class="mt-1 text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Add a new property</h1>
                    <p class="mt-1 text-sm text-white/80">Capture every detail — searchable forever.</p>
                </div>
            </div>
        </x-page-hero>

        @include('properties._form', ['property' => $property])
    </div>
</x-app-layout>
