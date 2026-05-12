<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-primary-600 dark:bg-primary-500 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-primary-500 dark:hover:bg-primary-400 hover:-translate-y-0.5 hover:shadow-glow active:translate-y-0 shadow-glow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition ease-spring duration-150']) }}>
    {{ $slot }}
</button>
