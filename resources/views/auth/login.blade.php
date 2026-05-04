<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="space-y-6">
        <div class="text-center">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                {{ __('Sign in to DigiProper') }}
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Continue with your Google account.') }}
            </p>
        </div>

        @if ($errors->any())
            <div class="rounded-md bg-red-50 dark:bg-red-900/30 p-3 text-sm text-red-700 dark:text-red-200">
                {{ $errors->first() }}
            </div>
        @endif

        <a href="{{ route('auth.google.redirect') }}"
           class="flex w-full items-center justify-center gap-3 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-200 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
            <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
                <path fill="#EA4335" d="M12 10.2v3.96h5.52c-.24 1.32-1.68 3.84-5.52 3.84-3.36 0-6.12-2.76-6.12-6.12s2.76-6.12 6.12-6.12c1.92 0 3.24.84 3.96 1.56l2.7-2.64C16.92 2.94 14.64 2 12 2 6.48 2 2 6.48 2 12s4.48 10 10 10c5.76 0 9.6-4.04 9.6-9.72 0-.66-.06-1.14-.18-1.62H12z"/>
                <path fill="#34A853" d="M3.96 7.74l3.24 2.4C8.16 8.34 9.96 7.08 12 7.08c1.92 0 3.24.84 3.96 1.56l2.7-2.64C16.92 4.5 14.64 3.56 12 3.56c-3.84 0-7.14 2.22-8.04 4.18z" opacity="0"/>
            </svg>
            <span>{{ __('Continue with Google') }}</span>
        </a>
    </div>
</x-guest-layout>
