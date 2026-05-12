<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('properties.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">← Properties</a>
            <span class="text-gray-300 dark:text-gray-600">/</span>
            <a href="{{ route('properties.show', $property) }}" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">{{ $property->name }}</a>
            <span class="text-gray-300 dark:text-gray-600">/</span>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Edit</h1>
        </div>
    </x-slot>

    <div class="py-10 max-w-4xl mx-auto sm:px-6 lg:px-8">
        @include('properties._form', ['property' => $property])
    </div>
</x-app-layout>
