<form method="post" action="{{ route('profile.update') }}" class="space-y-5">
    @csrf
    @method('patch')

    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1.5" :value="old('name', $user->name)" required autofocus autocomplete="name" />
        <x-input-error :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" name="email" type="email" class="mt-1.5" :value="old('email', $user->email)" required autocomplete="username" />
        <x-input-error :messages="$errors->get('email')" />
    </div>

    <div class="flex flex-col-reverse sm:flex-row sm:items-center gap-4">
        <x-primary-button class="w-full justify-center sm:w-auto">
            <x-icon name="check-circle" class="h-4 w-4" />
            {{ __('Save') }}
        </x-primary-button>

        @if (session('status') === 'profile-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="inline-flex items-center gap-1 text-sm font-medium text-emerald-700 dark:text-emerald-300"
            >
                <x-icon name="check-circle" class="h-4 w-4" />
                {{ __('Saved.') }}
            </p>
        @endif
    </div>
</form>
