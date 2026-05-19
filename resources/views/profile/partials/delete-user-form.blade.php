<div class="space-y-5">
    <p class="text-sm text-red-700/90 dark:text-red-300/90">
        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
    </p>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="w-full justify-center sm:w-auto"
    >
        <x-icon name="trash" class="h-4 w-4" />
        {{ __('Delete Account') }}
    </x-danger-button>

    <x-modal name="confirm-user-deletion" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-7">
            @csrf
            @method('delete')

            <div class="flex items-start gap-4">
                <span class="h-12 w-12 rounded-2xl bg-red-100 dark:bg-red-950/40 text-red-600 dark:text-red-300 flex items-center justify-center shrink-0">
                    <x-icon name="exclamation-triangle" class="h-6 w-6" />
                </span>
                <div>
                    <h2 class="text-lg font-bold text-ink-900 dark:text-ink-50">
                        {{ __('Are you sure you want to delete your account?') }}
                    </h2>
                    <p class="mt-2 text-sm text-ink-500 dark:text-ink-400">
                        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. This action cannot be undone.') }}
                    </p>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2.5 text-sm font-semibold text-ink-600 dark:text-ink-300 hover:bg-surface-100 dark:hover:bg-gray-700 rounded-xl transition">
                    {{ __('Cancel') }}
                </button>
                <x-danger-button>
                    <x-icon name="trash" class="h-4 w-4" />
                    {{ __('Delete account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</div>
