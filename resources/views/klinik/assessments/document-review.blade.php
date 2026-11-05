<x-layouts::app.sidebar :title="'Review Dokumen'">
    @php
        $sortedActivities = $activities
            ->sortByDesc('created_at')
            ->sortByDesc('id')
            ->values();

        $latestActivity = $sortedActivities->first();

        $latestActivityIsReview = $latestActivity?->type === 'reviewed';

        $latestActivityIsApproved = $latestActivityIsReview
            && $latestActivity?->status === 'approved';

        $latestActivityIsRevision = $latestActivityIsReview
            && in_array($latestActivity?->status, ['revision', 'rejected'], true);

        $latestActivityLabel = match ($latestActivity?->type) {
            'uploaded' => 'Dokumen diunggah',
            'reuploaded' => 'Dokumen diunggah ulang',
            'revision_uploaded' => 'Revisi dokumen diunggah',
            'reviewed' => match ($latestActivity?->status) {
                'approved' => 'Dokumen disetujui asesor',
                'revision', 'rejected' => 'Asesor meminta revisi',
                default => 'Asesor menyimpan review',
            },
            default => 'Aktivitas dokumen',
        };

        $latestActivityStatusLabel = match ($latestActivity?->status) {
            'approved' => 'Disetujui',
            'revision', 'rejected' => 'Butuh Revisi',
            'pending' => 'Menunggu Review',
            default => null,
        };

        $latestActivityStatusClass = match (true) {
            $latestActivityIsApproved => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400',
            $latestActivityIsRevision => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400',
            default => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400',
        };

        $latestActivityIconClass = 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400';

        $latestActivityNoteLabel = match ($latestActivity?->type) {
            'uploaded' => 'CATATAN UPLOAD',
            'reuploaded' => 'CATATAN UPLOAD ULANG',
            'revision_uploaded' => 'CATATAN REVISI',
            'reviewed' => $latestActivityIsRevision
                ? 'CATATAN REVISI ASESOR'
                : 'CATATAN ASESOR',
            default => 'CATATAN AKTIVITAS',
        };

        $reviewStatus = $latestActivityIsReview
            ? ($latestActivity?->status ?? 'pending')
            : ($reviews->sortByDesc('created_at')->sortByDesc('id')->first()?->status ?? 'pending');

        $reviewLabel = match ($reviewStatus) {
            'approved' => 'Disetujui',
            'revision', 'rejected' => 'Butuh Revisi',
            default => 'Menunggu Review',
        };

        $reviewClasses = match ($reviewStatus) {
            'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400',
            'revision', 'rejected' => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400',
            default => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400',
        };

        $documentInstrument = match ($document->instrument_type) {
            'regulasi' => 'Regulasi',
            'dokumen' => 'Dokumen',
            default => 'Dokumen',
        };

        $elementCode = $item->element->code ?? '-';
        $isClinic = auth()->user()?->role === 'klinik';

        // Dokumen yang sudah disetujui dikunci untuk klinik.
        $isApproved = $reviewStatus === 'approved';

        $canUploadRevision = $isClinic
            && in_array($reviewStatus, ['revision', 'rejected'], true);

        $canReupload = $isClinic && ! $isApproved;
    @endphp

    <flux:main class="!pt-0">
        <div class="space-y-4 pt-5">

            {{-- PAGE HEADER --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                        <a href="{{ route('dashboard') }}" wire:navigate class="transition hover:text-zinc-900 dark:hover:text-white">
                            Dashboard
                        </a>

                        <span>/</span>

                        <a href="{{ route('assessments.show', $assessment) }}" wire:navigate class="transition hover:text-zinc-900 dark:hover:text-white">
                            Assessment Klinik
                        </a>

                        <span>/</span>

                        <a
                            href="{{ route('assessments.items.show', ['assessment' => $assessment, 'item' => $item]) }}"
                            wire:navigate
                            class="transition hover:text-zinc-900 dark:hover:text-white"
                        >
                            {{ $elementCode }}
                        </a>

                        <span>/</span>

                        <span class="text-zinc-700 dark:text-zinc-300">
                            Aktivitas Dokumen
                        </span>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <flux:button href="{{ route('dashboard') }}" wire:navigate variant="ghost" icon="squares-2x2">
                        Kembali ke Dashboard
                    </flux:button>

                    <flux:button
                        href="{{ route('assessments.items.show', ['assessment' => $assessment, 'item' => $item]) }}"
                        wire:navigate
                        variant="ghost"
                        icon="arrow-left"
                    >
                        Kembali ke Detail EP
                    </flux:button>
                </div>
            </div>

            {{-- INFORMASI DOKUMEN DAN AKTIVITAS TERBARU --}}
            <flux:card>
                <div class="space-y-5">

                    {{-- HEADER DOKUMEN --}}
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex min-w-0 flex-1 items-start gap-3">
                            <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                                <flux:icon name="document-text" class="size-5" />
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                    Dokumen Bukti
                                </p>

                                <h1 class="mt-1 break-words text-lg font-bold tracking-tight text-zinc-900 dark:text-white sm:text-xl">
                                    {{ $document->original_name ?? 'Dokumen' }}
                                </h1>

                                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ strtoupper(pathinfo($document->original_name ?? '', PATHINFO_EXTENSION) ?: 'FILE') }}

                                    @if ($document->file_size)
                                        <span class="mx-1">·</span>
                                        {{ number_format($document->file_size / 1024, 0, ',', '.') }} KB
                                    @endif
                                </p>
                            </div>
                        </div>

                        <span class="inline-flex w-fit shrink-0 items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-inset {{ $reviewClasses }}">
                            @if ($reviewStatus === 'approved')
                                <flux:icon name="check-circle" class="size-4" />
                            @elseif (in_array($reviewStatus, ['revision', 'rejected'], true))
                                <flux:icon name="exclamation-circle" class="size-4" />
                            @else
                                <flux:icon name="clock" class="size-4" />
                            @endif

                            {{ $reviewLabel }}
                        </span>
                    </div>

                    {{-- METADATA DOKUMEN --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center rounded-lg bg-zinc-100 px-3 py-1.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                            EP {{ $elementCode }}
                        </span>

                        <span class="inline-flex items-center rounded-lg bg-zinc-100 px-3 py-1.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                            {{ $documentInstrument }}
                        </span>

                        @if ($isApproved)
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                <flux:icon name="lock-closed" class="size-3.5" />
                                Dokumen terkunci
                            </span>
                        @endif
                    </div>

                    {{-- INFORMASI PENGUNGGAH --}}
                    <div class="grid gap-4 border-y border-zinc-200 py-4 sm:grid-cols-2 dark:border-zinc-800">
                        <div>
                            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">
                                Pengunggah
                            </p>

                            <p class="mt-1 break-words text-sm font-semibold text-zinc-900 dark:text-white">
                                {{ $document->uploader?->name ?? 'Pengguna klinik' }}
                            </p>

                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                Diunggah {{ $document->created_at?->format('d M Y, H:i') ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">
                                Riwayat aktivitas
                            </p>

                            <p class="mt-1 text-sm font-semibold text-zinc-900 dark:text-white">
                                {{ $activities->count() }} aktivitas
                            </p>

                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                Termasuk upload, revisi, dan review asesor
                            </p>
                        </div>
                    </div>

                    {{-- AKSI DOKUMEN --}}
                    <div class="space-y-2">
                        <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">
                            Aksi dokumen
                        </p>

                        <div class="flex flex-wrap gap-2">
                            <a
                                href="{{ route('assessments.documents.download', $document) }}"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800 transition hover:border-amber-300 hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-300 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-300 dark:hover:bg-amber-950/70"
                            >
                                <flux:icon name="arrow-down-tray" class="size-4" />
                                Unduh Dokumen
                            </a>

                            @if (strtolower($document->mime_type ?? '') === 'application/pdf')
                                <button
                                    type="button"
                                    data-preview-url="{{ route('assessments.documents.preview', $document) }}"
                                    data-preview-download-url="{{ route('assessments.documents.download', $document) }}"
                                    data-preview-name="{{ $document->original_name ?? 'Dokumen' }}"
                                    data-preview-ep="{{ $elementCode }}"
                                    data-preview-instrument="{{ $documentInstrument }}"
                                    data-preview-uploader="{{ $document->uploader?->name ?? 'Pengguna klinik' }}"
                                    data-preview-mime="{{ strtolower($document->mime_type ?? '') }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800 transition hover:border-amber-300 hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-300 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-300 dark:hover:bg-amber-950/70"
                                >
                                    <flux:icon name="eye" class="size-4" />
                                    Lihat PDF
                                </button>
                            @endif

                            @if ($canReupload)
                                <button
                                    type="button"
                                    data-upload-modal-open="reupload"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-800 transition hover:border-blue-300 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-300 dark:border-blue-900 dark:bg-blue-950/40 dark:text-blue-300 dark:hover:bg-blue-950/70"
                                >
                                    <flux:icon name="arrow-path" class="size-4" />
                                    Upload Ulang
                                </button>
                            @endif

                            @if ($canUploadRevision)
                                <button
                                    type="button"
                                    data-upload-modal-open="revision"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800 transition hover:border-red-300 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-300 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300 dark:hover:bg-red-950/70"
                                >
                                    <flux:icon name="arrow-up-tray" class="size-4" />
                                    Unggah Revisi
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- AKTIVITAS TERBARU --}}
                    <div class="border-t border-zinc-200 pt-5 dark:border-zinc-800">
                        <div class="flex items-start gap-3">
                            <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">
                                <flux:icon name="clipboard-document-check" class="size-5" />
                            </div>

                            <div class="min-w-0 flex-1">
                                <flux:heading size="lg">
                                    Aktivitas Terbaru
                                </flux:heading>

                                <flux:subheading class="mt-1">
                                    Aktivitas terakhir pada dokumen ini, termasuk upload, revisi, dan review asesor.
                                </flux:subheading>
                            </div>
                        </div>

                        @if ($latestActivity)
                            @php
                                $activityIsReview = $latestActivity->type === 'reviewed';

                                $activityIsApproved = $activityIsReview
                                    && $latestActivity->status === 'approved';

                                $activityIsRevision = $activityIsReview
                                    && in_array($latestActivity->status, ['revision', 'rejected'], true);

                                $activityTypeLabel = match ($latestActivity->type) {
                                    'uploaded' => 'Dokumen diunggah',
                                    'reuploaded' => 'Dokumen diunggah ulang',
                                    'revision_uploaded' => 'Revisi dokumen diunggah',
                                    'reviewed' => match ($latestActivity->status) {
                                        'approved' => 'Dokumen disetujui asesor',
                                        'revision', 'rejected' => 'Asesor meminta revisi',
                                        default => 'Asesor menyimpan review',
                                    },
                                    default => 'Aktivitas dokumen',
                                };

                                $activityStatusLabel = match ($latestActivity->status) {
                                    'approved' => 'Disetujui',
                                    'revision', 'rejected' => 'Butuh Revisi',
                                    'pending' => 'Menunggu Review',
                                    default => null,
                                };

                                $activityStatusClass = match (true) {
                                    $activityIsApproved => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400',
                                    $activityIsRevision => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400',
                                    default => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400',
                                };

                                $activityNoteLabel = match ($latestActivity->type) {
                                    'uploaded' => 'CATATAN UPLOAD',
                                    'reuploaded' => 'CATATAN UPLOAD ULANG',
                                    'revision_uploaded' => 'CATATAN REVISI',
                                    'reviewed' => $activityIsRevision
                                        ? 'CATATAN REVISI ASESOR'
                                        : 'CATATAN ASESOR',
                                    default => 'CATATAN AKTIVITAS',
                                };
                            @endphp

                            <div class="mt-4 overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                                {{-- HEADER AKTIVITAS --}}
                                <div class="flex flex-col gap-3 bg-zinc-50/70 p-4 sm:flex-row sm:items-start sm:justify-between dark:bg-zinc-800/30">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-zinc-900 dark:text-white">
                                            {{ $activityTypeLabel }}
                                        </p>

                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                            Oleh {{ $latestActivity->user?->name ?? 'Pengguna tidak diketahui' }}
                                            <span class="mx-1">·</span>
                                            {{ $latestActivity->created_at?->format('d M Y, H:i') ?? '-' }}
                                        </p>
                                    </div>

                                    @if ($activityStatusLabel)
                                        <span class="inline-flex w-fit shrink-0 items-center rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-inset {{ $activityStatusClass }}">
                                            {{ $activityStatusLabel }}
                                        </span>
                                    @endif
                                </div>

                                {{-- DETAIL AKTIVITAS --}}
                                <div class="space-y-4 border-t border-zinc-200 p-4 dark:border-zinc-700">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                            {{ $activityNoteLabel }}
                                        </p>

                                        @if ($latestActivity->notes)
                                            <p class="mt-2 whitespace-pre-line break-words text-sm leading-6 text-zinc-700 dark:text-zinc-300">{{ trim($latestActivity->notes) }}</p>
                                        @else
                                            <p class="mt-2 text-sm italic text-zinc-500 dark:text-zinc-400">
                                                Tidak ada catatan pada aktivitas ini.
                                            </p>
                                        @endif
                                    </div>

                                    @if ($latestActivity->file_name)
                                        <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">
                                            File: {{ $latestActivity->file_name }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="mt-4 rounded-xl border border-dashed border-zinc-300 px-5 py-7 text-center dark:border-zinc-700">
                                <flux:icon name="clock" class="mx-auto size-7 text-zinc-400" />

                                <p class="mt-3 text-sm font-semibold text-zinc-800 dark:text-zinc-200">
                                    Belum ada aktivitas dokumen
                                </p>

                                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                                    Aktivitas upload dan review akan muncul di sini setelah tercatat.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </flux:card>

            {{-- MODAL UPLOAD ULANG --}}
            @if ($canReupload)
                <div
                    id="document-upload-modal-reupload"
                    class="fixed inset-0 z-[110] hidden items-center justify-center bg-black/60 p-3 sm:p-6"
                    data-upload-modal="reupload"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="document-upload-title-reupload"
                >
                    <div class="w-full max-w-xl overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-zinc-900">
                        <div class="flex items-start justify-between border-b border-zinc-200 px-5 py-4 sm:px-6 dark:border-zinc-700">
                            <div>
                                <h2 id="document-upload-title-reupload" class="text-lg font-semibold text-zinc-900 dark:text-white">
                                    Upload Ulang Dokumen
                                </h2>

                                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                                    File baru akan menggantikan file dokumen ini.
                                </p>
                            </div>

                            <button
                                type="button"
                                data-upload-modal-close
                                class="rounded-lg p-2 text-zinc-500 transition hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-zinc-300 dark:hover:bg-zinc-800"
                                aria-label="Tutup modal"
                            >
                                <flux:icon name="x-mark" class="size-5" />
                            </button>
                        </div>

                        <form
                            method="POST"
                            action="{{ route('assessments.documents.reupload', $document) }}"
                            enctype="multipart/form-data"
                            class="space-y-5 p-5 sm:p-6"
                            data-document-upload-form
                        >
                            @csrf

                            <div>
                                <label for="document-file-reupload" class="mb-2 block text-sm font-medium text-zinc-800 dark:text-zinc-200">
                                    File dokumen <span class="text-red-500">*</span>
                                </label>

                                <input
                                    id="document-file-reupload"
                                    type="file"
                                    name="document"
                                    required
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                    class="block w-full rounded-lg border border-zinc-300 text-sm text-zinc-700 file:mr-4 file:border-0 file:bg-zinc-100 file:px-4 file:py-3 file:font-medium dark:border-zinc-700 dark:text-zinc-200 dark:file:bg-zinc-800"
                                >

                                <p class="mt-2 text-xs leading-5 text-zinc-500 dark:text-zinc-400">
                                    Format PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, atau PNG. Maksimal 10 MB.
                                </p>

                                @error('document')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="document-note-reupload" class="mb-2 block text-sm font-medium text-zinc-800 dark:text-zinc-200">
                                    Catatan upload <span class="font-normal text-zinc-500">(opsional)</span>
                                </label>

                                <textarea
                                    id="document-note-reupload"
                                    name="upload_note"
                                    rows="3"
                                    maxlength="2000"
                                    placeholder="Tambahkan alasan upload ulang jika diperlukan..."
                                    class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-[var(--color-brand-gold)] focus:outline-none focus:ring-1 focus:ring-[var(--color-brand-gold)] dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"
                                ></textarea>

                                @error('upload_note')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex flex-col-reverse gap-2 border-t border-zinc-200 pt-4 sm:flex-row sm:justify-end dark:border-zinc-700">
                                <button
                                    type="button"
                                    data-upload-modal-close
                                    class="rounded-lg border border-zinc-300 px-4 py-2.5 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800"
                                >
                                    Batal
                                </button>

                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-[var(--color-brand-gold)] px-4 py-2.5 text-sm font-semibold text-white transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-[var(--color-brand-gold)]/50"
                                >
                                    <flux:icon name="arrow-path" class="size-4" />
                                    Upload Ulang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- MODAL UNGGAH REVISI --}}
            @if ($canUploadRevision)
                <div
                    id="document-upload-modal-revision"
                    class="fixed inset-0 z-[110] hidden items-center justify-center bg-black/60 p-3 sm:p-6"
                    data-upload-modal="revision"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="document-upload-title-revision"
                >
                    <div class="w-full max-w-xl overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-zinc-900">
                        <div class="flex items-start justify-between border-b border-zinc-200 px-5 py-4 sm:px-6 dark:border-zinc-700">
                            <div>
                                <h2 id="document-upload-title-revision" class="text-lg font-semibold text-zinc-900 dark:text-white">
                                    Unggah Revisi Dokumen
                                </h2>

                                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                                    Unggah dokumen yang telah diperbaiki sesuai catatan asesor.
                                </p>
                            </div>

                            <button
                                type="button"
                                data-upload-modal-close
                                class="rounded-lg p-2 text-zinc-500 transition hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-zinc-300 dark:hover:bg-zinc-800"
                                aria-label="Tutup modal"
                            >
                                <flux:icon name="x-mark" class="size-5" />
                            </button>
                        </div>

                        <form
                            method="POST"
                            action="{{ route('assessments.documents.revision', $document) }}"
                            enctype="multipart/form-data"
                            class="space-y-5 p-5 sm:p-6"
                            data-document-upload-form
                        >
                            @csrf

                            <div>
                                <label for="document-file-revision" class="mb-2 block text-sm font-medium text-zinc-800 dark:text-zinc-200">
                                    File dokumen <span class="text-red-500">*</span>
                                </label>

                                <input
                                    id="document-file-revision"
                                    type="file"
                                    name="document"
                                    required
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                    class="block w-full rounded-lg border border-zinc-300 text-sm text-zinc-700 file:mr-4 file:border-0 file:bg-zinc-100 file:px-4 file:py-3 file:font-medium dark:border-zinc-700 dark:text-zinc-200 dark:file:bg-zinc-800"
                                >

                                <p class="mt-2 text-xs leading-5 text-zinc-500 dark:text-zinc-400">
                                    Format PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, atau PNG. Maksimal 10 MB.
                                </p>

                                @error('document')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="document-note-revision" class="mb-2 block text-sm font-medium text-zinc-800 dark:text-zinc-200">
                                    Catatan upload <span class="font-normal text-zinc-500">(opsional)</span>
                                </label>

                                <textarea
                                    id="document-note-revision"
                                    name="upload_note"
                                    rows="3"
                                    maxlength="2000"
                                    placeholder="Jelaskan perbaikan yang sudah dilakukan..."
                                    class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-[var(--color-brand-gold)] focus:outline-none focus:ring-1 focus:ring-[var(--color-brand-gold)] dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"
                                ></textarea>

                                @error('upload_note')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex flex-col-reverse gap-2 border-t border-zinc-200 pt-4 sm:flex-row sm:justify-end dark:border-zinc-700">
                                <button
                                    type="button"
                                    data-upload-modal-close
                                    class="rounded-lg border border-zinc-300 px-4 py-2.5 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800"
                                >
                                    Batal
                                </button>

                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-[var(--color-brand-gold)] px-4 py-2.5 text-sm font-semibold text-white transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-[var(--color-brand-gold)]/50"
                                >
                                    <flux:icon name="arrow-up-tray" class="size-4" />
                                    Unggah Revisi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- TIMELINE AKTIVITAS DOKUMEN --}}
            <flux:card>
                <div class="border-b border-zinc-200 pb-5 dark:border-zinc-800">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <flux:heading size="lg">
                                Riwayat Aktivitas Dokumen
                            </flux:heading>

                            <flux:subheading class="mt-1">
                                Riwayat upload, penggantian file, revisi, dan keputusan asesor.
                            </flux:subheading>
                        </div>

                        <span class="inline-flex w-fit items-center rounded-full bg-zinc-100 px-3 py-1.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                            {{ $activities->count() }} aktivitas
                        </span>
                    </div>
                </div>

                <div class="mt-5">
                    @forelse ($activities as $activity)
                        @php
                            $isReview = $activity->type === 'reviewed';
                            $isApproved = $isReview && $activity->status === 'approved';
                            $isRevision = $isReview && in_array($activity->status, ['revision', 'rejected'], true);

                            $activityLabel = match ($activity->type) {
                                'uploaded' => 'Dokumen diunggah',
                                'reuploaded' => 'Dokumen diunggah ulang',
                                'revision_uploaded' => 'Revisi dokumen diunggah',
                                'reviewed' => match ($activity->status) {
                                    'approved' => 'Dokumen disetujui asesor',
                                    'revision', 'rejected' => 'Asesor meminta revisi',
                                    default => 'Asesor menyimpan review',
                                },
                                default => 'Aktivitas dokumen',
                            };

                            $activityStatusLabel = match ($activity->status) {
                                'approved' => 'Disetujui',
                                'revision', 'rejected' => 'Butuh Revisi',
                                'pending' => 'Menunggu Review',
                                default => null,
                            };

                            $activityStatusClass = match (true) {
                                $isApproved => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400',
                                $isRevision => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400',
                                default => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400',
                            };

                            $activityIconClass = match (true) {
                                $isApproved => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400',
                                $isRevision => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400',
                                default => 'bg-zinc-200 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300',
                            };

                            $noteLabel = match ($activity->type) {
                                'uploaded' => 'CATATAN UPLOAD',
                                'reuploaded' => 'CATATAN UPLOAD ULANG',
                                'revision_uploaded' => 'CATATAN REVISI',
                                'reviewed' => $isRevision ? 'CATATAN REVISI ASESOR' : 'CATATAN ASESOR',
                                default => 'CATATAN',
                            };
                        @endphp

                        <div class="relative flex gap-3 pb-5 last:pb-0">
                            @if (! $loop->last)
                                <div class="absolute bottom-0 left-[15px] top-8 w-px bg-zinc-200 dark:bg-zinc-700"></div>
                            @endif

                            <div class="relative z-10 flex size-8 shrink-0 items-center justify-center rounded-full {{ $activityIconClass }}">
                                @if ($isReview)
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-4" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4m5 2a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-4" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0L8 8m4-4 4 4M5 14v4.25A1.75 1.75 0 0 0 6.75 20h10.5A1.75 1.75 0 0 0 19 18.25V14" />
                                    </svg>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1 pt-0.5">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-zinc-900 dark:text-white">
                                            {{ $activityLabel }}
                                        </p>

                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                            Oleh
                                            <span class="font-medium text-zinc-700 dark:text-zinc-300">
                                                {{ $activity->user?->name ?? 'Pengguna tidak diketahui' }}
                                            </span>

                                            <span class="mx-1">·</span>

                                            {{ $activity->created_at?->format('d M Y, H:i') ?? '-' }}
                                        </p>
                                    </div>

                                    @if ($activityStatusLabel)
                                        <span class="inline-flex w-fit shrink-0 items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $activityStatusClass }}">
                                            {{ $activityStatusLabel }}
                                        </span>
                                    @endif
                                </div>

                                @if ($activity->file_name)
                                    <p class="mt-2 break-words text-xs text-zinc-500 dark:text-zinc-400">
                                        File: {{ $activity->file_name }}
                                    </p>
                                @endif

                                @if ($activity->notes)
                                    <div class="mt-2 rounded-lg border border-zinc-200 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-900">
                                        <p class="m-0 text-[10px] font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                                            {{ $noteLabel }}
                                        </p>

                                        <p class="m-0 whitespace-pre-line break-words text-sm leading-5 text-zinc-600 dark:text-zinc-300">
                                            {{ trim($activity->notes) }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-zinc-300 bg-zinc-50 px-6 py-8 text-center dark:border-zinc-700 dark:bg-zinc-800/50">
                            <div class="mx-auto flex size-11 items-center justify-center rounded-full bg-white text-zinc-400 ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-zinc-700">
                                <flux:icon name="clipboard-document-list" class="size-5" />
                            </div>

                            <p class="mt-3 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Belum ada aktivitas dokumen
                            </p>

                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                                Aktivitas upload dan review akan muncul di sini setelah tercatat.
                            </p>
                        </div>
                    @endforelse
                </div>
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
            <div class="flex flex-col gap-3 border-b border-zinc-200 px-4 py-3 dark:border-zinc-700 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div class="min-w-0 flex-1">
                    <h2 id="document-preview-title" class="truncate text-base font-semibold text-zinc-900 dark:text-white">
                        Preview Dokumen
                    </h2>

                    <div id="document-preview-information" class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-xs text-zinc-500 dark:text-zinc-400"></div>
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

            <div class="relative min-h-0 flex-1 bg-zinc-100 dark:bg-zinc-950">
                <iframe
                    id="document-preview-frame"
                    src="about:blank"
                    title="Preview dokumen"
                    class="h-full w-full border-0"
                ></iframe>

                <div id="document-preview-fallback" class="absolute inset-0 hidden items-center justify-center p-6 text-center">
                    <div class="max-w-md">
                        <flux:icon name="exclamation-triangle" class="mx-auto size-12 text-zinc-400" />

                        <p class="mt-4 font-semibold text-zinc-900 dark:text-white">
                            Preview tidak tersedia
                        </p>

                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                            Preview langsung hanya tersedia untuk file PDF. Silakan gunakan tombol Unduh untuk membuka dokumen ini.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT MODAL UPLOAD --}}
    <script>
        (() => {
            window.initializeDocumentUploadModals = function () {
                document.querySelectorAll('[data-upload-modal-open]').forEach((button) => {
                    if (button.dataset.uploadBound === 'true') return;

                    button.dataset.uploadBound = 'true';

                    button.addEventListener('click', () => {
                        const type = button.dataset.uploadModalOpen;
                        const modal = document.querySelector(`[data-upload-modal="${type}"]`);

                        if (!modal) return;

                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        document.body.classList.add('overflow-hidden');

                        modal.querySelector('input[type="file"]')?.focus();
                    });
                });

                document.querySelectorAll('[data-upload-modal]').forEach((modal) => {
                    if (modal.dataset.initialized === 'true') return;

                    modal.dataset.initialized = 'true';

                    const closeModal = () => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');

                        const anyOpen = [...document.querySelectorAll('[data-upload-modal]')]
                            .some((element) => !element.classList.contains('hidden'));

                        if (!anyOpen) {
                            document.body.classList.remove('overflow-hidden');
                        }
                    };

                    modal.querySelectorAll('[data-upload-modal-close]').forEach((button) => {
                        button.addEventListener('click', closeModal);
                    });

                    modal.addEventListener('click', (event) => {
                        if (event.target === modal) closeModal();
                    });

                    modal._closeUploadModal = closeModal;
                });

                if (!document.documentElement.dataset.uploadModalEscapeBound) {
                    document.documentElement.dataset.uploadModalEscapeBound = 'true';

                    document.addEventListener('keydown', (event) => {
                        if (event.key !== 'Escape') return;

                        document.querySelectorAll('[data-upload-modal]').forEach((modal) => {
                            if (!modal.classList.contains('hidden')) {
                                modal._closeUploadModal?.();
                            }
                        });
                    });
                }
            };

            window.initializeDocumentUploadModals();

            document.addEventListener('DOMContentLoaded', window.initializeDocumentUploadModals);
            document.addEventListener('livewire:navigated', window.initializeDocumentUploadModals);
        })();
    </script>

    {{-- SCRIPT PREVIEW DOKUMEN --}}
    <script>
        (() => {
            window.initializeClinicDocumentPreview = function () {
                const modal = document.getElementById('document-preview-modal');

                if (!modal) return;

                const title = document.getElementById('document-preview-title');
                const information = document.getElementById('document-preview-information');
                const frame = document.getElementById('document-preview-frame');
                const fallback = document.getElementById('document-preview-fallback');
                const downloadLink = document.getElementById('document-preview-download');
                const closeButton = document.getElementById('document-preview-close');

                if (!title || !information || !frame || !fallback || !downloadLink || !closeButton) {
                    return;
                }

                if (modal.dataset.initialized !== 'true') {
                    modal.dataset.initialized = 'true';

                    const closePreview = () => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        frame.src = 'about:blank';
                        document.body.classList.remove('overflow-hidden');
                    };

                    closeButton.addEventListener('click', closePreview);

                    modal.addEventListener('click', (event) => {
                        if (event.target === modal) closePreview();
                    });

                    document.addEventListener('keydown', (event) => {
                        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                            closePreview();
                        }
                    });

                    modal._closePreview = closePreview;
                }

                const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (character) => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;',
                })[character]);

                document.querySelectorAll('[data-preview-url]').forEach((button) => {
                    if (button.dataset.previewBound === 'true') return;

                    button.dataset.previewBound = 'true';

                    button.addEventListener('click', () => {
                        title.textContent = button.dataset.previewName || 'Dokumen';

                        information.innerHTML = `
                            <span><strong class="font-semibold text-zinc-700 dark:text-zinc-200">EP:</strong> ${escapeHtml(button.dataset.previewEp || '-')}</span>
                            <span><strong class="font-semibold text-zinc-700 dark:text-zinc-200">Instrumen:</strong> ${escapeHtml(button.dataset.previewInstrument || '-')}</span>
                            <span><strong class="font-semibold text-zinc-700 dark:text-zinc-200">Pengunggah:</strong> ${escapeHtml(button.dataset.previewUploader || '-')}</span>
                        `;

                        downloadLink.href = button.dataset.previewDownloadUrl || '#';

                        const mime = (button.dataset.previewMime || '').toLowerCase();

                        if (mime === 'application/pdf') {
                            frame.src = button.dataset.previewUrl;
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
                    });
                });
            };

            window.initializeClinicDocumentPreview();

            document.addEventListener('DOMContentLoaded', window.initializeClinicDocumentPreview);
            document.addEventListener('livewire:navigated', window.initializeClinicDocumentPreview);
        })();
    </script>
</x-layouts::app.sidebar>
