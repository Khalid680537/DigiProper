@php
    $categoryLabels = [
        'title' => 'Title',
        'lease' => 'Lease',
        'rera' => 'RERA',
        'tax_receipt' => 'Tax receipt',
        'id_proof' => 'ID proof',
        'other' => 'Other',
    ];

    $formatBytes = function (int $bytes): string {
        if ($bytes < 1024) return $bytes.' B';
        if ($bytes < 1024 * 1024) return round($bytes / 1024, 1).' KB';
        return round($bytes / (1024 * 1024), 1).' MB';
    };

    $iconTone = function (string $name): array {
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if ($ext === 'pdf') {
            return ['icon' => 'document-text', 'classes' => 'bg-red-100 dark:bg-red-950/40 text-red-700 dark:text-red-300'];
        }
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
            return ['icon' => 'photo', 'classes' => 'bg-accent-100 dark:bg-accent-950/40 text-accent-700 dark:text-accent-300'];
        }
        return ['icon' => 'document', 'classes' => 'bg-ink-100 dark:bg-ink-800 text-ink-600 dark:text-ink-300'];
    };
@endphp

<section class="rounded-3xl bg-white dark:bg-gray-800 shadow-soft ring-1 ring-gray-900/5 dark:ring-white/10 p-6 sm:p-8">
    <header class="flex items-start justify-between gap-3 mb-5">
        <div class="flex items-start gap-3 min-w-0">
            <span class="h-10 w-10 rounded-xl bg-primary-50 dark:bg-primary-950/40 text-primary-600 dark:text-primary-300 flex items-center justify-center shrink-0">
                <x-icon name="folder-open" class="h-5 w-5" />
            </span>
            <div class="min-w-0">
                <h2 class="text-base sm:text-lg font-semibold text-ink-900 dark:text-ink-50">Documents</h2>
                <p class="mt-0.5 text-sm text-ink-500 dark:text-ink-400">Title deeds, leases, tax receipts and more.</p>
            </div>
        </div>
        <button type="button" x-data @click="$dispatch('open-modal', 'upload-document')"
                class="shrink-0 inline-flex items-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-500 text-white px-3.5 py-2 text-xs font-semibold shadow-glow-sm hover:shadow-glow transition ease-spring duration-150">
            <x-icon name="upload" class="h-4 w-4" />
            <span class="hidden sm:inline">Upload</span>
        </button>
    </header>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
        {{-- Upload tile --}}
        <button type="button" x-data @click="$dispatch('open-modal', 'upload-document')"
                class="group rounded-2xl border-2 border-dashed border-primary-200 dark:border-primary-900 bg-primary-50/40 dark:bg-primary-950/20 p-5 flex flex-col items-center justify-center text-center hover:bg-primary-50 dark:hover:bg-primary-950/40 hover:border-primary-300 dark:hover:border-primary-800 transition min-h-[10rem]">
            <div class="h-12 w-12 rounded-2xl bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-200 flex items-center justify-center group-hover:scale-105 transition">
                <x-icon name="arrow-up-tray" class="h-6 w-6" />
            </div>
            <p class="mt-3 text-sm font-semibold text-primary-800 dark:text-primary-200">Upload document</p>
            <p class="text-xs text-primary-700/70 dark:text-primary-300/70">PDF, JPG, PNG · up to 10 MB</p>
        </button>

        @foreach ($property->documents as $document)
            @php $tone = $iconTone($document->original_name); @endphp
            <div class="group rounded-2xl bg-white dark:bg-gray-800 ring-1 ring-gray-900/5 dark:ring-white/10 shadow-soft p-4 flex flex-col gap-3 hover:-translate-y-0.5 hover:shadow-glow transition ease-spring duration-150">
                <div class="flex items-start gap-3 min-w-0">
                    <div class="h-11 w-11 rounded-xl {{ $tone['classes'] }} flex items-center justify-center shrink-0">
                        <x-icon :name="$tone['icon']" class="h-5 w-5" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <a href="{{ route('properties.documents.show', [$property, $document]) }}"
                           class="block font-semibold text-ink-900 dark:text-ink-50 truncate hover:text-primary-700 dark:hover:text-primary-300 transition">
                            {{ $document->title }}
                        </a>
                        @if ($document->category)
                            <div class="mt-1">
                                <x-status-badge tone="primary" :dot="false" :label="$categoryLabels[$document->category] ?? $document->category" />
                            </div>
                        @endif
                    </div>
                </div>
                <p class="text-xs text-ink-500 dark:text-ink-400 truncate">
                    {{ $document->original_name }} · {{ $formatBytes($document->size_bytes) }} · {{ $document->created_at?->diffForHumans() }}
                </p>
                @if ($document->notes)
                    <p class="text-xs text-ink-600 dark:text-ink-300 line-clamp-2">{{ $document->notes }}</p>
                @endif

                <div class="mt-auto flex items-center justify-between gap-2 pt-2 border-t border-ink-100 dark:border-ink-800">
                    <a href="{{ route('properties.documents.show', [$property, $document]) }}"
                       class="inline-flex items-center gap-1.5 rounded-xl bg-primary-50 dark:bg-primary-950/40 text-primary-700 dark:text-primary-300 hover:bg-primary-100 dark:hover:bg-primary-950/60 px-3 py-2 text-sm font-semibold transition">
                        <x-icon name="arrow-down-tray" class="h-4 w-4" /> Open
                    </a>
                    <form method="POST" action="{{ route('properties.documents.destroy', [$property, $document]) }}"
                          x-data
                          x-on:submit.prevent="if (confirm('Delete this document?')) $event.target.submit()">
                        @csrf
                        @method('DELETE')
                        <x-icon-button type="submit" tone="danger" aria-label="Delete document">
                            <x-icon name="trash" class="h-5 w-5" />
                        </x-icon-button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <x-modal name="upload-document" focusable>
        <form method="POST" action="{{ route('properties.documents.store', $property) }}" enctype="multipart/form-data" class="p-7 space-y-5">
            @csrf
            <div class="flex items-start gap-4">
                <span class="h-12 w-12 rounded-2xl bg-primary-100 dark:bg-primary-950/40 text-primary-700 dark:text-primary-300 flex items-center justify-center shrink-0">
                    <x-icon name="arrow-up-tray" class="h-6 w-6" />
                </span>
                <div>
                    <h3 class="text-lg font-bold text-ink-900 dark:text-ink-50">Upload document</h3>
                    <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">PDF, JPG, or PNG · up to 10 MB.</p>
                </div>
            </div>

            <div>
                <x-input-label for="doc_title" value="Title *" />
                <x-text-input id="doc_title" name="title" type="text" class="mt-1.5" required />
            </div>
            <div>
                <x-input-label for="doc_category" value="Category" />
                <x-select
                    id="doc_category"
                    name="category"
                    class="mt-1.5"
                    placeholder="—"
                    :options="[
                        'title' => 'Title',
                        'lease' => 'Lease',
                        'rera' => 'RERA',
                        'tax_receipt' => 'Tax receipt',
                        'id_proof' => 'ID proof',
                        'other' => 'Other',
                    ]"
                />
            </div>
            <div>
                <x-input-label for="doc_file" value="File *" />
                <input type="file" id="doc_file" name="file" accept=".pdf,.jpg,.jpeg,.png" required
                       class="mt-1.5 block w-full text-sm text-ink-700 dark:text-ink-200 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-primary-100 dark:file:bg-primary-900/40 file:text-primary-700 dark:file:text-primary-200 file:font-semibold hover:file:bg-primary-200 dark:hover:file:bg-primary-900/60 transition" />
            </div>
            <div>
                <x-input-label for="doc_notes" value="Notes" />
                <x-textarea id="doc_notes" name="notes" rows="2" class="mt-1.5"></x-textarea>
            </div>

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
