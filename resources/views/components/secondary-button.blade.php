<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white dark:bg-gray-800 ring-1 ring-ink-200 dark:ring-ink-700 rounded-xl font-semibold text-sm text-ink-700 dark:text-ink-200 hover:bg-surface-100 dark:hover:bg-gray-700 hover:-translate-y-0.5 hover:shadow-soft shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 disabled:opacity-50 transition ease-spring duration-150']) }}>
    {{ $slot }}
</button>
