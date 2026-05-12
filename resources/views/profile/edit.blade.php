@php
    $user = auth()->user();
    $initial = strtoupper(mb_substr($user->name, 0, 1));
@endphp

<x-app-layout :title="'Profile'">
    <div class="max-w-3xl mx-auto space-y-6">
        <x-page-hero tone="purple" compact>
            <div class="flex items-center gap-4">
                <div class="h-16 w-16 rounded-2xl bg-white/15 ring-2 ring-white/25 flex items-center justify-center text-2xl font-extrabold text-white shrink-0">
                    {{ $initial }}
                </div>
                <div class="min-w-0">
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white truncate">{{ $user->name }}</h1>
                    <p class="text-sm text-white/85 truncate">{{ $user->email }}</p>
                </div>
            </div>
        </x-page-hero>

        <x-form-section title="Profile" icon="user" description="Update your name and email.">
            @include('profile.partials.update-profile-information-form')
        </x-form-section>

        {{-- Danger zone --}}
        <section class="rounded-3xl bg-red-50/60 dark:bg-red-950/30 ring-1 ring-red-200 dark:ring-red-900/50 p-6 sm:p-8">
            <div class="flex items-start gap-3">
                <span class="h-10 w-10 rounded-xl bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-300 flex items-center justify-center shrink-0">
                    <x-icon name="exclamation-triangle" class="h-5 w-5" />
                </span>
                <div>
                    <h2 class="text-base sm:text-lg font-semibold text-red-800 dark:text-red-200">Danger zone</h2>
                    <p class="mt-0.5 text-sm text-red-700/80 dark:text-red-300/80">Permanent actions. Proceed with care.</p>
                </div>
            </div>
            <div class="mt-5">
                @include('profile.partials.delete-user-form')
            </div>
        </section>
    </div>
</x-app-layout>
