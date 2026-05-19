@php
    $photos = $property->photos;
@endphp

<section class="rounded-3xl bg-white dark:bg-gray-800 shadow-soft ring-1 ring-gray-900/5 dark:ring-white/10 p-6 sm:p-8">
    <header class="flex items-start justify-between gap-3 mb-5">
        <div class="flex items-start gap-3 min-w-0">
            <span class="h-10 w-10 rounded-xl bg-accent-100 dark:bg-accent-950/40 text-accent-700 dark:text-accent-300 flex items-center justify-center shrink-0">
                <x-icon name="photo" class="h-5 w-5" />
            </span>
            <div class="min-w-0">
                <h2 class="text-base sm:text-lg font-semibold text-ink-900 dark:text-ink-50">Photos</h2>
                <p class="mt-0.5 text-sm text-ink-500 dark:text-ink-400">Front photo shows as the thumbnail across the app.</p>
            </div>
        </div>
        <button type="button" x-data @click="$dispatch('open-modal', 'upload-photo')"
                class="shrink-0 inline-flex items-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-500 text-white px-3.5 py-2 text-xs font-semibold shadow-glow-sm hover:shadow-glow transition ease-spring duration-150">
            <x-icon name="upload" class="h-4 w-4" />
            <span class="hidden sm:inline">Upload</span>
        </button>
    </header>

    @if ($photos->isEmpty())
        <button type="button" x-data @click="$dispatch('open-modal', 'upload-photo')"
                class="group w-full rounded-2xl border-2 border-dashed border-accent-200 dark:border-accent-900 bg-accent-50/40 dark:bg-accent-950/20 p-8 flex flex-col items-center justify-center text-center hover:bg-accent-50 dark:hover:bg-accent-950/40 hover:border-accent-300 dark:hover:border-accent-800 transition">
            <div class="h-12 w-12 rounded-2xl bg-accent-100 dark:bg-accent-900/50 text-accent-700 dark:text-accent-200 flex items-center justify-center group-hover:scale-105 transition">
                <x-icon name="photo" class="h-6 w-6" />
            </div>
            <p class="mt-3 text-sm font-semibold text-accent-800 dark:text-accent-200">Upload a front photo</p>
            <p class="text-xs text-accent-700/70 dark:text-accent-300/70">JPG, PNG, WebP or HEIC · up to 5 MB</p>
        </button>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3">
            @foreach ($photos as $photo)
                <div class="group relative rounded-2xl overflow-hidden ring-1 ring-gray-900/5 dark:ring-white/10 shadow-soft aspect-square bg-surface-100 dark:bg-gray-900/40">
                    <img src="{{ route('properties.photos.show', [$property, $photo]) }}"
                         alt="{{ $property->name }}"
                         class="absolute inset-0 h-full w-full object-cover" />

                    @if ($photo->is_primary)
                        <span class="absolute top-2 left-2 inline-flex items-center gap-1 rounded-full bg-emerald-500/95 text-white text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 ring-1 ring-white/30 shadow-soft">
                            <x-icon name="check-circle" class="h-3 w-3" /> Primary
                        </span>
                    @endif

                    <div class="absolute inset-x-0 bottom-0 flex items-center justify-end gap-1.5 p-1.5 bg-gradient-to-t from-ink-950/80 via-ink-950/40 to-transparent">
                        @unless ($photo->is_primary)
                            <form method="POST" action="{{ route('properties.photos.makePrimary', [$property, $photo]) }}">
                                @csrf
                                @method('PATCH')
                                <x-icon-button type="submit" tone="overlay" size="sm" aria-label="Set as primary photo">
                                    <x-icon name="check-circle" class="h-5 w-5" />
                                </x-icon-button>
                            </form>
                        @endunless
                        <form method="POST" action="{{ route('properties.photos.destroy', [$property, $photo]) }}"
                              x-data
                              x-on:submit.prevent="if (confirm('Delete this photo?')) $event.target.submit()">
                            @csrf
                            @method('DELETE')
                            <x-icon-button type="submit" tone="overlay" size="sm" aria-label="Delete photo" class="hover:bg-red-500/90">
                                <x-icon name="trash" class="h-5 w-5" />
                            </x-icon-button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <x-modal name="upload-photo" focusable>
        <form method="POST" action="{{ route('properties.photos.store', $property) }}" enctype="multipart/form-data" class="p-7 space-y-5">
            @csrf
            <div class="flex items-start gap-4">
                <span class="h-12 w-12 rounded-2xl bg-accent-100 dark:bg-accent-950/40 text-accent-700 dark:text-accent-300 flex items-center justify-center shrink-0">
                    <x-icon name="photo" class="h-6 w-6" />
                </span>
                <div>
                    <h3 class="text-lg font-bold text-ink-900 dark:text-ink-50">Upload a photo</h3>
                    <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">JPG, PNG, WebP or HEIC · up to 5 MB.</p>
                </div>
            </div>

            <div>
                <x-input-label for="photo_file" value="Photo *" />
                <input type="file" id="photo_file" name="file" accept="image/*" capture="environment" required
                       class="mt-1.5 block w-full text-sm text-ink-700 dark:text-ink-200 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-accent-100 dark:file:bg-accent-900/40 file:text-accent-700 dark:file:text-accent-200 file:font-semibold hover:file:bg-accent-200 dark:hover:file:bg-accent-900/60 transition" />
                <x-input-error :messages="$errors->get('file')" class="mt-2" />
            </div>

            @if ($photos->isNotEmpty())
                <label class="inline-flex items-center gap-2 text-sm text-ink-700 dark:text-ink-200">
                    <input type="checkbox" name="is_primary" value="1"
                           class="rounded border-ink-300 text-primary-600 focus:ring-primary-500" />
                    Make this the primary thumbnail
                </label>
            @endif

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2.5 text-sm font-semibold text-ink-600 dark:text-ink-300 hover:bg-surface-100 dark:hover:bg-gray-700 rounded-xl transition">Cancel</button>
                <x-primary-button type="submit">
                    <x-icon name="upload" class="h-4 w-4" />
                    Upload
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</section>
