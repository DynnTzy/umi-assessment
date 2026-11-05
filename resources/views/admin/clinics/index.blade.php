<x-layouts::app.sidebar :title="'Manajemen Klinik'">
    <flux:main>
        <div
            class="space-y-6"
            x-data="{ showCreateClinicModal: {{ $errors->any() ? 'true' : 'false' }} }"
        >
            {{-- PAGE HEADER --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                        <a
                            href="{{ route('dashboard') }}"
                            class="transition hover:text-zinc-900 dark:hover:text-white"
                        >
                            Dr. Umi Assessment
                        </a>

                        <span>/</span>
                        <span>Administrasi</span>
                        <span>/</span>

                        <span class="font-medium text-zinc-800 dark:text-zinc-200">
                            Manajemen Klinik
                        </span>
                    </div>

                    <div class="mt-3 flex items-center gap-3">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">
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

                        <flux:heading size="xl" class="font-bold tracking-tight">
                            Manajemen Klinik
                        </flux:heading>
                    </div>

                    <flux:subheading class="mt-1">
                        Kelola data klinik yang terdaftar dalam sistem assessment.
                    </flux:subheading>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <flux:button
                        type="button"
                        variant="primary"
                        icon="plus"
                        x-on:click="showCreateClinicModal = true"
                    >
                        Tambah Klinik
                    </flux:button>

                    <flux:button
                        href="{{ route('dashboard') }}"
                        variant="ghost"
                        icon="arrow-left"
                    >
                        Kembali
                    </flux:button>
                </div>
            </div>


            {{-- SUCCESS MESSAGE --}}
            @if (session('success'))
                <flux:callout variant="success" icon="check-circle">
                    {{ session('success') }}
                </flux:callout>
            @endif


            {{-- CLINIC LIST --}}
            <flux:card class="overflow-hidden p-0">

                {{-- LIST HEADER & FILTERS --}}
                <div class="flex flex-col gap-4 border-b border-zinc-200 px-5 py-5 sm:px-6 dark:border-zinc-800">
                    <div class="flex flex-wrap items-center gap-2">
                        <flux:heading size="lg" class="font-semibold tracking-tight">
                            Daftar Klinik
                        </flux:heading>

                        <flux:badge variant="neutral" class="font-semibold">
                            {{ $clinics->total() }}
                        </flux:badge>
                    </div>

                    <form
                        id="clinic-search-form"
                        method="GET"
                        action="{{ route('admin.clinics.index') }}"
                        class="space-y-3"
                    >
                        {{-- SEARCH --}}
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <div class="relative w-full sm:max-w-md">
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
                                    id="clinic-search"
                                    type="search"
                                    name="search"
                                    value="{{ $search }}"
                                    placeholder="Cari nama, kode, atau alamat klinik..."
                                    aria-label="Cari nama, kode, atau alamat klinik"
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
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M6 6l12 12M18 6 6 18"
                                        />
                                    </svg>
                                </button>
                            </div>

                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400 sm:ml-auto">
                                Menampilkan
                                {{ $clinics->firstItem() ?? 0 }}–{{ $clinics->lastItem() ?? 0 }}
                                dari {{ $clinics->total() }} klinik
                            </p>
                        </div>


                        {{-- FILTERS --}}
                        <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap">
                            <select
                                name="status"
                                aria-label="Filter status klinik"
                                class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2.5 text-sm text-zinc-700 outline-none transition focus:border-amber-400 focus:ring-2 focus:ring-amber-100 sm:w-auto dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:focus:ring-amber-500/10"
                            >
                                <option value="">Semua Status</option>

                                <option value="active" @selected($status === 'active')>
                                    Aktif
                                </option>

                                <option value="inactive" @selected($status === 'inactive')>
                                    Nonaktif
                                </option>
                            </select>

                            @if ($search !== '' || $status !== '')
                                <flux:button
                                    href="{{ route('admin.clinics.index') }}"
                                    variant="ghost"
                                    size="sm"
                                    icon="x-mark"
                                >
                                    Reset Filter
                                </flux:button>
                            @endif
                        </div>
                    </form>
                </div>


                {{-- TABLE --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-zinc-200 bg-zinc-50/80 dark:border-zinc-800 dark:bg-zinc-900/60">
                                <th
                                    scope="col"
                                    class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wide text-zinc-600 sm:px-6 dark:text-zinc-400"
                                >
                                    Klinik
                                </th>

                                <th
                                    scope="col"
                                    class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wide text-zinc-600 dark:text-zinc-400"
                                >
                                    Kode Klinik
                                </th>

                                <th
                                    scope="col"
                                    class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wide text-zinc-600 dark:text-zinc-400"
                                >
                                    Status
                                </th>

                                <th
                                    scope="col"
                                    class="px-5 py-3.5 text-center text-xs font-bold uppercase tracking-wide text-zinc-600 dark:text-zinc-400"
                                >
                                    Pengguna
                                </th>

                                <th
                                    scope="col"
                                    class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wide text-zinc-600 sm:px-6 dark:text-zinc-400"
                                >
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @forelse ($clinics as $clinic)
                                <tr class="group transition-colors duration-150 odd:bg-white even:bg-zinc-50/50 hover:bg-amber-50/70 dark:odd:bg-zinc-950 dark:even:bg-zinc-900/40 dark:hover:bg-amber-500/5">

                                    {{-- CLINIC --}}
                                    <td class="px-5 py-4 sm:px-6">
                                        <div class="flex min-w-0 items-center gap-3">
                                            {{-- CLINIC ICON --}}
                                            <div class="flex size-14 shrink-0 items-center justify-center rounded-2xl border border-amber-200/70 bg-amber-50/70 text-amber-700">
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.8"
                                                    stroke="currentColor"
                                                    class="size-7"
                                                    aria-hidden="true"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M3 21h18M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16M9 7h1m4 0h1M9 11h1m4 0h1M9 15h1m4 0h1"
                                                    />
                                                </svg>
                                            </div>

                                            <div class="min-w-0">
                                                <p class="font-semibold text-zinc-900 dark:text-white">
                                                    {{ $clinic->name }}
                                                </p>

                                                @if ($clinic->address)
                                                    <p
                                                        class="mt-1 max-w-sm truncate text-xs text-zinc-500 dark:text-zinc-400"
                                                        title="{{ $clinic->address }}"
                                                    >
                                                        {{ $clinic->address }}
                                                    </p>
                                                @else
                                                    <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">
                                                        Alamat belum tersedia
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>


                                    {{-- CLINIC CODE --}}
                                    <td class="px-5 py-4">
                                        <flux:badge variant="neutral">
                                            {{ $clinic->code }}
                                        </flux:badge>
                                    </td>


                                    {{-- STATUS --}}
                                    <td class="px-5 py-4">
                                        @if ($clinic->is_active)
                                            <flux:badge color="green" icon="check-circle">
                                                Aktif
                                            </flux:badge>
                                        @else
                                            <flux:badge color="red" icon="x-circle">
                                                Nonaktif
                                            </flux:badge>
                                        @endif
                                    </td>


                                    {{-- USER COUNT --}}
                                    <td class="px-5 py-4 text-center">
                                        <span class="font-semibold tabular-nums text-zinc-800 dark:text-zinc-200">
                                            {{ $clinic->users_count }}
                                        </span>
                                    </td>


                                    {{-- ACTION --}}
                                    <td class="px-5 py-4 text-right sm:px-6">
                                        <flux:button
                                            href="{{ route('admin.clinics.show', $clinic) }}"
                                            variant="primary"
                                            size="sm"
                                            class="font-semibold"
                                        >
                                            Detail

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
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="mx-auto flex max-w-md flex-col items-center">
                                            <div class="flex size-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20">
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

                                            <p class="mt-4 font-semibold text-zinc-900 dark:text-white">
                                                @if ($search !== '' || $status !== '')
                                                    Klinik tidak ditemukan
                                                @else
                                                    Belum ada klinik
                                                @endif
                                            </p>

                                            <p class="mt-1 text-sm leading-6 text-zinc-500 dark:text-zinc-400">
                                                @if ($search !== '' || $status !== '')
                                                    Tidak ada klinik yang sesuai dengan pencarian atau filter yang dipilih.
                                                @else
                                                    Belum terdapat klinik yang terdaftar di dalam sistem.
                                                @endif
                                            </p>

                                            @if ($search !== '' || $status !== '')
                                                <flux:button
                                                    href="{{ route('admin.clinics.index') }}"
                                                    variant="ghost"
                                                    class="mt-4 font-medium"
                                                >
                                                    Reset Filter
                                                </flux:button>
                                            @else
                                                <flux:button
                                                    type="button"
                                                    variant="primary"
                                                    size="sm"
                                                    class="mt-4"
                                                    icon="plus"
                                                    x-on:click="showCreateClinicModal = true"
                                                >
                                                    Tambah Klinik
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
                @if ($clinics->hasPages())
                    <div class="flex flex-col gap-3 border-t border-zinc-200 bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6 dark:border-zinc-800 dark:bg-zinc-950">
                        <p class="text-xs text-zinc-600 dark:text-zinc-400">
                            Menampilkan

                            <span class="font-semibold text-zinc-800 dark:text-zinc-200">
                                {{ $clinics->firstItem() ?? 0 }}–{{ $clinics->lastItem() ?? 0 }}
                            </span>

                            dari

                            <span class="font-semibold text-zinc-800 dark:text-zinc-200">
                                {{ $clinics->total() }}
                            </span>

                            klinik
                        </p>

                        <div>
                            {{ $clinics->onEachSide(1)->links() }}
                        </div>
                    </div>
                @endif

            </flux:card>


            {{-- =========================================================
                CREATE CLINIC MODAL
            ========================================================== --}}
            <div
                x-cloak
                x-show="showCreateClinicModal"
                x-transition.opacity.duration.200ms
                x-on:keydown.escape.window="showCreateClinicModal = false"
                class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-zinc-950/40 p-4 backdrop-blur-sm"
                style="display: none;"
            >
                {{-- MODAL CONTENT --}}
                <div
                    x-show="showCreateClinicModal"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="translate-y-4 scale-95 opacity-0"
                    x-transition:enter-end="translate-y-0 scale-100 opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="translate-y-0 scale-100 opacity-100"
                    x-transition:leave-end="translate-y-4 scale-95 opacity-0"
                    x-on:click.outside="showCreateClinicModal = false"
                    class="my-auto w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-zinc-900"
                >

                    {{-- MODAL HEADER --}}
                    <div class="flex items-start justify-between border-b border-zinc-200 px-6 py-5 dark:border-zinc-800">
                        <div>
                            <flux:heading size="lg">
                                Tambah Klinik
                            </flux:heading>

                            <flux:subheading class="mt-1">
                                Lengkapi informasi klinik yang akan didaftarkan.
                            </flux:subheading>
                        </div>

                        <button
                            type="button"
                            x-on:click="showCreateClinicModal = false"
                            class="rounded-lg p-2 text-zinc-400 transition hover:bg-zinc-100 hover:text-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-zinc-200"
                            aria-label="Tutup modal"
                        >
                            <flux:icon name="x-mark" class="size-5" />
                        </button>
                    </div>


                    {{-- MODAL FORM --}}
                    <form
                        id="create-clinic-form"
                        method="POST"
                        action="{{ route('admin.clinics.store') }}"
                    >
                        @csrf

                        <div class="max-h-[65vh] space-y-5 overflow-y-auto px-6 py-6">

                            {{-- KODE KLINIK --}}
                            <flux:field>
                                <flux:label for="code">
                                    Kode Klinik
                                </flux:label>

                                <flux:input
                                    id="code"
                                    name="code"
                                    type="text"
                                    value="{{ old('code') }}"
                                    placeholder="Contoh: CMG"
                                    maxlength="255"
                                    required
                                />

                                <flux:description>
                                    Kode unik untuk mengidentifikasi klinik.
                                </flux:description>

                                <flux:error name="code" />
                            </flux:field>


                            {{-- NAMA KLINIK --}}
                            <flux:field>
                                <flux:label for="name">
                                    Nama Klinik
                                </flux:label>

                                <flux:input
                                    id="name"
                                    name="name"
                                    type="text"
                                    value="{{ old('name') }}"
                                    placeholder="Contoh: Klinik Cipta Medika"
                                    maxlength="255"
                                    required
                                />

                                <flux:error name="name" />
                            </flux:field>


                            {{-- ALAMAT --}}
                            <flux:field>
                                <flux:label for="address">
                                    Alamat
                                </flux:label>

                                <flux:textarea
                                    id="address"
                                    name="address"
                                    rows="3"
                                    placeholder="Alamat lengkap klinik"
                                >{{ old('address') }}</flux:textarea>

                                <flux:error name="address" />
                            </flux:field>


                            {{-- TELEPON & EMAIL --}}
                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <flux:field>
                                    <flux:label for="phone">
                                        Nomor Telepon
                                    </flux:label>

                                    <flux:input
                                        id="phone"
                                        name="phone"
                                        type="text"
                                        value="{{ old('phone') }}"
                                        maxlength="255"
                                        placeholder="Contoh: 0311234567"
                                    />

                                    <flux:error name="phone" />
                                </flux:field>

                                <flux:field>
                                    <flux:label for="email">
                                        Email Klinik
                                    </flux:label>

                                    <flux:input
                                        id="email"
                                        name="email"
                                        type="email"
                                        value="{{ old('email') }}"
                                        maxlength="255"
                                        placeholder="contoh@klinik.com"
                                    />

                                    <flux:error name="email" />
                                </flux:field>
                            </div>

                        </div>


                        {{-- MODAL FOOTER --}}
                        <div class="flex flex-col-reverse gap-3 border-t border-zinc-200 px-6 py-4 sm:flex-row sm:justify-end dark:border-zinc-800">
                            <flux:button
                                type="button"
                                variant="ghost"
                                x-on:click="showCreateClinicModal = false"
                            >
                                Batal
                            </flux:button>

                            <flux:button
                                type="submit"
                                variant="primary"
                                icon="check"
                            >
                                Simpan Klinik
                            </flux:button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </flux:main>


    {{-- LIVE SEARCH --}}
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

            // STATUS FILTER
            const filters = form.querySelectorAll('select[name="status"]');

            filters.forEach(function (filter) {
                filter.addEventListener('change', function () {
                    form.requestSubmit();
                });
            });

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

                input.setSelectionRange(
                    input.value.length,
                    input.value.length
                );
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
        });
    </script>


    {{-- SWEETALERT2: KONFIRMASI TAMBAH KLINIK --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('create-clinic-form');

            if (!form) {
                return;
            }

            form.addEventListener('submit', async function (event) {
                event.preventDefault();

                const result = await Swal.fire({
                    title: 'Tambahkan Klinik?',
                    text: 'Pastikan informasi klinik yang kamu masukkan sudah benar.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Tambahkan',
                    cancelButtonText: 'Periksa Kembali',
                    reverseButtons: true,
                    confirmButtonColor: '#b8954a',
                });

                if (result.isConfirmed) {
                    HTMLFormElement.prototype.submit.call(form);
                }
            });
        });


        {{-- SUCCESS NOTIFICATION --}}
        @if (session('success'))
            Swal.fire({
                title: 'Berhasil!',
                text: @json(session('success')),
                icon: 'success',
                confirmButtonText: 'Oke',
                confirmButtonColor: '#b8954a',
            });
        @endif
    </script>

</x-layouts::app.sidebar>
