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
@endphp

<section class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 rounded-2xl p-6 sm:p-8">
    <header class="flex items-start justify-between gap-3 mb-5">
        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Documents</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Scanned title deeds, leases, tax receipts, and more.</p>
        </div>
        <button
            type="button"
            x-data
            @click="$dispatch('open-modal', 'upload-document')"
            class="inline-flex items-center px-3 py-1.5 bg-primary-600 dark:bg-primary-500 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 dark:hover:bg-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition"
        >
            Upload
        </button>
    </header>

    @if ($property->documents->isEmpty())
        <p class="text-sm text-gray-500 dark:text-gray-400 italic">No documents uploaded yet.</p>
    @else
        <ul class="divide-y divide-gray-100 dark:divide-gray-700/60">
            @foreach ($property->documents as $document)
                <li class="py-3 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <a href="{{ route('properties.documents.show', [$property, $document]) }}"
                               class="font-medium text-gray-900 dark:text-gray-100 hover:text-primary-700 dark:hover:text-primary-300 truncate">
                                {{ $document->title }}
                            </a>
                            @if ($document->category)
                                <x-status-badge tone="primary" :label="$categoryLabels[$document->category] ?? $document->category" />
                            @endif
                        </div>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 truncate">
                            {{ $document->original_name }} · {{ $formatBytes($document->size_bytes) }}
                        </p>
                        @if ($document->notes)
                            <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">{{ $document->notes }}</p>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('properties.documents.destroy', [$property, $document]) }}"
                          onsubmit="return confirm('Delete this document?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-gray-400 hover:text-red-600 dark:hover:text-red-400" aria-label="Delete document">Remove</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif

    <x-modal name="upload-document" focusable>
        <form method="POST" action="{{ route('properties.documents.store', $property) }}" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Upload document</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">PDF, JPG, or PNG · up to 10 MB.</p>

            <div>
                <x-input-label for="doc_title" value="Title *" />
                <x-text-input id="doc_title" name="title" type="text" class="mt-1 block w-full" required />
            </div>
            <div>
                <x-input-label for="doc_category" value="Category" />
                <x-select
                    id="doc_category"
                    name="category"
                    class="mt-1 block w-full"
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
                       class="mt-1 block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-primary-50 dark:file:bg-primary-900/40 file:text-primary-700 dark:file:text-primary-200 file:font-semibold hover:file:bg-primary-100 dark:hover:file:bg-primary-900/60" />
            </div>
            <div>
                <x-input-label for="doc_notes" value="Notes" />
                <x-textarea id="doc_notes" name="notes" rows="2" class="mt-1"></x-textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" x-on:click="$dispatch('close')" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:underline">Cancel</button>
                <x-primary-button type="submit">Upload</x-primary-button>
            </div>
        </form>
    </x-modal>
</section>
