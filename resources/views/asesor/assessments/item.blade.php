<x-layouts::app.sidebar :title="'Detail Elemen'">

    @php
        $statusClasses = match ($item->status) {
            'sesuai' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            'tidak_sesuai' => 'bg-red-50 text-red-700 ring-red-600/20',
            'na' => 'bg-zinc-100 text-zinc-700 ring-zinc-500/20',
            default => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        };

        $statusLabel = match ($item->status) {
            'sesuai' => 'Sesuai',
            'tidak_sesuai' => 'Tidak Sesuai',
            'na' => 'N/A',
            default => 'Pending',
        };

        $documents = $item->documents->sortByDesc('created_at');
        $documentCount = $documents->count();

        $approvedCount = $documents->filter(
            fn ($document) => $document->latestReview?->status === 'approved'
        )->count();

        $revisionCount = $documents->filter(
            fn ($document) => in_array(
                $document->latestReview?->status,
                ['revision', 'rejected'],
                true
            )
        )->count();

        $pendingCount = $documentCount - $approvedCount - $revisionCount;

        $elementCode = $item->element->code ?? '-';
        $elementName = $item->element->name ?? '';
        $instrumentName = $item->element->chapter->name ?? '-';
    @endphp

    <flux:main class="!pt-0">
        <div class="space-y-4 pt-5">

            {{-- HEADER HALAMAN --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-wrap items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                    <a
                        href="{{ route('assessments.index') }}"
                        wire:navigate
                        class="transition hover:text-zinc-900 dark:hover:text-white"
                    >
                        Assessments
                    </a>

                    <span>/</span>

                    <a
                        href="{{ route('assessments.show', $assessment) }}"
                        wire:navigate
                        class="transition hover:text-zinc-900 dark:hover:text-white"
                    >
                        {{ $assessment->clinic?->name ?? 'Assessment' }}
                    </a>

                    <span>/</span>

                    <span class="font-medium text-zinc-700 dark:text-zinc-300">
                        {{ $elementCode }}
                    </span>
                </div>

                <flux:button
                    href="{{ route('assessments.show', $assessment) }}"
                    wire:navigate
                    variant="ghost"
                    size="sm"
                >
                    <span class="inline-flex items-center gap-2">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="size-4"
                            aria-hidden="true"
                        >
                            <path d="m15 18-6-6 6-6" />
                            <path d="M9 12h12" />
                        </svg>
                        Kembali
                    </span>
                </flux:button>
            </div>

            {{-- NOTIFIKASI --}}
            @if (session('success'))
                <script>
                    (() => {
                        const showSuccessToast = () => {
                            if (!window.Swal) return;

                            window.Swal.fire({
                                toast: true,
                                position: 'top',
                                icon: 'success',
                                title: @json(session('success')),
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                background: '#ECFDF5',
                                color: '#065F46',
                                customClass: {
                                    popup: 'assessment-success-toast',
                                },
                                didOpen: (toast) => {
                                    toast.style.border = '1px solid #A7F3D0';
                                    toast.style.borderRadius = '12px';
                                    toast.style.boxShadow =
                                        '0 8px 24px rgba(6, 95, 70, 0.12)';

                                    toast.addEventListener(
                                        'mouseenter',
                                        window.Swal.stopTimer
                                    );

                                    toast.addEventListener(
                                        'mouseleave',
                                        window.Swal.resumeTimer
                                    );
                                },
                            });
                        };

                        if (document.readyState === 'loading') {
                            document.addEventListener(
                                'DOMContentLoaded',
                                showSuccessToast,
                                { once: true }
                            );
                        } else {
                            showSuccessToast();
                        }
                    })();
                </script>
            @endif

            @if (session('error'))
                <flux:callout variant="danger">
                    {{ session('error') }}
                </flux:callout>
            @endif

            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800 dark:border-red-900 dark:bg-red-950/30 dark:text-red-300">
                    <div class="mb-2 font-semibold">
                        Terdapat kesalahan:
                    </div>

                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- INFORMASI ELEMEN --}}
            <flux:card>
                <div class="space-y-5">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                            Elemen Penilaian
                        </p>

                        <h1 class="mt-1 text-xl font-bold tracking-tight text-zinc-900 dark:text-white">
                            {{ $elementCode }}
                        </h1>

                        @if ($elementName)
                            <p class="mt-2 max-w-3xl text-sm leading-6 text-zinc-600 dark:text-zinc-400">
                                {{ $elementName }}
                            </p>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <span class="inline-flex items-center rounded-lg bg-zinc-100 px-3 py-1.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                            {{ $instrumentName }}
                        </span>

                        <span class="inline-flex items-center rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-inset {{ $statusClasses }}">
                            Status EP: {{ $statusLabel }}
                        </span>
                    </div>

                    {{-- RINGKASAN DOKUMEN --}}
                    <div class="flex flex-wrap items-center gap-x-5 gap-y-3 border-t border-zinc-200 pt-4 text-sm text-zinc-500 dark:border-zinc-800 dark:text-zinc-400">
                        <span class="inline-flex items-center gap-2">
                            <flux:icon name="document" class="size-4" />
                            {{ $documentCount }} dokumen
                        </span>

                        <span class="inline-flex items-center gap-2">
                            <flux:icon name="check-circle" class="size-4 text-emerald-600" />
                            {{ $approvedCount }} disetujui
                        </span>

                        <span class="inline-flex items-center gap-2">
                            <flux:icon name="clock" class="size-4 text-amber-600" />
                            {{ $pendingCount }} menunggu review
                        </span>

                        <span class="inline-flex items-center gap-2">
                            <flux:icon name="exclamation-triangle" class="size-4 text-red-600" />
                            {{ $revisionCount }} butuh revisi
                        </span>
                    </div>
                </div>
            </flux:card>

            {{-- DAFTAR DOKUMEN --}}
            <flux:card class="overflow-hidden p-0">
                <div class="border-b border-zinc-200 px-6 py-5 dark:border-zinc-800">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <flux:heading size="lg">
                                Daftar Dokumen Bukti
                            </flux:heading>

                            <flux:subheading class="mt-1">
                                Dokumen bukti yang diunggah klinik untuk elemen ini.
                            </flux:subheading>
                        </div>

                        <flux:badge variant="neutral">
                            {{ $documentCount }} dokumen
                        </flux:badge>
                    </div>
                </div>

                @if ($documentCount > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[1000px] text-left text-sm">
                            <thead class="bg-zinc-50 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:bg-zinc-800/50 dark:text-zinc-400">
                                <tr>
                                    <th class="px-6 py-4">Kode EP</th>
                                    <th class="px-4 py-4">Instrumen</th>
                                    <th class="px-4 py-4">Judul Dokumen</th>
                                    <th class="px-4 py-4">Pengunggah</th>
                                    <th class="px-4 py-4">Status Review</th>
                                    <th class="px-6 py-4">Aksi Dokumen</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                @foreach ($documents as $document)
                                    @php
                                        $latestReview = $document->latestReview;
                                        $reviewStatus = $latestReview?->status ?? 'pending';

                                        /*
                                         * Samakan dengan halaman Klinik:
                                         * gunakan aktivitas terbaru yang memiliki catatan.
                                         * Jika tidak ada, gunakan catatan review terbaru.
                                         */
                                        $latestActivityWithNotes = $document->activities
                                            ->filter(fn ($activity) => filled($activity->notes))
                                            ->sortByDesc('created_at')
                                            ->first();

                                        $displayNote = $latestActivityWithNotes?->notes
                                            ?? $latestReview?->notes;

                                        $reviewStatusClasses = match ($reviewStatus) {
                                            'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400',
                                            'revision', 'rejected' => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400',
                                            default => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400',
                                        };

                                        $reviewStatusLabel = match ($reviewStatus) {
                                            'approved' => 'Disetujui',
                                            'revision', 'rejected' => 'Butuh Revisi',
                                            default => 'Menunggu Review',
                                        };

                                        /*
                                         * Samakan warna catatan dengan halaman Klinik:
                                         * warna mengikuti status review dokumen.
                                         */
                                        $noteClasses = match ($reviewStatus) {
                                            'approved' => 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/20 dark:text-emerald-300',
                                            'revision', 'rejected' => 'border-red-200 bg-red-50 text-red-700 dark:border-red-900/50 dark:bg-red-950/20 dark:text-red-300',
                                            default => 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-900/50 dark:bg-amber-950/20 dark:text-amber-300',
                                        };

                                        $mimeType = strtolower($document->mime_type ?? '');

                                        $documentInstrument = match ($document->instrument_type) {
                                            'regulasi' => 'Regulasi',
                                            'dokumen' => 'Dokumen',
                                            default => 'Dokumen',
                                        };
                                    @endphp

                                    <tr class="align-top transition hover:bg-zinc-50/70 dark:hover:bg-zinc-800/30">
                                        <td class="whitespace-nowrap px-4 py-5 sm:px-6">
                                            <span class="font-semibold text-blue-600 dark:text-blue-400">
                                                {{ $elementCode }}
                                            </span>
                                        </td>

                                        <td class="whitespace-nowrap px-4 py-5">
                                            <span class="inline-flex items-center rounded-sm bg-slate-100 px-2 py-1 text-[11px] font-semibold uppercase text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                {{ $documentInstrument }}
                                            </span>
                                        </td>

                                        <td class="min-w-[260px] px-4 py-5">
                                            <p class="break-words font-medium text-zinc-900 dark:text-white">
                                                {{ $document->original_name }}
                                            </p>

                                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                                Diunggah {{ $document->created_at?->format('d M Y, H:i') ?? '-' }}

                                                @if ($document->file_size)
                                                    <span class="mx-1">·</span>
                                                    {{ number_format($document->file_size / 1024, 0) }} KB
                                                @endif
                                            </p>

                                            @if (filled($displayNote))
                                                <div class="mt-2 max-w-md rounded border px-2 py-1.5 text-xs leading-relaxed {{ $noteClasses }}">
                                                    <span class="font-medium">Catatan:</span>
                                                    <span class="whitespace-pre-line">{{ trim($displayNote) }}</span>
                                                </div>
                                            @endif
                                        </td>

                                        <td class="whitespace-nowrap px-4 py-5 text-zinc-700 dark:text-zinc-300">
                                            {{ $document->uploader?->name ?? 'Pengguna klinik' }}
                                        </td>

                                        <td class="whitespace-nowrap px-4 py-5">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $reviewStatusClasses }}">
                                                {{ $reviewStatusLabel }}
                                            </span>
                                        </td>

                                        <td class="whitespace-nowrap px-4 py-5 sm:px-6">
                                            <div class="flex items-center gap-2">
                                                {{-- PREVIEW --}}
                                                <button
                                                    type="button"
                                                    data-preview-url="{{ route('assessments.documents.preview', $document) }}"
                                                    data-preview-download-url="{{ route('assessments.documents.download', $document) }}"
                                                    data-preview-name="{{ $document->original_name }}"
                                                    data-preview-ep="{{ $elementCode }}"
                                                    data-preview-instrument="{{ $documentInstrument }}"
                                                    data-preview-uploader="{{ $document->uploader?->name ?? 'Pengguna klinik' }}"
                                                    data-preview-mime="{{ $mimeType }}"
                                                    class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-300 px-3 py-2 text-xs font-medium text-zinc-700 transition hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-zinc-600 dark:text-zinc-200 dark:hover:bg-zinc-800"
                                                >
                                                    <flux:icon name="eye" class="size-4" />
                                                    Lihat
                                                </button>

                                                {{-- DOWNLOAD --}}
                                                <a
                                                    href="{{ route('assessments.documents.download', $document) }}"
                                                    class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 px-3 py-2 text-xs font-medium text-blue-700 transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-blue-900 dark:text-blue-300 dark:hover:bg-blue-950/40"
                                                >
                                                    <flux:icon name="arrow-down-tray" class="size-4" />
                                                    Unduh
                                                </a>

                                                {{-- REVIEW --}}
                                                <a
                                                    href="{{ route('assessments.documents.evaluate', ['document' => $document]) }}"
                                                    wire:navigate
                                                    class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 px-3 py-2 text-xs font-medium text-emerald-700 transition hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:border-emerald-900 dark:text-emerald-300 dark:hover:bg-emerald-950/40"
                                                >
                                                    <flux:icon name="clipboard-document-check" class="size-4" />
                                                    Review
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-6 py-14 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-zinc-100 text-zinc-400 dark:bg-zinc-800">
                            <flux:icon name="document" class="size-7" />
                        </div>

                        <flux:heading size="sm" class="mt-4">
                            Belum ada dokumen bukti
                        </flux:heading>

                        <flux:text class="mt-1">
                            Klinik belum mengunggah dokumen untuk elemen ini.
                        </flux:text>
                    </div>
                @endif
            </flux:card>
        </div>
    </flux:main>

    {{-- MODAL PREVIEW DOKUMEN --}}
    <div
        id="document-preview-modal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/70 p-3 sm:p-6"
        role="dialog"
        aria-modal="true"
        aria-labelledby="document-preview-title"
    >
        <div class="flex h-full max-h-[94vh] w-full max-w-[88vw] flex-col overflow-hidden rounded-xl bg-white shadow-2xl dark:bg-zinc-900">

            {{-- HEADER MODAL --}}
            <div class="flex flex-col gap-3 border-b border-zinc-200 px-4 py-3 dark:border-zinc-700 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div class="min-w-0 flex-1">
                    <h2
                        id="document-preview-title"
                        class="truncate text-base font-semibold text-zinc-900 dark:text-white"
                    >
                        Preview Dokumen
                    </h2>

                    <div
                        id="document-preview-information"
                        class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-xs text-zinc-500 dark:text-zinc-400"
                    ></div>
                </div>

                <div class="flex shrink-0 items-center gap-2">
                    <a
                        id="document-preview-download"
                        href="#"
                        class="inline-flex items-center gap-2 rounded-lg bg-zinc-900 px-3 py-2 text-sm font-medium text-white transition hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"
                    >
                        <flux:icon name="arrow-down-tray" class="size-4" />
                        Unduh
                    </a>

                    <button
                        id="document-preview-close"
                        type="button"
                        class="inline-flex items-center justify-center rounded-lg border border-zinc-300 p-2 text-zinc-600 transition hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800"
                        aria-label="Tutup preview"
                    >
                        <flux:icon name="x-mark" class="size-5" />
                    </button>
                </div>
            </div>

            {{-- AREA PREVIEW --}}
            <div class="relative min-h-0 flex-1 bg-zinc-100 dark:bg-zinc-950">
                <iframe
                    id="document-preview-frame"
                    src="about:blank"
                    title="Preview dokumen"
                    class="h-full w-full border-0"
                ></iframe>

                <div
                    id="document-preview-fallback"
                    class="absolute inset-0 hidden items-center justify-center p-6 text-center"
                >
                    <div class="max-w-md">
                        <flux:icon name="exclamation-triangle" class="mx-auto size-12 text-zinc-400" />

                        <p class="mt-4 font-semibold text-zinc-900 dark:text-white">
                            Preview tidak tersedia
                        </p>

                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                            Preview langsung hanya tersedia untuk file PDF.
                            Silakan gunakan tombol Unduh untuk membuka dokumen ini.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT PREVIEW DOKUMEN --}}
    <script>
        (() => {
            if (window.initializeAssessorDocumentPreview) {
                window.initializeAssessorDocumentPreview();
                return;
            }

            window.initializeAssessorDocumentPreview = function () {
                const modal = document.getElementById('document-preview-modal');

                if (!modal) return;

                const title = document.getElementById('document-preview-title');
                const information = document.getElementById('document-preview-information');
                const frame = document.getElementById('document-preview-frame');
                const fallback = document.getElementById('document-preview-fallback');
                const closeButton = document.getElementById('document-preview-close');
                const downloadLink = document.getElementById('document-preview-download');

                if (
                    !title ||
                    !information ||
                    !frame ||
                    !fallback ||
                    !closeButton ||
                    !downloadLink
                ) {
                    return;
                }

                const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (character) => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;',
                })[character]);

                const closePreview = () => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    frame.src = 'about:blank';
                    document.body.classList.remove('overflow-hidden');

                    if (modal._previousFocus?.isConnected) {
                        modal._previousFocus.focus();
                    }
                };

                if (modal.dataset.initialized !== 'true') {
                    modal.dataset.initialized = 'true';

                    closeButton.addEventListener('click', closePreview);

                    modal.addEventListener('click', (event) => {
                        if (event.target === modal) {
                            closePreview();
                        }
                    });

                    document.addEventListener('keydown', (event) => {
                        if (
                            event.key === 'Escape' &&
                            !modal.classList.contains('hidden')
                        ) {
                            closePreview();
                        }
                    });

                    modal._closePreview = closePreview;
                }

                document.querySelectorAll('[data-preview-url]').forEach((button) => {
                    if (button.dataset.previewBound === 'true') return;

                    button.dataset.previewBound = 'true';

                    button.addEventListener('click', () => {
                        const previewUrl = button.dataset.previewUrl;
                        const downloadUrl = button.dataset.previewDownloadUrl;

                        if (!previewUrl || !downloadUrl) return;

                        modal._previousFocus = button;

                        title.textContent =
                            button.dataset.previewName || 'Preview Dokumen';

                        information.innerHTML = `
                            <span>
                                <strong class="font-semibold text-zinc-700 dark:text-zinc-200">EP:</strong>
                                ${escapeHtml(button.dataset.previewEp || '-')}
                            </span>
                            <span>
                                <strong class="font-semibold text-zinc-700 dark:text-zinc-200">Instrumen:</strong>
                                ${escapeHtml(button.dataset.previewInstrument || '-')}
                            </span>
                            <span>
                                <strong class="font-semibold text-zinc-700 dark:text-zinc-200">Pengunggah:</strong>
                                ${escapeHtml(button.dataset.previewUploader || '-')}
                            </span>
                        `;

                        downloadLink.href = downloadUrl;

                        const mimeType = (
                            button.dataset.previewMime || ''
                        ).toLowerCase();

                        if (mimeType === 'application/pdf') {
                            frame.src = previewUrl;
                            frame.classList.remove('hidden');

                            fallback.classList.add('hidden');
                            fallback.classList.remove('flex');
                        } else {
                            frame.src = 'about:blank';
                            frame.classList.add('hidden');

                            fallback.classList.remove('hidden');
                            fallback.classList.add('flex');
                        }

                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        document.body.classList.add('overflow-hidden');

                        closeButton.focus();
                    });
                });
            };

            window.initializeAssessorDocumentPreview();

            document.addEventListener(
                'DOMContentLoaded',
                window.initializeAssessorDocumentPreview
            );

            document.addEventListener(
                'livewire:navigated',
                window.initializeAssessorDocumentPreview
            );
        })();
    </script>

</x-layouts::app.sidebar>
