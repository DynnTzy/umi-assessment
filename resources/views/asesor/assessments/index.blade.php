<x-layouts::app.sidebar :title="'Daftar Klinik'">

    <flux:main>
        <div class="space-y-6">

            {{-- PAGE HEADER --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                        <a href="{{ route('dashboard') }}"
                            class="transition hover:text-zinc-900 dark:hover:text-white">
                            Dr. Umi Assessment
                        </a>

                        <span>/</span>

                        <span class="font-medium text-zinc-800 dark:text-zinc-200">
                            Daftar Klinik
                        </span>
                    </div>

                    <div class="mt-3 flex items-center gap-3">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">
                            <flux:icon name="check-circle" class="size-5" />
                        </div>

                        <flux:heading size="xl" class="font-bold tracking-tight">
                            Daftar Klinik
                        </flux:heading>
                    </div>

                    <flux:subheading class="mt-1">
                        Pilih klinik untuk melihat assessment dan melakukan review dokumen.
                    </flux:subheading>
                </div>

                <flux:button
                    href="{{ route('dashboard') }}"
                    variant="ghost"
                    icon="arrow-left"
                >
                    Kembali
                </flux:button>
            </div>

            {{-- CLINIC LIST --}}
            <flux:card class="overflow-hidden p-0">

                {{-- LIST HEADER --}}
                <div class="flex flex-col gap-4 border-b border-zinc-200 px-5 py-5 sm:px-6 dark:border-zinc-800">

                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <flux:heading size="lg" class="font-semibold tracking-tight">
                                Klinik yang Ditugaskan
                            </flux:heading>

                            <flux:badge variant="neutral" class="font-semibold">
                                {{ $assessments->total() }}
                            </flux:badge>
                        </div>
                    </div>

                    {{-- LIVE SEARCH --}}
                    <form
                        id="clinic-search-form"
                        method="GET"
                        action="{{ route('assessments.index') }}"
                        class="flex flex-col gap-3 sm:flex-row sm:items-center"
                    >
                        <div class="relative w-full sm:max-w-md">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-zinc-400"
                            >
                                <circle cx="11" cy="11" r="7" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="m20 20-4-4" />
                            </svg>

                            <input
                                id="clinic-search"
                                type="search"
                                name="search"
                                value="{{ $search }}"
                                placeholder="Cari nama klinik..."
                                aria-label="Cari nama klinik"
                                autocomplete="off"
                                class="w-full rounded-lg border border-zinc-200 bg-white py-2.5 pl-9 pr-9 text-sm text-zinc-900 outline-none transition placeholder:text-zinc-400 focus:border-amber-400 focus:ring-2 focus:ring-amber-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white dark:focus:ring-amber-500/10"
                            />

                            {{-- CLEAR SEARCH --}}
                            <button
                                id="clear-clinic-search"
                                type="button"
                                aria-label="Hapus pencarian"
                                class="{{ $search !== '' ? '' : 'hidden' }} absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 transition hover:text-amber-600 dark:hover:text-amber-400"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    class="size-4"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" />
                                </svg>
                            </button>
                        </div>

                        <p
                            id="clinic-search-count"
                            aria-live="polite"
                            class="text-sm font-medium text-zinc-600 dark:text-zinc-400 sm:ml-auto"
                        >
                            Menampilkan
                            {{ $assessments->firstItem() ?? 0 }}–{{ $assessments->lastItem() ?? 0 }}
                            dari {{ $assessments->total() }} klinik
                        </p>
                    </form>
                </div>

                {{-- TABLE --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">

                        <thead>
                            <tr class="border-b border-zinc-200 bg-zinc-50/80 dark:border-zinc-800 dark:bg-zinc-900/60">

                                <th scope="col" class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wide text-zinc-600 sm:px-6 dark:text-zinc-400">
                                    Klinik
                                </th>

                                <th scope="col" class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wide text-zinc-600 dark:text-zinc-400">
                                    Alamat
                                </th>

                                <th scope="col" class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wide text-zinc-600 dark:text-zinc-400">
                                    Tanggal Dibuat
                                </th>

                                <th scope="col" class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wide text-zinc-600 sm:px-6 dark:text-zinc-400">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">

                            @forelse ($assessments as $assessment)
                                <tr class="group transition-colors duration-150 odd:bg-white even:bg-zinc-50/50 hover:bg-amber-50/70 dark:odd:bg-zinc-950 dark:even:bg-zinc-900/40 dark:hover:bg-amber-500/5">

                                    {{-- CLINIC --}}
<td class="px-5 py-4 sm:px-6">
    <div class="flex min-w-0 items-center gap-3">

        {{-- CLINIC ICON --}}
        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-100 transition-colors group-hover:bg-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20 dark:group-hover:bg-amber-500/15">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.8"
                stroke="currentColor"
                class="size-5"
                aria-hidden="true"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M3 21h18M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16M9 7h1m4 0h1M9 11h1m4 0h1M9 15h1m4 0h1"
                />
            </svg>
        </div>

        {{-- CLINIC NAME --}}
        <div class="min-w-0">
            <p class="font-semibold text-zinc-900 transition-colors group-hover:text-amber-800 dark:text-white dark:group-hover:text-amber-300">
                {{ $assessment->clinic?->name ?? 'Klinik tidak ditemukan' }}
            </p>

            @if ($assessment->clinic && ! $assessment->clinic->is_active)
                <div class="mt-2">
                    <flux:badge color="red" icon="x-circle">
                        Klinik Nonaktif
                    </flux:badge>
                </div>
            @endif
        </div>

    </div>
</td>
                                    {{-- CLINIC ADDRESS --}}
                                    <td class="px-5 py-4">
                                        <div class="flex items-start gap-2 text-zinc-600 dark:text-zinc-300">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.7"
                                                stroke="currentColor"
                                                class="mt-0.5 size-4 shrink-0 text-zinc-400"
                                                aria-hidden="true"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-5.2 7-12a7 7 0 1 0-14 0c0 6.8 7 12 7 12Z" />
                                                <circle cx="12" cy="9" r="2.5" />
                                            </svg>

                                            <span>
                                                {{ $assessment->clinic?->address ?: '-' }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- CREATED AT --}}
                                    <td class="px-5 py-4">
                                        <p class="font-semibold text-zinc-800 dark:text-zinc-200">
                                            {{ $assessment->created_at?->format('d M Y') ?? '-' }}
                                        </p>

                                        @if ($assessment->created_at)
                                            <p class="mt-1 text-xs font-medium text-zinc-500 dark:text-zinc-500">
                                                {{ $assessment->created_at->format('H:i') }}
                                            </p>
                                        @endif
                                    </td>

                                    {{-- ACTION --}}
                                    <td class="px-5 py-4 text-right sm:px-6">
                                        <flux:button
                                            href="{{ route('assessments.show', $assessment) }}"
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
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </flux:button>
                                    </td>

                                </tr>

                            @empty

                                {{-- EMPTY STATE --}}
                                <tr>
                                    <td colspan="4" class="px-6 py-16 text-center">
                                        <div class="mx-auto flex max-w-md flex-col items-center">

                                            <div class="flex size-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20">
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="1.8"
                                                    class="size-7"
                                                >
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16M9 7h1m4 0h1M9 11h1m4 0h1M9 15h1m4 0h1" />
                                                </svg>
                                            </div>

                                            <p class="mt-4 font-semibold text-zinc-900 dark:text-white">
                                                @if ($search !== '')
                                                    Klinik tidak ditemukan
                                                @else
                                                    Belum ada klinik yang ditugaskan
                                                @endif
                                            </p>

                                            <p class="mt-1 text-sm leading-6 text-zinc-500 dark:text-zinc-400">
                                                @if ($search !== '')
                                                    Tidak ada klinik dengan nama
                                                    "{{ $search }}" yang cocok dengan pencarian Anda.
                                                @else
                                                    Klinik akan muncul di halaman ini setelah assessment ditugaskan kepada Anda.
                                                @endif
                                            </p>

                                            @if ($search !== '')
                                                <flux:button
                                                    href="{{ route('assessments.index') }}"
                                                    variant="ghost"
                                                    class="mt-4 font-medium"
                                                >
                                                    Reset Pencarian
                                                </flux:button>
                                            @else
                                                <flux:button
                                                    href="{{ route('dashboard') }}"
                                                    variant="ghost"
                                                    class="mt-4 font-medium"
                                                >
                                                    Kembali ke Dashboard
                                                </flux:button>
                                            @endif

                                        </div>
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                @if ($assessments->hasPages())
                    <div class="flex flex-col gap-3 border-t border-zinc-200 bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6 dark:border-zinc-800 dark:bg-zinc-950">

                        <p class="text-xs text-zinc-600 dark:text-zinc-400">
                            Menampilkan
                            <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $assessments->firstItem() ?? 0 }}–{{ $assessments->lastItem() ?? 0 }}</span>
                            dari
                            <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $assessments->total() }}</span>
                            klinik
                        </p>

                        <div>
                            {{ $assessments->onEachSide(1)->links() }}
                        </div>

                    </div>
                @endif

            </flux:card>

        </div>
    </flux:main>

    {{-- LIVE SEARCH SCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('clinic-search-form');
            const input = document.getElementById('clinic-search');
            const clearButton = document.getElementById('clear-clinic-search');

            if (!form || !input || !clearButton) {
                return;
            }

            let debounceTimer;
            let lastSubmittedValue = input.value.trim();
            const focusKey = 'clinic-search-restore-focus';

            function updateClearButton() {
                clearButton.classList.toggle('hidden', input.value.length === 0);
            }

            function submitSearch() {
                const value = input.value.trim();

                if (value === lastSubmittedValue) {
                    return;
                }

                lastSubmittedValue = value;
                sessionStorage.setItem(focusKey, '1');
                form.requestSubmit();
            }

            if (sessionStorage.getItem(focusKey) === '1') {
                sessionStorage.removeItem(focusKey);
                input.focus();
                input.setSelectionRange(input.value.length, input.value.length);
            }

            input.addEventListener('input', function () {
                updateClearButton();
                clearTimeout(debounceTimer);

                debounceTimer = setTimeout(function () {
                    submitSearch();
                }, 700);
            });

            clearButton.addEventListener('click', function () {
                clearTimeout(debounceTimer);
                input.value = '';
                updateClearButton();
                submitSearch();
            });

            form.addEventListener('submit', function () {
                sessionStorage.setItem(focusKey, '1');
            });
        });
    </script>

</x-layouts::app.sidebar>