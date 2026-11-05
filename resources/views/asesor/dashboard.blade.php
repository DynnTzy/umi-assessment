<x-layouts::app :title="'Dashboard Asesor'">
    <div class="space-y-6 px-5 py-6 lg:px-8 lg:py-8">
        {{-- PROFILE / WELCOME CARD --}}
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-6 p-6 sm:p-8 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-5">
                    <div class="relative shrink-0">
                        <div class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-2xl bg-slate-100 text-2xl font-bold text-slate-700 ring-1 ring-slate-200 sm:h-28 sm:w-28">
                            @if (auth()->user()->profile_photo_path)
                                <img
                                    src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}"
                                    alt="Foto profil {{ auth()->user()->name }}"
                                    class="h-full w-full object-cover"
                                />
                            @else
                                {{ auth()->user()->initials() }}
                            @endif
                        </div>

                        <span class="absolute -bottom-1 -right-1 h-5 w-5 rounded-full border-4 border-white bg-blue-500"></span>
                    </div>

                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-[#B28D32]">
                            Dr. Umi Assessment
                        </p>

                        <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                            Halo, {{ auth()->user()->name }}
                        </h2>

                        <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-slate-500">
                            <span class="inline-flex items-center gap-2">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.8"
                                    stroke="currentColor"
                                    class="h-4 w-4 text-slate-400"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"
                                    />
                                </svg>

                                {{ auth()->user()->email }}
                            </span>

                            <span class="inline-flex items-center rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">
                                ROLE: ASESOR
                            </span>
                        </div>
                    </div>
                </div>

                <a
                    href="{{ route('assessments.index') }}"
                    class="inline-flex shrink-0 items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                >
                    Lihat Daftar Klinik

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        class="ml-2 h-4 w-4"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m8.25 4.5 7.5 7.5-7.5 7.5"
                        />
                    </svg>
                </a>
            </div>
        </section>

        {{-- ALL DOCUMENTS --}}
        <flux:card class="overflow-hidden p-0">
            {{-- LIST HEADER --}}
            <div class="border-b border-zinc-200 px-5 py-5 sm:px-6 dark:border-zinc-800">
                <div class="flex flex-wrap items-center gap-2">
                    <flux:heading size="lg" class="font-semibold tracking-tight">
                        Daftar Dokumen Bukti
                    </flux:heading>

                    <flux:badge variant="neutral" class="font-semibold">
                        {{ $documents->total() }}
                    </flux:badge>
                </div>

                <flux:subheading class="mt-1">
                    Seluruh dokumen yang diunggah oleh klinik.
                </flux:subheading>

                {{-- SEARCH AND FILTERS --}}
                <form
                    id="document-search-form"
                    method="GET"
                    action="{{ route('dashboard') }}"
                    class="mt-5"
                >
                    <div class="flex flex-col gap-3 xl:flex-row xl:items-center">
                        {{-- SEARCH INPUT --}}
                        <div class="relative w-full xl:max-w-md">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-zinc-400"
                                aria-hidden="true"
                            >
                                <circle cx="11" cy="11" r="7" />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m20 20-4-4"
                                />
                            </svg>

                            <input
                                id="document-search"
                                type="search"
                                name="search"
                                value="{{ $search ?? request('search', '') }}"
                                placeholder="Cari dokumen, kode EP, atau klinik..."
                                aria-label="Cari dokumen"
                                autocomplete="off"
                                class="w-full rounded-lg border border-zinc-200 bg-white py-2.5 pl-9 pr-9 text-sm text-zinc-900 outline-none transition placeholder:text-zinc-400 focus:border-amber-400 focus:ring-2 focus:ring-amber-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white dark:focus:ring-amber-500/10"
                            />

                            {{-- CLEAR SEARCH --}}
                            <button
                                id="clear-document-search"
                                type="button"
                                aria-label="Hapus pencarian"
                                class="{{ ($search ?? request('search', '')) !== '' ? '' : 'hidden' }} absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 transition hover:text-amber-600 dark:hover:text-amber-400"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    class="size-4"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M6 6l12 12M18 6 6 18"
                                    />
                                </svg>
                            </button>
                        </div>

                        {{-- STATUS FILTER --}}
                        <div class="w-full sm:w-auto">
                            <label for="document-status-filter" class="sr-only">
                                Filter status review
                            </label>

                            <select
                                id="document-status-filter"
                                name="status"
                                class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2.5 text-sm text-zinc-700 outline-none transition focus:border-amber-400 focus:ring-2 focus:ring-amber-100 sm:min-w-44 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:focus:ring-amber-500/10"
                            >
                                <option value="" {{ ($status ?? request('status', '')) === '' ? 'selected' : '' }}>
                                    Semua Status
                                </option>
                                <option value="pending" {{ ($status ?? request('status', '')) === 'pending' ? 'selected' : '' }}>
                                    Menunggu Review
                                </option>
                                <option value="approved" {{ ($status ?? request('status', '')) === 'approved' ? 'selected' : '' }}>
                                    Disetujui
                                </option>
                                <option value="revision" {{ ($status ?? request('status', '')) === 'revision' ? 'selected' : '' }}>
                                    Butuh Revisi
                                </option>
                            </select>
                        </div>

                        {{-- INSTRUMENT FILTER --}}
                        <div class="w-full sm:w-auto">
                            <label for="document-instrument-filter" class="sr-only">
                                Filter instrumen
                            </label>

                            <select
                                id="document-instrument-filter"
                                name="instrument"
                                class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2.5 text-sm text-zinc-700 outline-none transition focus:border-amber-400 focus:ring-2 focus:ring-amber-100 sm:min-w-40 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:focus:ring-amber-500/10"
                            >
                                <option value="" {{ ($instrument ?? request('instrument', '')) === '' ? 'selected' : '' }}>
                                    Semua Instrumen
                                </option>
                                <option value="regulasi" {{ ($instrument ?? request('instrument', '')) === 'regulasi' ? 'selected' : '' }}>
                                    Regulasi
                                </option>
                                <option value="dokumen" {{ ($instrument ?? request('instrument', '')) === 'dokumen' ? 'selected' : '' }}>
                                    Dokumen
                                </option>
                            </select>
                        </div>

                        {{-- RESET FILTERS --}}
                        @if (
                            ($search ?? request('search', '')) !== ''
                            || ($status ?? request('status', '')) !== ''
                            || ($instrument ?? request('instrument', '')) !== ''
                        )
                            <a
                                id="reset-document-filters"
                                href="{{ route('dashboard') }}"
                                class="inline-flex shrink-0 items-center justify-center gap-1.5 rounded-lg border border-zinc-200 px-3 py-2.5 text-sm font-medium text-zinc-600 transition hover:border-amber-300 hover:bg-amber-50 hover:text-amber-700 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-amber-500/10"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.8"
                                    stroke="currentColor"
                                    class="size-4"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M6 6l12 12M18 6 6 18"
                                    />
                                </svg>

                                Reset
                            </a>
                        @endif

                        {{-- RESULT COUNT --}}
                        <p
                            id="document-search-count"
                            aria-live="polite"
                            class="text-sm font-medium text-zinc-600 xl:ml-auto dark:text-zinc-400"
                        >
                            Menampilkan
                            {{ $documents->firstItem() ?? 0 }}–{{ $documents->lastItem() ?? 0 }}
                            dari {{ $documents->total() }} dokumen
                        </p>
                    </div>
                </form>
            </div>

            {{-- TABLE --}}
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1150px] text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200 bg-zinc-50/80 dark:border-zinc-800 dark:bg-zinc-900/60">
                            <th
                                scope="col"
                                class="whitespace-nowrap px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wide text-zinc-600 sm:px-6 dark:text-zinc-400"
                            >
                                Kode EP
                            </th>

                            <th
                                scope="col"
                                class="whitespace-nowrap px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wide text-zinc-600 dark:text-zinc-400"
                            >
                                Instrumen
                            </th>

                            <th
                                scope="col"
                                class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wide text-zinc-600 dark:text-zinc-400"
                            >
                                Judul Dokumen
                            </th>

                            <th
                                scope="col"
                                class="whitespace-nowrap px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wide text-zinc-600 dark:text-zinc-400"
                            >
                                Pengunggah
                            </th>

                            <th
                                scope="col"
                                class="whitespace-nowrap px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wide text-zinc-600 dark:text-zinc-400"
                            >
                                Klinik
                            </th>

                            <th
                                scope="col"
                                class="whitespace-nowrap px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wide text-zinc-600 dark:text-zinc-400"
                            >
                                Status Review
                            </th>

                            <th
                                scope="col"
                                class="whitespace-nowrap px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wide text-zinc-600 sm:px-6 dark:text-zinc-400"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse ($documents as $document)
                            @php
                                $item = $document->assessmentItem;
                                $element = $item?->element;
                                $clinic = $item?->assessment?->clinic;
                                $review = $document->latestReview;
                                $latestActivity = $document->activities->first();

                                $reviewStatus = strtolower($review?->status ?? 'pending');

                                $reviewStatusClasses = match ($reviewStatus) {
                                    'approved', 'disetujui' =>
                                        'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400',

                                    'revision', 'revisi', 'rejected', 'ditolak' =>
                                        'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400',

                                    default =>
                                        'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400',
                                };

                                $reviewStatusLabel = match ($reviewStatus) {
                                    'approved', 'disetujui' => 'Disetujui',
                                    'revision', 'revisi', 'rejected', 'ditolak' => 'Butuh Revisi',
                                    default => 'Menunggu Review',
                                };

                                $activityClasses = match ($reviewStatus) {
                                    'approved', 'disetujui' =>
                                        'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/20 dark:text-emerald-300',

                                    'revision', 'revisi', 'rejected', 'ditolak' =>
                                        'border-red-200 bg-red-50 text-red-700 dark:border-red-900/50 dark:bg-red-950/20 dark:text-red-300',

                                    default =>
                                        'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-900/50 dark:bg-amber-950/20 dark:text-amber-300',
                                };

                                $documentInstrument = match ($document->instrument_type) {
                                    'regulasi' => 'Regulasi',
                                    'dokumen' => 'Dokumen',
                                    default => $document->instrument_type
                                        ? ucfirst($document->instrument_type)
                                        : '-',
                                };
                            @endphp

                            <tr class="group align-top transition-colors duration-150 odd:bg-white even:bg-zinc-50/50 hover:bg-amber-50/70 dark:odd:bg-zinc-950 dark:even:bg-zinc-900/40 dark:hover:bg-amber-500/5">
                                {{-- KODE EP --}}
                                <td class="whitespace-nowrap px-5 py-4 sm:px-6">
                                    <span class="font-semibold text-blue-600 dark:text-blue-400">
                                        {{ $element?->code ?? '-' }}
                                    </span>
                                </td>

                                {{-- INSTRUMEN --}}
                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex items-center rounded-sm bg-slate-100 px-2 py-1 text-[11px] font-semibold uppercase text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                        {{ $documentInstrument }}
                                    </span>
                                </td>

                                {{-- JUDUL DOKUMEN DAN AKTIVITAS TERBARU --}}
                                <td class="min-w-[280px] max-w-sm px-5 py-4">
                                    <p class="break-words font-semibold text-zinc-900 dark:text-white">
                                        {{ $document->original_name }}
                                    </p>

                                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $document->created_at?->format('d M Y, H:i') ?? '-' }}

                                        @if ($document->file_size)
                                            <span class="mx-1">·</span>
                                            {{ number_format($document->file_size / 1024, 0) }} KB
                                        @endif
                                    </p>

                                    {{-- LATEST ACTIVITY ONLY --}}
                                    @if ($latestActivity)
                                        @php
                                            $activityType = match ($latestActivity->type) {
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
                                        @endphp

                                        <div class="mt-2 max-w-md rounded border px-2 py-1.5 text-xs leading-relaxed {{ $activityClasses }}">
                                            <p class="font-semibold">
                                                {{ $activityType }}
                                                <span class="font-normal opacity-80">
                                                    · {{ $activityActor }}
                                                </span>
                                            </p>

                                            <p class="mt-1 opacity-75">
                                                {{ $latestActivity->created_at?->format('d M Y, H:i') ?? '-' }}
                                            </p>
                                        </div>
                                    @endif
                                </td>

                                {{-- PENGUNGGAH --}}
                                <td class="whitespace-nowrap px-5 py-4 text-zinc-600 dark:text-zinc-300">
                                    {{ $document->uploader?->name ?? '-' }}
                                </td>

                                {{-- KLINIK --}}
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-zinc-900 dark:text-white">
                                        {{ $clinic?->name ?? 'Klinik tidak ditemukan' }}
                                    </p>
                                </td>

                                {{-- STATUS REVIEW --}}
                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $reviewStatusClasses }}">
                                        {{ $reviewStatusLabel }}
                                    </span>
                                </td>

                                {{-- AKSI --}}
                                <td class="whitespace-nowrap px-5 py-4 text-right sm:px-6">
                                    <flux:button
                                        href="{{ route('assessments.documents.evaluate', $document) }}"
                                        variant="primary"
                                        size="sm"
                                        class="font-semibold"
                                    >
                                        Buka

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            class="ml-1 size-4 shrink-0 transition-transform duration-150 group-hover:translate-x-0.5"
                                            aria-hidden="true"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M9 5l7 7-7 7"
                                            />
                                        </svg>
                                    </flux:button>
                                </td>
                            </tr>
                        @empty
                            {{-- EMPTY STATE --}}
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="mx-auto flex max-w-md flex-col items-center">
                                        <div class="flex size-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20">
                                            <flux:icon name="document-text" class="size-7" />
                                        </div>

                                        <p class="mt-4 font-semibold text-zinc-900 dark:text-white">
                                            Dokumen tidak ditemukan
                                        </p>

                                        <p class="mt-1 text-sm leading-6 text-zinc-500 dark:text-zinc-400">
                                            Tidak ada dokumen yang cocok dengan pencarian atau filter yang dipilih.
                                        </p>

                                        <flux:button
                                            href="{{ route('dashboard') }}"
                                            variant="ghost"
                                            class="mt-4 font-medium"
                                        >
                                            Reset Pencarian dan Filter
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if ($documents->hasPages())
                <div class="border-t border-zinc-200 bg-white px-5 py-4 sm:px-6 dark:border-zinc-800">
                    {{ $documents->onEachSide(1)->links() }}
                </div>
            @endif
        </flux:card>

        {{-- SEARCH AND FILTER INTERACTIONS --}}
        <script>
            (() => {
                function initializeDocumentSearch() {
                    const form = document.getElementById('document-search-form');
                    const input = document.getElementById('document-search');
                    const clearButton = document.getElementById('clear-document-search');
                    const statusFilter = document.getElementById('document-status-filter');
                    const instrumentFilter = document.getElementById('document-instrument-filter');

                    if (
                        !form ||
                        !input ||
                        !clearButton ||
                        !statusFilter ||
                        !instrumentFilter ||
                        form.dataset.initialized === 'true'
                    ) {
                        return;
                    }

                    form.dataset.initialized = 'true';

                    let debounceTimer;
                    let isSearchSubmission = false;
                    let lastSubmittedValue = input.value.trim();

                    const focusKey = 'document-search-restore-focus';

                    function updateClearButton() {
                        clearButton.classList.toggle('hidden', input.value.length === 0);
                    }

                    function submitSearch() {
                        const value = input.value.trim();

                        if (value === lastSubmittedValue) {
                            return;
                        }

                        lastSubmittedValue = value;
                        isSearchSubmission = true;

                        form.requestSubmit();
                    }

                    // Restore focus after submitting a text search.
                    if (sessionStorage.getItem(focusKey) === '1') {
                        sessionStorage.removeItem(focusKey);
                        input.focus();
                        input.setSelectionRange(input.value.length, input.value.length);
                    }

                    // Debounced text search.
                    input.addEventListener('input', function () {
                        updateClearButton();
                        clearTimeout(debounceTimer);

                        debounceTimer = setTimeout(function () {
                            submitSearch();
                        }, 700);
                    });

                    // Clear text search while preserving active filters.
                    clearButton.addEventListener('click', function () {
                        clearTimeout(debounceTimer);

                        if (input.value === '') {
                            return;
                        }

                        input.value = '';
                        updateClearButton();
                        submitSearch();
                    });

                    // Apply filters immediately.
                    statusFilter.addEventListener('change', function () {
                        clearTimeout(debounceTimer);
                        form.requestSubmit();
                    });

                    instrumentFilter.addEventListener('change', function () {
                        clearTimeout(debounceTimer);
                        form.requestSubmit();
                    });

                    form.addEventListener('submit', function () {
                        if (isSearchSubmission) {
                            sessionStorage.setItem(focusKey, '1');
                        }
                    });

                    updateClearButton();
                }

                initializeDocumentSearch();

                document.addEventListener('DOMContentLoaded', initializeDocumentSearch);
                document.addEventListener('livewire:navigated', initializeDocumentSearch);
            })();
        </script>
    </div>
</x-layouts::app>
