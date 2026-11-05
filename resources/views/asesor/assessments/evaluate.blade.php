<x-layouts::app.sidebar :title="'Review Dokumen'">
    @php
        /*
         * Review terbaru: created_at descending, lalu id descending.
         * Ambil review pertama setelah pengurutan.
         */
        $latestReview = $document->reviews
            ->sortByDesc('id')
            ->sortByDesc('created_at')
            ->first();

        $documentStatus = old('status', $latestReview?->status ?? 'pending');

        $statusClasses = match ($documentStatus) {
            'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400',
            'revision', 'rejected' => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400',
            default => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400',
        };

        $statusLabel = match ($documentStatus) {
            'approved' => 'Disetujui',
            'revision', 'rejected' => 'Butuh Revisi',
            default => 'Menunggu Review',
        };

        $initialStatus = $documentStatus;
        $initialNotes = old('notes', $latestReview?->notes ?? '');

        $documentInstrument = match ($document->instrument_type) {
            'regulasi' => 'Regulasi',
            'dokumen' => 'Dokumen',
            default => 'Dokumen',
        };

        $elementCode = $item->element->code ?? '-';

        $activities = $document->activities
            ->sortByDesc('id')
            ->sortByDesc('created_at')
            ->values();

        $activityCount = $activities->count();
        $latestActivity = $activities->first();

        $reviewLabel = match ($latestReview?->status) {
            'approved' => 'Disetujui',
            'revision', 'rejected' => 'Butuh Revisi',
            default => 'Menunggu Review',
        };

        $reviewClasses = match ($latestReview?->status) {
            'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400',
            'revision', 'rejected' => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400',
            default => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400',
        };

        $activityLabels = [
            'uploaded' => 'Dokumen diunggah',
            'reuploaded' => 'Dokumen diunggah ulang',
            'revision_uploaded' => 'Revisi dokumen diunggah',
        ];

        $activityStatusLabels = [
            'pending' => 'Menunggu Review',
            'approved' => 'Disetujui',
            'revision' => 'Butuh Revisi',
            'rejected' => 'Ditolak',
        ];
    @endphp

    <flux:main class="!pt-0">
        <div class="space-y-4 pt-5">

            {{-- PAGE HEADER --}}
            
{{-- PAGE HEADER --}}
<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <div class="flex flex-wrap items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
            <a
                href="{{ route('assessments.index') }}"
                class="transition hover:text-zinc-900 dark:hover:text-white"
            >
                Assessments
            </a>

            <span>/</span>

            <a
                href="{{ route('assessments.show', $assessment) }}"
                class="transition hover:text-zinc-900 dark:hover:text-white"
            >
                {{ $assessment->clinic?->name ?? 'Assessment' }}
            </a>

            <span>/</span>

            <a
                href="{{ route('assessments.items.show', [
                    'assessment' => $assessment,
                    'item' => $item,
                ]) }}"
                class="transition hover:text-zinc-900 dark:hover:text-white"
            >
                {{ $elementCode }}
            </a>

            <span>/</span>

            <span class="text-zinc-700 dark:text-zinc-300">
                Review Dokumen
            </span>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        {{-- KEMBALI KE DASHBOARD --}}
        <flux:button
            href="{{ route('dashboard') }}"
            variant="ghost"
            icon="squares-2x2"
        >
            Kembali ke Dashboard
        </flux:button>

        {{-- KEMBALI KE HALAMAN EP --}}
        <flux:button
            href="{{ route('assessments.items.show', [
                'assessment' => $assessment,
                'item' => $item,
            ]) }}"
            variant="ghost"
            icon="arrow-left"
        >
            Kembali
        </flux:button>
    </div>
