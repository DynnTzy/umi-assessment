<x-layouts::app.sidebar :title="'Assessment'">

    @php
        /*
         * Progress berdasarkan elemen yang memiliki dokumen.
         * Elemen tanpa dokumen tidak dihitung sebagai pending.
         */
        $itemsWithDocuments = $assessment->items
            ->filter(fn ($item) => $item->documents->isNotEmpty());

        $requiredItems = $itemsWithDocuments->count();

        $completedItems = $itemsWithDocuments
            ->filter(function ($item) {
                return $item->documents->every(function ($document) {
                    return $document->reviews
                        ->sortByDesc('created_at')
                        ->first()?->status === 'approved';
                });
            })
            ->count();

        $pendingItems = $itemsWithDocuments
            ->filter(function ($item) {
                $latestStatuses = $item->documents
                    ->map(fn ($document) => $document->reviews
                        ->sortByDesc('created_at')
                        ->first()?->status);

                return $latestStatuses->contains('pending')
                    || $latestStatuses->contains(null);
            })
            ->count();

        $revisionItems = $itemsWithDocuments
            ->filter(function ($item) {
                return $item->documents->contains(function ($document) {
                    return $document->reviews
                        ->sortByDesc('created_at')
                        ->first()?->status === 'revision';
                });
            })
            ->count();
    @endphp

    <flux:main>
        <div class="space-y-6">

            {{-- PAGE HEADER --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    {{-- Breadcrumb --}}
                    <div class="flex items-center gap-2 text-sm text-zinc-500">
                        <a
                            href="{{ route('assessments.index') }}"
                            class="transition hover:text-zinc-900"
                        >
                            Assessments
                        </a>

                        <span>/</span>

                        <span class="text-zinc-700">
                            Detail
                        </span>
                    </div>

                    {{-- Title --}}
                    <flux:heading size="xl" class="mt-2">
                        Klinik {{ $assessment->clinic?->name ?? '-' }}
                    </flux:heading>

                    <flux:subheading class="mt-1">
                        Kelola dan lakukan penilaian dokumen akreditasi klinik.
                    </flux:subheading>
                </div>

                {{-- Tombol Kembali --}}
                <flux:button
                    href="{{ route('assessments.index') }}"
                    variant="ghost"
                    icon="arrow-left"
                >
                    Kembali
                </flux:button>
            </div>

            {{-- PERINGATAN KLINIK NONAKTIF --}}
            @if ($assessment->clinic && ! $assessment->clinic->is_active)
                <flux:callout
                    variant="warning"
                    icon="exclamation-triangle"
                    heading="Klinik Nonaktif"
                >
                    Klinik {{ $assessment->clinic->name }} sedang nonaktif.
                    Assessment yang sudah berjalan tetap dapat dilanjutkan dan diselesaikan.
                    Klinik ini tidak dapat memulai assessment baru sampai statusnya
                    diaktifkan kembali.
                </flux:callout>
            @endif

            {{-- ASSESSMENT SUMMARY --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                {{-- Clinic --}}
                <flux:card>
                    <div class="flex items-start gap-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.8"
                                stroke="currentColor"
                                class="h-5 w-5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M3 21h18M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16M9 7h1m4 0h1M9 11h1m4 0h1M9 15h1m4 0h1"
                                />
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                Klinik
                            </p>

                            <p class="mt-1 truncate font-semibold text-zinc-900 dark:text-white">
                                {{ $assessment->clinic?->name ?? '-' }}
                            </p>
                        </div>
                    </div>
                </flux:card>

                {{-- Asesor --}}
                <flux:card>
                    <div class="flex items-start gap-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.8"
                                stroke="currentColor"
                                class="h-5 w-5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"
                                />
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                Asesor
                            </p>

                            <p class="mt-1 truncate font-semibold text-zinc-900 dark:text-white">
                                {{ $assessment->asesor?->name ?? 'Belum ditentukan' }}
                            </p>
                        </div>
                    </div>
                </flux:card>

            </div>

            {{-- PROGRESS ASSESSMENT --}}
            <flux:card>
                <div class="space-y-4">
                    <div>
                        <flux:heading size="lg">
                            Progress Assessment
                        </flux:heading>

                        <p class="mt-1 text-xs text-zinc-500">
                            {{ $completedItems }} dari {{ $requiredItems }} elemen dengan dokumen disetujui
                        </p>
                    </div>

                    {{-- Progress Summary --}}
                    <div class="flex flex-wrap items-center gap-4 text-sm">

                        {{-- Selesai --}}
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>

                            <span class="text-zinc-600 dark:text-zinc-300">
                                Selesai: {{ $completedItems }}
                            </span>
                        </div>

                        {{-- Menunggu Review --}}
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>

                            <span class="text-zinc-600 dark:text-zinc-300">
                                Menunggu Review: {{ $pendingItems }}
                            </span>
                        </div>

                        {{-- Butuh Revisi --}}
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-red-500"></span>

                            <span class="text-zinc-600 dark:text-zinc-300">
                                Butuh Revisi: {{ $revisionItems }}
                            </span>
                        </div>

                        {{-- Elemen dengan Dokumen --}}
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-zinc-300"></span>

                            <span class="text-zinc-600 dark:text-zinc-300">
                                Elemen dengan Dokumen: {{ $requiredItems }}
                            </span>
                        </div>

                    </div>
                </div>
            </flux:card>

            {{-- ASSESSMENT ITEMS --}}
            <div class="space-y-4">

                {{-- Section Header --}}
                <div>
                    <flux:heading size="lg">
                        Elemen Penilaian
                    </flux:heading>

                    <flux:subheading class="mt-1">
                        Pilih elemen untuk melihat dokumen dan melakukan penilaian.
                    </flux:subheading>
                </div>

                {{-- DAFTAR BAB --}}
                <div class="space-y-3">

                    @forelse ($chapters as $chapterId => $items)

                        @php
                            $chapterNumber = $loop->iteration;
                            $chapter = $items->first()->element->chapter;
                        @endphp

                        {{-- CHAPTER ACCORDION --}}
                        <details
                            class="group overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900"
                        >

                            {{-- Chapter Header --}}
                            <summary
                                class="flex cursor-pointer list-none items-center justify-between px-6 py-5 transition hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                            >
                                <div class="flex min-w-0 items-center gap-4">

                                    {{-- Nomor Bab --}}
                                    <div class="flex h-9 w-auto shrink-0 items-center justify-center rounded-lg bg-[var(--color-brand-gold)] px-3 text-sm font-semibold text-white">
                                        Bab {{ $chapterNumber }}
                                    </div>

                                    {{-- Nama Bab --}}
                                    <div class="min-w-0">
                                        <p class="font-semibold text-zinc-900 dark:text-white">
                                            {{ $chapter->name }}
                                        </p>
                                    </div>

                                </div>

                                {{-- Chevron --}}
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="h-5 w-5 shrink-0 text-zinc-400 transition-transform duration-200 group-open:rotate-180"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="m19.5 8.25-7.5 7.5-7.5-7.5"
                                    />
                                </svg>
                            </summary>

                            {{-- CHAPTER ITEMS --}}
                            <div class="border-t border-zinc-200 dark:border-zinc-700">

                                @foreach ($items->sortBy('element.sort_order') as $item)

                                    @php
                                        $hasDocuments = $item->documents->isNotEmpty();

                                        $latestStatuses = $item->documents
                                            ->map(fn ($document) => $document->reviews
                                                ->sortByDesc('created_at')
                                                ->first()?->status);

                                        if (! $hasDocuments) {
                                            $statusLabel = null;
                                            $statusClasses = '';
                                        } elseif ($latestStatuses->contains('revision')) {
                                            $statusLabel = 'Butuh Revisi';
                                            $statusClasses = 'border-red-200 bg-red-50 text-red-700';
                                        } elseif ($latestStatuses->contains('pending') || $latestStatuses->contains(null)) {
                                            $statusLabel = 'Menunggu Review';
                                            $statusClasses = 'border-amber-200 bg-amber-50 text-amber-700';
                                        } elseif ($latestStatuses->every(fn ($status) => $status === 'approved')) {
                                            $statusLabel = 'Disetujui';
                                            $statusClasses = 'border-emerald-200 bg-emerald-50 text-emerald-700';
                                        } else {
                                            $statusLabel = null;
                                            $statusClasses = '';
                                        }

                                        $documentCount = $item->documents->count();
                                    @endphp

                                    {{-- Item --}}
                                    <div class="flex items-center justify-between gap-4 border-b border-zinc-100 px-6 py-4 transition-colors last:border-b-0 odd:bg-white even:bg-zinc-100/80 hover:bg-amber-50/50 dark:border-zinc-800 dark:odd:bg-zinc-900 dark:even:bg-zinc-800/50 dark:hover:bg-zinc-800">

                                        {{-- Item Information --}}
                                        <div class="flex min-w-0 items-center gap-4">

                                            {{-- Nomor Item --}}
                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-zinc-100 text-sm text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                                                {{ $loop->iteration }}
                                            </div>

                                            {{-- Detail Item --}}
                                            <div class="min-w-0">
                                                <p class="font-medium text-zinc-900 dark:text-white">
                                                    {{ $item->element->code }}
                                                </p>

                                                <p class="mt-0.5 text-sm {{ $documentCount > 0 ? 'font-medium text-emerald-700 dark:text-emerald-400' : 'text-zinc-500' }}">
                                                    @if ($documentCount === 0)
                                                        Belum ada dokumen
                                                    @elseif ($documentCount === 1)
                                                        1 dokumen diunggah
                                                    @else
                                                        {{ $documentCount }} dokumen diunggah
                                                    @endif
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Status + Action --}}
                                        <div class="flex shrink-0 items-center gap-3">

                                            {{-- Status --}}
                                            @if ($statusLabel)
                                                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-medium {{ $statusClasses }}">
                                                    {{ $statusLabel }}
                                                </span>
                                            @endif

                                            {{-- Buka Detail --}}
                                            <flux:button
                                                href="{{ route('assessments.items.show', [
                                                    'assessment' => $assessment,
                                                    'item' => $item,
                                                ]) }}"
                                                variant="primary"
                                                size="sm"
                                            >
                                                Buka

                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="1.8"
                                                    class="ml-1 h-4 w-4 shrink-0"
                                                    aria-hidden="true"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M9 5l7 7-7 7"
                                                    />
                                                </svg>
                                            </flux:button>

                                        </div>
                                    </div>

                                @endforeach

                            </div>
                        </details>

                    @empty

                        {{-- EMPTY STATE --}}
                        <flux:card>
                            <div class="py-10 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-zinc-100 text-zinc-400 dark:bg-zinc-800">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.8"
                                        stroke="currentColor"
                                        class="h-6 w-6"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9 12h6m-6 4h6M9 8h6m3 12H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h8l4 4v10a2 2 0 0 1-2 2Z"
                                        />
                                    </svg>
                                </div>

                                <p class="mt-4 font-medium text-zinc-900 dark:text-white">
                                    Belum ada item assessment
                                </p>

                                <p class="mt-1 text-sm text-zinc-500">
                                    Assessment ini belum memiliki elemen penilaian.
                                </p>
                            </div>
                        </flux:card>

                    @endforelse

                </div>
            </div>

        </div>
    </flux:main>

</x-layouts::app.sidebar>
