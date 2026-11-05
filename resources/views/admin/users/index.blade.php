<x-layouts::app.sidebar :title="'Manajemen User'">

    <flux:main>
        <div class="space-y-6">

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
                            Manajemen User
                        </span>
                    </div>

                    <div class="mt-3 flex items-center gap-3">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">
                            <flux:icon name="users" class="size-5" />
                        </div>

                        <flux:heading size="xl" class="font-bold tracking-tight">
                            Manajemen User
                        </flux:heading>
                    </div>

                    <flux:subheading class="mt-1">
                        Kelola pengguna dan hubungan pengguna dengan klinik.
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

            {{-- USER LIST --}}
            <flux:card class="overflow-hidden p-0">

                {{-- LIST HEADER & FILTERS --}}
                <div class="flex flex-col gap-4 border-b border-zinc-200 px-5 py-5 sm:px-6 dark:border-zinc-800">

                    {{-- LIST TITLE --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <flux:heading size="lg" class="font-semibold tracking-tight">
                            Daftar User
                        </flux:heading>

                        <flux:badge variant="neutral" class="font-semibold">
                            {{ $users->total() }}
                        </flux:badge>
                    </div>

                    {{-- SEARCH & FILTER FORM --}}
                    <form
                        id="user-search-form"
                        method="GET"
                        action="{{ route('admin.users.index') }}"
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
                                >
                                    <circle cx="11" cy="11" r="7" />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="m20 20-4-4"
                                    />
                                </svg>

                                <input
                                    id="user-search"
                                    type="search"
                                    name="search"
                                    value="{{ $search }}"
                                    placeholder="Cari nama atau email user..."
                                    aria-label="Cari nama atau email user"
                                    autocomplete="off"
                                    class="w-full rounded-lg border border-zinc-200 bg-white py-2.5 pl-9 pr-9 text-sm text-zinc-900 outline-none transition placeholder:text-zinc-400 focus:border-amber-400 focus:ring-2 focus:ring-amber-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white dark:focus:ring-amber-500/10"
                                />

                                <button
                                    id="clear-user-search"
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
                                {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }}
                                dari {{ $users->total() }} user
                            </p>
                        </div>

                        {{-- FILTERS --}}
                        <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap">

                            {{-- ROLE FILTER --}}
                            <select
                                name="role"
                                aria-label="Filter role"
                                class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2.5 text-sm text-zinc-700 outline-none transition focus:border-amber-400 focus:ring-2 focus:ring-amber-100 sm:w-auto dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:focus:ring-amber-500/10"
                            >
                                <option value="">Semua Role</option>
                                <option value="admin" @selected($role === 'admin')>
                                    Admin
                                </option>
                                <option value="asesor" @selected($role === 'asesor')>
                                    Asesor
                                </option>
                                <option value="klinik" @selected($role === 'klinik')>
                                    Pengguna Klinik
                                </option>
                            </select>

                            {{-- CLINIC FILTER --}}
                            <select
                                name="clinic_id"
                                aria-label="Filter klinik"
                                class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2.5 text-sm text-zinc-700 outline-none transition focus:border-amber-400 focus:ring-2 focus:ring-amber-100 sm:w-auto dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:focus:ring-amber-500/10"
                            >
                                <option value="">Semua Klinik</option>

                                @foreach ($clinics as $clinic)
                                    <option
                                        value="{{ $clinic->id }}"
                                        @selected((string) $clinicId === (string) $clinic->id)
                                    >
                                        {{ $clinic->name }}{{ ! $clinic->is_active ? ' (Nonaktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- EMAIL VERIFICATION FILTER --}}
                            <select
                                name="verification"
                                aria-label="Filter verifikasi email"
                                class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2.5 text-sm text-zinc-700 outline-none transition focus:border-amber-400 focus:ring-2 focus:ring-amber-100 sm:w-auto dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:focus:ring-amber-500/10"
                            >
                                <option value="">Semua Status Email</option>
                                <option value="verified" @selected($verification === 'verified')>
                                    Terverifikasi
                                </option>
                                <option value="unverified" @selected($verification === 'unverified')>
                                    Belum Terverifikasi
                                </option>
                            </select>

                            {{-- RESET FILTER --}}
                            @if ($search !== '' || $role !== '' || $clinicId !== '' || $verification !== '')
                                <flux:button
                                    href="{{ route('admin.users.index') }}"
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

                {{-- USER TABLE --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">

                        {{-- TABLE HEADER --}}
                        <thead>
                            <tr class="border-b border-zinc-200 bg-zinc-50/80 dark:border-zinc-800 dark:bg-zinc-900/60">
                                <th
                                    scope="col"
                                    class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wide text-zinc-600 sm:px-6 dark:text-zinc-400"
                                >
                                    Pengguna
                                </th>

                                <th
                                    scope="col"
                                    class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wide text-zinc-600 dark:text-zinc-400"
                                >
                                    Role
                                </th>

                                <th
                                    scope="col"
                                    class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wide text-zinc-600 dark:text-zinc-400"
                                >
                                    Klinik
                                </th>

                                <th
                                    scope="col"
                                    class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wide text-zinc-600 dark:text-zinc-400"
                                >
                                    Status Email
                                </th>

                                <th
                                    scope="col"
                                    class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wide text-zinc-600 sm:px-6 dark:text-zinc-400"
                                >
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        {{-- TABLE BODY --}}
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">

                            @forelse ($users as $user)
                                @php
                                    $roleStyle = match ($user->role) {
                                        'admin' => 'purple',
                                        'asesor' => 'blue',
                                        'klinik' => 'teal',
                                        default => 'zinc',
                                    };

                                    $roleLabel = match ($user->role) {
                                        'admin' => 'ADMIN',
                                        'asesor' => 'ASESOR',
                                        'klinik' => 'PENGGUNA KLINIK',
                                        default => strtoupper($user->role),
                                    };
                                @endphp

                                <tr class="group transition-colors duration-150 odd:bg-white even:bg-zinc-50/50 hover:bg-amber-50/70 dark:odd:bg-zinc-950 dark:even:bg-zinc-900/40 dark:hover:bg-amber-500/5">

                                    {{-- USER --}}
                                    <td class="px-5 py-4 sm:px-6">
                                        <div class="flex min-w-0 items-center gap-3">

                                            {{-- PROFILE PHOTO --}}
                                            @if ($user->profile_photo_path)
                                                <img
                                                    src="{{ asset('storage/' . $user->profile_photo_path) }}"
                                                    alt="Foto profil {{ $user->name }}"
                                                    class="size-10 shrink-0 rounded-xl object-cover ring-1 ring-inset ring-zinc-200 dark:ring-zinc-700"
                                                    loading="lazy"
                                                />
                                            @else
                                                <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-sm font-semibold text-zinc-600 ring-1 ring-inset ring-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:ring-zinc-700">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                            @endif

                                            {{-- USER INFORMATION --}}
                                            <div class="min-w-0">
                                                <p class="font-semibold text-zinc-900 dark:text-white">
                                                    {{ $user->name }}
                                                </p>

                                                <p class="mt-1 break-all text-xs text-zinc-500 dark:text-zinc-400">
                                                    {{ $user->email }}
                                                </p>
                                            </div>

                                        </div>
                                    </td>

                                    {{-- ROLE --}}
                                    <td class="px-5 py-4">
                                        <flux:badge :color="$roleStyle">
                                            {{ $roleLabel }}
                                        </flux:badge>
                                    </td>

                                    {{-- CLINIC --}}
                                    <td class="px-5 py-4">
                                        @if ($user->role === 'klinik')
                                            @if ($user->clinic)
                                                <div class="space-y-1">
                                                    <p class="font-medium text-zinc-800 dark:text-zinc-200">
                                                        {{ $user->clinic->name }}
                                                    </p>

                                                    @if (! $user->clinic->is_active)
                                                        <flux:badge color="red" icon="x-circle">
                                                            Nonaktif
                                                        </flux:badge>
                                                    @endif
                                                </div>
                                            @else
                                                <flux:badge variant="warning">
                                                    Belum Ditentukan
                                                </flux:badge>
                                            @endif
                                        @else
                                            <span class="text-zinc-400 dark:text-zinc-500">
                                                -
                                            </span>
                                        @endif
                                    </td>

                                    {{-- EMAIL STATUS --}}
                                    <td class="px-5 py-4">
                                        @if ($user->email_verified_at)
                                            <flux:badge color="green" icon="check-circle">
                                                Terverifikasi
                                            </flux:badge>
                                        @else
                                            <flux:badge color="amber" icon="clock">
                                                Belum Terverifikasi
                                            </flux:badge>
                                        @endif
                                    </td>

                                    {{-- ACTION --}}
                                    <td class="px-5 py-4 text-right sm:px-6">
                                        <flux:button
                                            href="{{ route('admin.users.show', $user) }}"
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
                                                <flux:icon name="users" class="size-7" />
                                            </div>

                                            <p class="mt-4 font-semibold text-zinc-900 dark:text-white">
                                                @if ($search !== '' || $role !== '' || $clinicId !== '' || $verification !== '')
                                                    User tidak ditemukan
                                                @else
                                                    Belum ada user
                                                @endif
                                            </p>

                                            <p class="mt-1 text-sm leading-6 text-zinc-500 dark:text-zinc-400">
                                                @if ($search !== '' || $role !== '' || $clinicId !== '' || $verification !== '')
                                                    Tidak ada pengguna yang sesuai dengan pencarian atau filter yang dipilih.
                                                @else
                                                    Belum terdapat pengguna yang terdaftar di dalam sistem.
                                                @endif
                                            </p>

                                            @if ($search !== '' || $role !== '' || $clinicId !== '' || $verification !== '')
                                                <flux:button
                                                    href="{{ route('admin.users.index') }}"
                                                    variant="ghost"
                                                    class="mt-4 font-medium"
                                                >
                                                    Reset Filter
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
                @if ($users->hasPages())
                    <div class="flex flex-col gap-3 border-t border-zinc-200 bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6 dark:border-zinc-800 dark:bg-zinc-950">

                        <p class="text-xs text-zinc-600 dark:text-zinc-400">
                            Menampilkan

                            <span class="font-semibold text-zinc-800 dark:text-zinc-200">
                                {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }}
                            </span>

                            dari

                            <span class="font-semibold text-zinc-800 dark:text-zinc-200">
                                {{ $users->total() }}
                            </span>

                            user
                        </p>

                        <div>
                            {{ $users->onEachSide(1)->links() }}
                        </div>

                    </div>
                @endif

            </flux:card>

        </div>
    </flux:main>

    {{-- LIVE SEARCH & FILTERS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('user-search-form');
            const input = document.getElementById('user-search');
            const clearButton = document.getElementById('clear-user-search');

            if (!form || !input || !clearButton) {
                return;
            }

            let debounceTimer;
            let lastSubmittedValue = input.value.trim();
            const focusKey = 'user-search-restore-focus';

            // FILTER DROPDOWNS
            const filters = form.querySelectorAll(
                'select[name="role"], select[name="clinic_id"], select[name="verification"]'
            );

            filters.forEach(function (filter) {
                filter.addEventListener('change', function () {
                    form.requestSubmit();
                });
            });

            // UPDATE CLEAR BUTTON VISIBILITY
            function updateClearButton() {
                clearButton.classList.toggle('hidden', input.value.length === 0);
            }

            // SUBMIT SEARCH
            function submitSearch() {
                const value = input.value.trim();

                if (value === lastSubmittedValue) {
                    return;
                }

                lastSubmittedValue = value;
                sessionStorage.setItem(focusKey, '1');
                form.requestSubmit();
            }

            // RESTORE INPUT FOCUS AFTER SEARCH
            if (sessionStorage.getItem(focusKey) === '1') {
                sessionStorage.removeItem(focusKey);
                input.focus();
                input.setSelectionRange(input.value.length, input.value.length);
            }

            // LIVE SEARCH WITH DEBOUNCE
            input.addEventListener('input', function () {
                updateClearButton();
                clearTimeout(debounceTimer);

                debounceTimer = setTimeout(function () {
                    submitSearch();
                }, 700);
            });

            // CLEAR SEARCH
            clearButton.addEventListener('click', function () {
                clearTimeout(debounceTimer);
                input.value = '';
                updateClearButton();
                submitSearch();
            });
        });
    </script>

</x-layouts::app.sidebar>
