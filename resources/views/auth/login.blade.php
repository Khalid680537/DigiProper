<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="space-y-6">
        <div class="text-center">
            <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">
                {{ __('Sign in to DigiProper') }}
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Continue with your Google account.') }}
            </p>
        </div>

        @if ($errors->any())
            <div class="rounded-md bg-red-50 dark:bg-red-900/30 p-3 text-sm text-red-700 dark:text-red-200">
                {{ $errors->first() }}
            </div>
        @endif

        <a href="{{ route('auth.google.redirect') }}"
           class="flex w-full items-center justify-center gap-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-200 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
            <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
                <path fill="#EA4335" d="M12 10.8v3.6h5.04c-.216 1.116-1.62 3.276-5.04 3.276-3.024 0-5.508-2.484-5.508-5.544S8.976 6.588 12 6.588c1.728 0 2.88.72 3.528 1.332l2.412-2.34C16.452 4.176 14.4 3.24 12 3.24c-4.86 0-8.784 3.924-8.784 8.784S7.14 20.808 12 20.808c5.076 0 8.436-3.564 8.436-8.568 0-.576-.072-1.008-.144-1.44H12z"/>
                <path fill="#FBBC05" d="M3.216 7.704l2.952 2.16C7.02 8.064 9.36 6.588 12 6.588c1.728 0 2.88.72 3.528 1.332l2.412-2.34C16.452 4.176 14.4 3.24 12 3.24c-3.456 0-6.444 1.98-7.92 4.86l-.864-.396z"/>
                <path fill="#34A853" d="M12 20.808c2.34 0 4.32-.756 5.76-2.052l-2.736-2.232c-.756.504-1.764.864-3.024.864-2.34 0-4.32-1.512-5.04-3.6l-2.952 2.268C5.484 18.9 8.484 20.808 12 20.808z"/>
                <path fill="#4285F4" d="M20.436 12.24c0-.576-.072-1.008-.144-1.44H12v3.6h5.04c-.108.864-.684 2.16-1.98 3.024l2.736 2.232c1.62-1.512 2.64-3.708 2.64-7.416z"/>
            </svg>
            <span>{{ __('Continue with Google') }}</span>
        </a>

        <p class="text-center text-xs text-gray-500 dark:text-gray-400">
            {{ __('Property management for Indian property owners') }}
        </p>
    </div>
</x-guest-layout>
