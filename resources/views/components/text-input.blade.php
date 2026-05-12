@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full border-ink-200 dark:border-ink-700 dark:bg-gray-900 dark:text-ink-100 placeholder:text-ink-400 dark:placeholder:text-ink-500 focus:border-primary-500 dark:focus:border-primary-400 focus:ring-4 focus:ring-primary-500/15 rounded-xl px-4 py-2.5 text-sm shadow-sm transition']) }}>
