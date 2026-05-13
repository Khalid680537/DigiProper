@php
    $shareUrl = $property->share_url;
    $visibilityFields = [
        'share_financials' => ['Show financials', 'Imputed value, yearly rent and yield.'],
        'share_contacts' => ['Show contacts', 'Phone numbers, roles and contact notes.'],
        'share_title_holder' => ['Show title holder', 'Owner / title-holder name.'],
        'share_keys_location' => ['Show keys location', 'Where the keys and originals are kept.'],
        'share_extra_notes' => ['Show extra notes', 'Free-text notes from the property record.'],
    ];
@endphp

<x-modal name="property-share" maxWidth="2xl">
    <div x-data="{ copied: false, async copyShareUrl() { try { await navigator.clipboard.writeText(@js($shareUrl)); this.copied = true; setTimeout(() => this.copied = false, 1800); } catch (e) {} } }"
         class="p-6 sm:p-8">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-3">
                <span class="h-11 w-11 rounded-2xl bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-200 flex items-center justify-center shrink-0">
                    <x-icon name="share" class="h-5 w-5" />
                </span>
                <div>
                    <h2 class="text-lg font-bold text-ink-900 dark:text-ink-50">Share this property</h2>
                    <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">Anyone with this QR or link can see the fields you've toggled on below — nothing else.</p>
                </div>
            </div>
            <button type="button" x-on:click="$dispatch('close')"
                    class="rounded-xl p-1.5 text-ink-400 hover:text-ink-700 hover:bg-surface-100 dark:hover:bg-gray-700 transition">
                <x-icon name="x-mark" class="h-5 w-5" />
            </button>
        </div>

        @if ($shareUrl)
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-[auto,1fr] gap-5 items-start">
                <div class="flex justify-center sm:justify-start">
                    <x-qr-thumb :property="$property" :size="180" :linked="false" />
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="text-[10px] font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">Share link</label>
                        <div class="mt-1 flex items-stretch gap-2">
                            <input type="text" readonly value="{{ $shareUrl }}"
                                   x-on:click="$el.select()"
                                   class="flex-1 min-w-0 rounded-xl border-ink-200 dark:border-ink-700 dark:bg-gray-900 dark:text-ink-100 text-sm tabular-nums shadow-soft focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15" />
                            <button type="button" x-on:click="copyShareUrl()"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-primary-600 hover:bg-primary-500 text-white px-3.5 text-sm font-semibold shadow-glow-sm transition">
                                <template x-if="!copied">
                                    <span class="inline-flex items-center gap-1.5"><x-icon name="clipboard" class="h-4 w-4" /> Copy</span>
                                </template>
                                <template x-if="copied">
                                    <span class="inline-flex items-center gap-1.5"><x-icon name="check" class="h-4 w-4" /> Copied</span>
                                </template>
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <a href="{{ $shareUrl }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-1.5 rounded-xl bg-surface-100 dark:bg-gray-900/60 ring-1 ring-ink-100 dark:ring-ink-700 px-3 py-2 text-xs font-semibold text-ink-700 dark:text-ink-200 hover:bg-white dark:hover:bg-gray-800 transition">
                            <x-icon name="eye" class="h-3.5 w-3.5" /> Open preview
                        </a>
                        <a href="{{ route('properties.share.qr.svg', $property->share_token) }}" download
                           class="inline-flex items-center gap-1.5 rounded-xl bg-surface-100 dark:bg-gray-900/60 ring-1 ring-ink-100 dark:ring-ink-700 px-3 py-2 text-xs font-semibold text-ink-700 dark:text-ink-200 hover:bg-white dark:hover:bg-gray-800 transition">
                            <x-icon name="arrow-down-tray" class="h-3.5 w-3.5" /> SVG
                        </a>
                        <a href="{{ route('properties.share.qr.png', $property->share_token) }}" download
                           class="inline-flex items-center gap-1.5 rounded-xl bg-surface-100 dark:bg-gray-900/60 ring-1 ring-ink-100 dark:ring-ink-700 px-3 py-2 text-xs font-semibold text-ink-700 dark:text-ink-200 hover:bg-white dark:hover:bg-gray-800 transition">
                            <x-icon name="arrow-down-tray" class="h-3.5 w-3.5" /> PNG
                        </a>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('properties.share.visibility', $property) }}"
                  x-data
                  x-on:change="$el.requestSubmit()"
                  class="mt-7 rounded-2xl bg-surface-100 dark:bg-gray-900/40 ring-1 ring-ink-100 dark:ring-ink-800 p-4 sm:p-5">
                @csrf
                @method('PATCH')
                <p class="text-xs font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">What scanners can see</p>
                <p class="mt-1 text-xs text-ink-500 dark:text-ink-400">Address, photos, area and tenure are always visible.</p>
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach ($visibilityFields as $field => [$label, $hint])
                        <label class="group flex items-start gap-3 rounded-xl bg-white dark:bg-gray-800 ring-1 ring-ink-100 dark:ring-ink-700 p-3 cursor-pointer hover:ring-primary-300 transition">
                            <input type="checkbox" name="{{ $field }}" value="1"
                                   @checked($property->{$field})
                                   class="mt-0.5 rounded-md border-ink-300 dark:border-ink-600 text-primary-600 focus:ring-primary-500/30">
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-semibold text-ink-900 dark:text-ink-50">{{ $label }}</span>
                                <span class="block text-[11px] text-ink-500 dark:text-ink-400">{{ $hint }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </form>

            <div class="mt-6 flex flex-col sm:flex-row gap-2 sm:items-center sm:justify-between">
                <button type="button"
                        x-on:click="$dispatch('open-modal', 'confirm-share-rotate')"
                        class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-white dark:bg-gray-800 ring-1 ring-ink-200 dark:ring-ink-700 px-3.5 py-2 text-xs font-semibold text-ink-700 dark:text-ink-200 hover:bg-surface-100 dark:hover:bg-gray-700 transition">
                    <x-icon name="arrow-path" class="h-3.5 w-3.5" /> Rotate link
                </button>
                <button type="button"
                        x-on:click="$dispatch('open-modal', 'confirm-share-disable')"
                        class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-red-50 dark:bg-red-950/30 ring-1 ring-red-200 dark:ring-red-900 px-3.5 py-2 text-xs font-semibold text-red-700 dark:text-red-300 hover:bg-red-100 dark:hover:bg-red-950/50 transition">
                    <x-icon name="x-mark" class="h-3.5 w-3.5" /> Disable sharing
                </button>
            </div>
        @else
            <div class="mt-6 rounded-2xl bg-surface-100 dark:bg-gray-900/40 ring-1 ring-ink-100 dark:ring-ink-800 p-6 text-center">
                <p class="text-sm text-ink-600 dark:text-ink-300">Sharing is currently disabled for this property.</p>
                <form method="POST" action="{{ route('properties.share.enable', $property) }}" class="mt-4">
                    @csrf
                    <x-primary-button>
                        <x-icon name="share" class="h-4 w-4 mr-1" /> Enable sharing
                    </x-primary-button>
                </form>
            </div>
        @endif
    </div>
</x-modal>

@if ($shareUrl)
    <x-modal name="confirm-share-rotate" maxWidth="md" focusable>
        <form method="POST" action="{{ route('properties.share.rotate', $property) }}" class="p-7">
            @csrf
            <div class="flex items-start gap-4">
                <span class="h-12 w-12 rounded-2xl bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-200 flex items-center justify-center shrink-0">
                    <x-icon name="arrow-path" class="h-6 w-6" />
                </span>
                <div class="flex-1">
                    <h2 class="text-lg font-bold text-ink-900 dark:text-ink-50">Rotate share link?</h2>
                    <p class="mt-2 text-sm text-ink-500 dark:text-ink-400">
                        A fresh link will be generated and the current QR / URL will stop working. Anyone holding the old QR will see a 404.
                    </p>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2.5 text-sm font-semibold text-ink-600 dark:text-ink-300 hover:bg-surface-100 dark:hover:bg-gray-700 rounded-xl transition">Cancel</button>
                <x-primary-button>Rotate link</x-primary-button>
            </div>
        </form>
    </x-modal>

    <x-modal name="confirm-share-disable" maxWidth="md" focusable>
        <form method="POST" action="{{ route('properties.share.disable', $property) }}" class="p-7">
            @csrf
            @method('DELETE')
            <div class="flex items-start gap-4">
                <span class="h-12 w-12 rounded-2xl bg-red-100 dark:bg-red-950/40 text-red-600 dark:text-red-300 flex items-center justify-center shrink-0">
                    <x-icon name="exclamation-triangle" class="h-6 w-6" />
                </span>
                <div class="flex-1">
                    <h2 class="text-lg font-bold text-ink-900 dark:text-ink-50">Disable sharing?</h2>
                    <p class="mt-2 text-sm text-ink-500 dark:text-ink-400">
                        The share page and QR will both stop working immediately. You can re-enable later, but a fresh link will be generated.
                    </p>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2.5 text-sm font-semibold text-ink-600 dark:text-ink-300 hover:bg-surface-100 dark:hover:bg-gray-700 rounded-xl transition">Cancel</button>
                <x-danger-button type="submit">Disable sharing</x-danger-button>
            </div>
        </form>
    </x-modal>
@endif
