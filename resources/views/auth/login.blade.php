<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="space-y-7">
        {{-- Decorative dots --}}
        <div class="flex items-center justify-center gap-1.5" aria-hidden="true">
            <span class="h-2.5 w-2.5 rounded-full bg-primary-500"></span>
            <span class="h-2.5 w-2.5 rounded-full bg-accent-400"></span>
            <span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
        </div>

        {{-- Hero --}}
        <div class="text-center">
            <h1 class="text-3xl sm:text-[2rem] font-extrabold tracking-tight text-ink-900 dark:text-ink-50 leading-tight">
                Your property portfolio,<br/>
                <span class="gradient-text">beautifully organised.</span>
            </h1>
            <p class="mt-3 text-sm text-ink-500 dark:text-ink-400">
                {{ __('Sign in to DigiProper to manage addresses, tenure, financials and documents in one place.') }}
            </p>
        </div>

        @if ($errors->any())
            <div class="rounded-2xl bg-red-50 dark:bg-red-900/30 ring-1 ring-red-200 dark:ring-red-800 px-4 py-3 text-sm text-red-700 dark:text-red-200 flex items-start gap-2">
                <x-icon name="exclamation-circle" class="h-4 w-4 mt-0.5 shrink-0" />
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        {{-- Google button --}}
        <a href="{{ route('auth.google.redirect') }}"
           class="group flex w-full items-center justify-center gap-3 rounded-2xl border border-ink-200 dark:border-ink-700 bg-white dark:bg-gray-900 px-5 py-3.5 text-sm font-semibold text-ink-800 dark:text-ink-100 shadow-soft transition ease-spring duration-150 hover:-translate-y-0.5 hover:shadow-glow hover:border-primary-300 dark:hover:border-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
            <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
                <path fill="#EA4335" d="M12 10.8v3.6h5.04c-.216 1.116-1.62 3.276-5.04 3.276-3.024 0-5.508-2.484-5.508-5.544S8.976 6.588 12 6.588c1.728 0 2.88.72 3.528 1.332l2.412-2.34C16.452 4.176 14.4 3.24 12 3.24c-4.86 0-8.784 3.924-8.784 8.784S7.14 20.808 12 20.808c5.076 0 8.436-3.564 8.436-8.568 0-.576-.072-1.008-.144-1.44H12z"/>
                <path fill="#FBBC05" d="M3.216 7.704l2.952 2.16C7.02 8.064 9.36 6.588 12 6.588c1.728 0 2.88.72 3.528 1.332l2.412-2.34C16.452 4.176 14.4 3.24 12 3.24c-3.456 0-6.444 1.98-7.92 4.86l-.864-.396z"/>
                <path fill="#34A853" d="M12 20.808c2.34 0 4.32-.756 5.76-2.052l-2.736-2.232c-.756.504-1.764.864-3.024.864-2.34 0-4.32-1.512-5.04-3.6l-2.952 2.268C5.484 18.9 8.484 20.808 12 20.808z"/>
                <path fill="#4285F4" d="M20.436 12.24c0-.576-.072-1.008-.144-1.44H12v3.6h5.04c-.108.864-.684 2.16-1.98 3.024l2.736 2.232c1.62-1.512 2.64-3.708 2.64-7.416z"/>
            </svg>
            <span>{{ __('Continue with Google') }}</span>
            <x-icon name="arrow-right" class="h-4 w-4 -ml-1 opacity-0 -translate-x-1 transition-all group-hover:opacity-100 group-hover:translate-x-0 text-primary-600" />
        </a>

        {{-- Trust signals --}}
        <div class="flex items-center justify-center gap-1.5 text-[11px] text-ink-500 dark:text-ink-400">
            <x-icon name="shield-check" class="h-3.5 w-3.5 text-primary-500" />
            <span>DPDP-compliant · Made for Indian property owners</span>
        </div>
    </div>
</x-guest-layout>