</div>

            {{-- FLASH MESSAGES --}}
            @if (session('error'))
                <flux:callout variant="danger">
                    {{ session('error') }}
                </flux:callout>
            @endif

            @if ($errors->any())
                <flux:callout variant="danger">
                    <div class="space-y-2">
                        <p class="font-semibold">
                            Review belum dapat disimpan.
                        </p>

                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </flux:callout>
            @endif

            {{-- INFORMASI DOKUMEN --}}
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

                        {{-- STATUS REVIEW --}}
                        <span class="inline-flex w-fit shrink-0 items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-inset {{ $reviewClasses }}">
                            @if ($latestReview?->status === 'approved')
                                <flux:icon name="check-circle" class="size-4" />
                            @elseif (in_array($latestReview?->status, ['revision', 'rejected'], true))
                                <flux:icon name="exclamation-circle" class="size-4" />
                            @else
                                <flux:icon name="clock" class="size-4" />
                            @endif

                            {{ $reviewLabel }}
                        </span>
                    </div>

                    {{-- METADATA --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center rounded-lg bg-zinc-100 px-3 py-1.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                            EP {{ $elementCode }}
                        </span>

                        @if ($item->element->chapter)
                            <span class="inline-flex items-center rounded-lg bg-zinc-100 px-3 py-1.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                                {{ $item->element->chapter->name }}
                            </span>
                        @endif

                        <span class="inline-flex items-center rounded-lg bg-zinc-100 px-3 py-1.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                            {{ $documentInstrument }}
                        </span>
                    </div>

                    {{-- PENGUNGGAH DAN AKTIVITAS --}}
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
                                {{ $activityCount }} aktivitas
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
                            {{-- UNDUH DOKUMEN --}}
                            <a
                                href="{{ route('assessments.documents.download', $document) }}"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800 transition hover:border-amber-300 hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-300 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-300 dark:hover:bg-amber-950/70"
                            >
                                <flux:icon name="arrow-down-tray" class="size-4" />
                                Unduh Dokumen
                            </a>

                            {{-- LIHAT PDF --}}
                            @if (strtolower($document->mime_type ?? '') === 'application/pdf')
                                <button
                                    type="button"
                                    data-preview-url="{{ route('assessments.documents.preview', $document) }}"
                                    data-preview-download-url="{{ route('assessments.documents.download', $document) }}"
                                    data-preview-name="{{ $document->original_name ?? 'Dokumen' }}"
                                    data-preview-ep="{{ $elementCode }}"
                                    data-preview-instrument="{{ $documentInstrument }}"
                                    data-preview-uploader="{{ $document->uploader?->name ?? 'Pengguna klinik' }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800 transition hover:border-amber-300 hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-300 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-300 dark:hover:bg-amber-950/70"
                                >
                                    <flux:icon name="eye" class="size-4" />
                                    Lihat PDF
                                </button>
                            @endif

                            {{-- NILAI DOKUMEN --}}
                            <button
                                type="button"
                                data-review-modal-open
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-800 transition hover:border-blue-300 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-300 dark:border-blue-900 dark:bg-blue-950/40 dark:text-blue-300"
                            >
                                <flux:icon name="clipboard-document-check" class="size-4" />
                                Nilai Dokumen
                            </button>
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
                                $activityTypeLabel = match ($latestActivity->type) {
                                    'uploaded' => 'Dokumen diunggah',
                                    'reuploaded' => 'Dokumen diunggah ulang',
                                    'revision_uploaded' => 'Revisi dokumen diunggah',
                                    'reviewed' => 'Asesor memberikan review',
                                    default => 'Aktivitas dokumen',
                                };

                                $activityActor = match ($latestActivity->user?->role) {
                                    'asesor' => 'Asesor',
                                    'klinik' => 'Klinik',
                                    'admin' => 'Admin',
                                    default => $latestActivity->user?->name ?? 'Pengguna',
                                };

                                $activityIsReview = $latestActivity->type === 'reviewed';
                            @endphp

                            <div class="mt-4 overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                                <div class="flex flex-col gap-3 bg-zinc-50/70 p-4 sm:flex-row sm:items-center sm:justify-between dark:bg-zinc-800/30">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-zinc-900 dark:text-white">
                                            {{ $activityTypeLabel }}
                                        </p>

                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                            Oleh {{ $latestActivity->user?->name ?? $activityActor }}
                                            <span class="mx-1">·</span>
                                            {{ $latestActivity->created_at?->format('d M Y, H:i') ?? '-' }}
                                        </p>
                                    </div>

                                    {{-- BADGE STATUS AKTIVITAS TERBARU --}}
                                    @php
                                        $latestActivityStatus = $latestActivity->status;

                                        $latestActivityStatusClass = match ($latestActivityStatus) {
                                            'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400',
                                            'revision', 'rejected' => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400',
                                            'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400',
                                            default => 'bg-zinc-100 text-zinc-600 ring-zinc-500/20 dark:bg-zinc-700 dark:text-zinc-300',
                                        };

                                        $latestActivityStatusLabel = $activityStatusLabels[$latestActivityStatus]
                                            ?? 'Aktivitas Dokumen';
                                    @endphp

                                    @if ($latestActivityStatus)
                                        <span class="inline-flex w-fit items-center rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-inset {{ $latestActivityStatusClass }}">
                                            {{ $latestActivityStatusLabel }}
                                        </span>
                                    @endif
                                </div>

                                <div class="border-t border-zinc-200 p-4 dark:border-zinc-700">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                        {{ $activityIsReview ? 'Catatan Asesor' : 'Catatan dari Klinik' }}
                                    </p>

                                    @if ($latestActivity->notes)
                                        <p class="mt-2 whitespace-pre-line break-words text-sm leading-6 text-zinc-700 dark:text-zinc-300">{{ trim($latestActivity->notes) }}</p>
                                    @else
                                        <p class="mt-2 text-sm italic text-zinc-500 dark:text-zinc-400">
                                            Tidak ada catatan pada aktivitas ini.
                                        </p>
                                    @endif

                                    @if ($latestActivity->file_name)
                                        <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">
                                            File: {{ $latestActivity->file_name }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @elseif ($latestReview)
                            <div class="mt-4 overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                                <div class="bg-zinc-50/70 p-4 dark:bg-zinc-800/30">
                                    <p class="text-sm font-semibold text-zinc-900 dark:text-white">
                                        Hasil Review Terakhir
                                    </p>

                                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                        Oleh {{ $latestReview->reviewer?->name ?? 'Asesor' }}
                                        <span class="mx-1">·</span>
                                        {{ $latestReview->created_at?->format('d M Y, H:i') ?? '-' }}
                                    </p>

                                    <span class="mt-3 inline-flex w-fit items-center rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-inset {{ $reviewClasses }}">
                                        {{ $reviewLabel }}
                                    </span>
                                </div>

                                <div class="border-t border-zinc-200 p-4 dark:border-zinc-700">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                        Catatan Asesor
                                    </p>

                                    <p class="mt-2 whitespace-pre-line break-words text-sm leading-6 text-zinc-700 dark:text-zinc-300">
                                        {{ $latestReview->notes ? trim($latestReview->notes) : 'Tidak ada catatan dari asesor.' }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="mt-4 rounded-xl border border-dashed border-zinc-300 px-5 py-7 text-center dark:border-zinc-700">
                                <flux:icon name="clock" class="mx-auto size-7 text-zinc-400" />

                                <p class="mt-3 text-sm font-semibold text-zinc-800 dark:text-zinc-200">
                                    Belum ada aktivitas dokumen
                                </p>

                                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                                    Aktivitas upload dan review akan muncul di sini.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </flux:card>

            {{-- RIWAYAT AKTIVITAS DOKUMEN --}}
            <flux:card>
                <div class="border-b border-zinc-200 pb-5 dark:border-zinc-800">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <flux:heading size="lg">
                                Riwayat Aktivitas Dokumen
                            </flux:heading>

                            <flux:subheading class="mt-1">
                                Riwayat upload, penggantian file, revisi, dan keputusan asesor.
                            </flux:subheading>
                        </div>

                        <flux:badge variant="neutral">
                            {{ $activityCount }} aktivitas
                        </flux:badge>
                    </div>
                </div>

                @if ($activityCount > 0)
                    <div class="mt-5 space-y-0">
                        @foreach ($activities as $activity)
                            @php
                                $isReview = $activity->type === 'reviewed';
                                $isApproved = $activity->status === 'approved';

                                $isRevision = in_array(
                                    $activity->status,
                                    ['revision', 'rejected'],
                                    true
                                );

                                $activityTitle = $activityLabels[$activity->type]
                                    ?? (
                                        $isReview
                                            ? (
                                                $isApproved
                                                    ? 'Dokumen disetujui asesor'
                                                    : (
                                                        $isRevision
                                                            ? 'Asesor meminta revisi'
                                                            : 'Asesor menyimpan review'
                                                    )
                                            )
                                            : 'Aktivitas dokumen'
                                    );

                                $activityStatusClass = match ($activity->status) {
                                    'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400',
                                    'revision', 'rejected' => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400',
                                    default => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400',
                                };

                                $activityIconClass = $isApproved
                                    ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400'
                                    : (
                                        $isRevision
                                            ? 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400'
                                            : 'bg-zinc-200 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300'
                                    );

                                $noteLabel = match ($activity->type) {
                                    'uploaded' => 'CATATAN UPLOAD',
                                    'reuploaded' => 'CATATAN UPLOAD ULANG',
                                    'revision_uploaded' => 'CATATAN REVISI',
                                    'reviewed' => in_array($activity->status, ['revision', 'rejected'], true)
                                        ? 'CATATAN REVISI ASESOR'
                                        : 'CATATAN ASESOR',
                                    default => 'CATATAN',
                                };
                            @endphp

                            <div class="relative flex gap-3 pb-5 last:pb-0">
                                @if (! $loop->last)
                                    <div class="absolute bottom-0 left-[15px] top-8 w-px bg-zinc-200 dark:bg-zinc-700"></div>
                                @endif

                                <div class="relative z-10 flex size-8 shrink-0 items-center justify-center rounded-full {{ $activityIconClass }}">
                                    @if ($isReview)
                                        <flux:icon name="check-circle" class="size-4" />
                                    @else
                                        <flux:icon name="arrow-up-tray" class="size-4" />
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1 pt-0.5">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-zinc-900 dark:text-white">
                                                {{ $activityTitle }}
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

                                        @if ($activity->status)
                                            <span class="inline-flex w-fit shrink-0 items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $activityStatusClass }}">
                                                {{ $activityStatusLabels[$activity->status] ?? ucfirst($activity->status) }}
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
                                            <div class="flex flex-col gap-1">
                                                <span class="text-[10px] font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                                                    {{ $noteLabel }}
                                                </span>

                                                <div class="whitespace-pre-line break-words text-sm leading-5 text-zinc-600 dark:text-zinc-300">{{ trim($activity->notes) }}</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="mt-5 rounded-lg border border-dashed border-zinc-300 px-4 py-5 text-center dark:border-zinc-700">
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            Belum ada riwayat aktivitas untuk dokumen ini.
                        </p>
                    </div>
                @endif
            </flux:card>
        </div>
    </flux:main>

    {{-- MODAL REVIEW DOKUMEN --}}
    <div
        id="document-review-modal"
        class="fixed inset-0 z-[120] hidden items-center justify-center bg-black/60 p-3 sm:p-6"
        role="dialog"
        aria-modal="true"
        aria-labelledby="document-review-modal-title"
    >
        <div class="flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-zinc-900">

            {{-- HEADER MODAL --}}
            <div class="flex items-start justify-between gap-4 border-b border-zinc-200 px-5 py-4 sm:px-6 dark:border-zinc-700">
                <div class="min-w-0">
                    <h2
                        id="document-review-modal-title"
                        class="text-lg font-semibold text-zinc-900 dark:text-white"
                    >
                        Form Review Dokumen
                    </h2>

                    <p class="mt-1 break-words text-sm text-zinc-500 dark:text-zinc-400">
                        {{ $document->original_name ?? 'Dokumen' }}
                    </p>
                </div>

                <button
                    type="button"
                    data-review-modal-close
                    class="flex size-9 shrink-0 items-center justify-center rounded-lg text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-800 dark:hover:text-white"
                    aria-label="Tutup modal"
                >
                    <flux:icon name="x-mark" class="size-5" />
                </button>
            </div>

            {{-- FORM REVIEW --}}
            <form
                id="assessment-form"
                method="POST"
                action="{{ route('assessments.documents.evaluate.update', ['document' => $document]) }}"
                data-initial-status="{{ $initialStatus }}"
                data-initial-notes="{{ $initialNotes }}"
                class="flex min-h-0 flex-col"
            >
                @csrf

                <div class="space-y-5 overflow-y-auto p-5 sm:p-6">
                    {{-- STATUS --}}
                    <div>
                        <flux:select
                            name="status"
                            label="Status Review Dokumen"
                            required
                        >
                            <flux:select.option
                                value="pending"
                                :selected="$initialStatus === 'pending'"
                            >
                                Menunggu Review
                            </flux:select.option>

                            <flux:select.option
                                value="approved"
                                :selected="$initialStatus === 'approved'"
                            >
                                Disetujui
                            </flux:select.option>

                            <flux:select.option
                                value="revision"
                                :selected="in_array($initialStatus, ['revision', 'rejected'], true)"
                            >
                                Butuh Revisi
                            </flux:select.option>
                        </flux:select>

                        <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">
                            Pilih status berdasarkan hasil pemeriksaan dokumen.
                        </p>
                    </div>

                    {{-- CATATAN --}}
                    <div>
                        <flux:textarea
                            name="notes"
                            label="Catatan / Masukan Asesor"
                            rows="5"
                            maxlength="2000"
                            placeholder="Tuliskan hasil pemeriksaan, kekurangan dokumen, atau rekomendasi perbaikan..."
                        >{{ $initialNotes }}</flux:textarea>

                        <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">
                            Catatan wajib diisi apabila dokumen membutuhkan revisi.
                        </p>
                    </div>

                    {{-- VALIDASI --}}
                    @if ($errors->any())
                        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 dark:border-red-900 dark:bg-red-950/30">
                            <p class="text-sm font-semibold text-red-800 dark:text-red-300">
                                Review belum dapat disimpan.
                            </p>

                            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700 dark:text-red-300">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                {{-- FOOTER MODAL --}}
                <div class="flex flex-col-reverse gap-2 border-t border-zinc-200 px-5 py-4 sm:flex-row sm:justify-end sm:px-6 dark:border-zinc-700">
                    <button
                        type="button"
                        data-review-modal-close
                        class="rounded-lg border border-zinc-300 px-4 py-2.5 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800"
                    >
                        Batal
                    </button>

                    <button
                        id="save-assessment-button"
                        type="submit"
                        disabled
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-[var(--color-brand-gold)] px-4 py-2.5 text-sm font-semibold text-white transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <flux:icon name="check" class="size-4" />
                        Simpan Review
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPT MODAL REVIEW --}}
    <script>
        (() => {
            function initializeReviewModal() {
                const modal = document.getElementById('document-review-modal');

                if (!modal || modal.dataset.initialized === 'true') {
                    return;
                }

                modal.dataset.initialized = 'true';

                let previousFocus = null;

                const openModal = (trigger) => {
                    previousFocus = trigger;

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');

                    modal.querySelector('[name="status"]')?.focus();
                };

                const closeModal = () => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');

                    const anotherModalOpen = [
                        ...document.querySelectorAll('[role="dialog"]')
                    ].some((element) => {
                        return element !== modal
                            && !element.classList.contains('hidden');
                    });

                    if (!anotherModalOpen) {
                        document.body.classList.remove('overflow-hidden');
                    }

                    if (previousFocus?.isConnected) {
                        previousFocus.focus();
                    }
                };

                document.querySelectorAll('[data-review-modal-open]').forEach((button) => {
                    if (button.dataset.reviewBound === 'true') {
                        return;
                    }

                    button.dataset.reviewBound = 'true';

                    button.addEventListener('click', () => {
                        openModal(button);
                    });
                });

                modal.querySelectorAll('[data-review-modal-close]').forEach((button) => {
                    if (button.dataset.reviewCloseBound === 'true') {
                        return;
                    }

                    button.dataset.reviewCloseBound = 'true';
                    button.addEventListener('click', closeModal);
                });

                if (modal.dataset.backdropBound !== 'true') {
                    modal.dataset.backdropBound = 'true';

                    modal.addEventListener('click', (event) => {
                        if (event.target === modal) {
                            closeModal();
                        }
                    });
                }

                if (document.documentElement.dataset.reviewEscapeBound !== 'true') {
                    document.documentElement.dataset.reviewEscapeBound = 'true';

                    document.addEventListener('keydown', (event) => {
                        if (
                            event.key === 'Escape'
                            && !modal.classList.contains('hidden')
                        ) {
                            closeModal();
                        }
                    });
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener(
                    'DOMContentLoaded',
                    initializeReviewModal,
                    { once: true }
                );
            } else {
                initializeReviewModal();
            }

            document.addEventListener('livewire:navigated', initializeReviewModal);
        })();
    </script>

    {{-- MODAL PREVIEW DOKUMEN --}}
    <div
        id="document-preview-modal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/70 p-3 sm:p-6"
        role="dialog"
        aria-modal="true"
        aria-labelledby="document-preview-title"
    >
        <div class="flex h-full max-h-[94vh] w-full max-w-[88vw] flex-col overflow-hidden rounded-xl bg-white shadow-2xl dark:bg-zinc-900">
            <div class="flex items-center gap-4 border-b border-zinc-200 px-5 py-3 dark:border-zinc-700">
                <div class="min-w-0 flex-1">
                    <h2
                        id="document-preview-title"
                        class="truncate text-sm font-semibold text-zinc-900 dark:text-white"
                    >
                        Preview Dokumen
                    </h2>

                    <p
                        id="document-preview-information"
                        class="mt-1 truncate text-xs text-zinc-500 dark:text-zinc-400"
                    ></p>
                </div>

                <a
                    id="document-preview-download"
                    href="#"
                    class="shrink-0 rounded-lg px-3 py-2 text-sm font-medium text-blue-600 transition hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-zinc-800"
                >
                    Unduh
                </a>

                <button
                    type="button"
                    id="document-preview-close"
                    class="flex size-9 shrink-0 items-center justify-center rounded-lg text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-800 dark:hover:text-white"
                    aria-label="Tutup preview"
                >
                    <flux:icon name="x-mark" class="size-5" />
                </button>
            </div>

            <div class="min-h-0 flex-1 bg-zinc-200 dark:bg-zinc-950">
                <iframe
                    id="document-preview-frame"
                    src="about:blank"
                    title="Preview dokumen PDF"
                    class="h-full w-full border-0"
                ></iframe>
            </div>
        </div>
    </div>

    {{-- SCRIPT PREVIEW DOKUMEN --}}
    <script>
        (() => {
            function initializeDocumentPreview() {
                const modal = document.getElementById('document-preview-modal');

                if (!modal || modal.dataset.initialized === 'true') {
                    return;
                }

                modal.dataset.initialized = 'true';

                const title = document.getElementById('document-preview-title');
                const information = document.getElementById('document-preview-information');
                const frame = document.getElementById('document-preview-frame');
                const closeButton = document.getElementById('document-preview-close');
                const downloadLink = document.getElementById('document-preview-download');

                let previousFocus = null;

                function closePreview() {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    frame.src = 'about:blank';

                    const anotherModalOpen = [
                        ...document.querySelectorAll('[role="dialog"]')
                    ].some((element) => {
                        return !element.classList.contains('hidden');
                    });

                    if (!anotherModalOpen) {
                        document.body.classList.remove('overflow-hidden');
                    }

                    if (previousFocus && previousFocus.isConnected) {
                        previousFocus.focus();
                    }
                }

                function openPreview(button) {
                    const previewUrl = button.dataset.previewUrl;
                    const downloadUrl = button.dataset.previewDownloadUrl;

                    if (!previewUrl || !downloadUrl) {
                        return;
                    }

                    previousFocus = button;
                    title.textContent = button.dataset.previewName || 'Preview Dokumen';
                    information.replaceChildren();

                    const metadata = [
                        ['EP:', button.dataset.previewEp || '-'],
                        ['Instrumen:', button.dataset.previewInstrument || '-'],
                        ['Pengunggah:', button.dataset.previewUploader || '-'],
                    ];

                    metadata.forEach(([label, value], index) => {
                        const labelElement = document.createElement('strong');
                        labelElement.textContent = label;

                        information.appendChild(labelElement);
                        information.appendChild(document.createTextNode(` ${value}`));

                        if (index < metadata.length - 1) {
                            information.appendChild(document.createTextNode(' | '));
                        }
                    });

                    downloadLink.href = downloadUrl;
                    frame.src = previewUrl;

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                    closeButton.focus();
                }

                function bindPreviewButtons() {
                    document.querySelectorAll('[data-preview-url]').forEach((button) => {
                        if (button.dataset.previewInitialized === 'true') {
                            return;
                        }

                        button.dataset.previewInitialized = 'true';
                        button.addEventListener('click', () => openPreview(button));
                    });
                }

                bindPreviewButtons();

                closeButton.addEventListener('click', closePreview);

                modal.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        closePreview();
                    }
                });

                document.addEventListener('keydown', (event) => {
                    if (
                        event.key === 'Escape'
                        && !modal.classList.contains('hidden')
                    ) {
                        closePreview();
                    }
                });

                document.addEventListener('livewire:navigated', bindPreviewButtons);
            }

            if (document.readyState === 'loading') {
                document.addEventListener(
                    'DOMContentLoaded',
                    initializeDocumentPreview,
                    { once: true }
                );
            } else {
                initializeDocumentPreview();
            }

            document.addEventListener('livewire:navigated', initializeDocumentPreview);
        })();
    </script>

    {{-- REVIEW FORM UX --}}
    <script>
        (() => {
            function initializeAssessmentForm() {
                const form = document.getElementById('assessment-form');

                if (!form || form.dataset.initialized === 'true') {
                    return;
                }

                form.dataset.initialized = 'true';

                const statusField = form.querySelector('[name="status"]');
                const notesField = form.querySelector('[name="notes"]');
                const saveButton = document.getElementById('save-assessment-button');

                if (!statusField || !notesField || !saveButton) {
                    return;
                }

                const initialStatus = form.dataset.initialStatus;
                const initialNotes = form.dataset.initialNotes ?? '';

                let isSubmitting = false;

                function updateSaveButton() {
                    const hasChanges =
                        statusField.value !== initialStatus ||
                        notesField.value !== initialNotes;

                    saveButton.disabled = !hasChanges || isSubmitting;
                }

                statusField.addEventListener('change', updateSaveButton);
                notesField.addEventListener('input', updateSaveButton);

                form.addEventListener('submit', async (event) => {
                    event.preventDefault();

                    if (isSubmitting || !form.reportValidity()) {
                        return;
                    }

                    if (
                        statusField.value === 'revision'
                        && !notesField.value.trim()
                    ) {
                        notesField.setCustomValidity(
                            'Catatan wajib diisi apabila dokumen membutuhkan revisi.'
                        );

                        notesField.reportValidity();
                        notesField.setCustomValidity('');
                        notesField.focus();

                        return;
                    }

                    const hasChanges =
                        statusField.value !== initialStatus ||
                        notesField.value !== initialNotes;

                    if (!hasChanges) {
                        return;
                    }

                    if (!window.Swal) {
                        if (confirm('Simpan perubahan review dokumen ini?')) {
                            isSubmitting = true;
                            HTMLFormElement.prototype.submit.call(form);
                        }

                        return;
                    }

                    const result = await window.Swal.fire({
                        title: 'Simpan review dokumen?',
                        text: 'Status dan catatan review dokumen ini akan disimpan.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, simpan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#b8954a',
                        cancelButtonColor: '#71717a',
                        reverseButtons: true,
                    });

                    if (!result.isConfirmed) {
                        return;
                    }

                    isSubmitting = true;
                    saveButton.disabled = true;

                    window.Swal.fire({
                        title: 'Menyimpan review...',
                        text: 'Mohon tunggu sebentar.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => window.Swal.showLoading(),
                    });

                    HTMLFormElement.prototype.submit.call(form);
                });

                updateSaveButton();
            }

            if (document.readyState === 'loading') {
                document.addEventListener(
                    'DOMContentLoaded',
                    initializeAssessmentForm,
                    { once: true }
                );
            } else {
                initializeAssessmentForm();
            }

            document.addEventListener('livewire:navigated', initializeAssessmentForm);
        })();
    </script>

    {{-- SUCCESS TOAST --}}
    @if (session('success'))
        <script>
            (() => {
                const showSuccessToast = () => {
                    if (!window.Swal) {
                        return;
                    }

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
                            toast.style.boxShadow = '0 8px 24px rgba(6, 95, 70, 0.12)';

                            toast.addEventListener('mouseenter', window.Swal.stopTimer);
                            toast.addEventListener('mouseleave', window.Swal.resumeTimer);
                        },
                    });
                };

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', showSuccessToast, { once: true });
                } else {
                    showSuccessToast();
                }
            })();
        </script>
    @endif
</x-layouts::app.sidebar>
