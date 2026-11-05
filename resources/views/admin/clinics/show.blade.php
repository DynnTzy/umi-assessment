<x-layouts::app.sidebar :title="'Detail Klinik'">

    <flux:main>
        <div
            class="space-y-6"
            x-data="{
                showAssignModal: false,
                searchUser: '',
                selectedUser: ''
            }"
        >

            {{-- PAGE HEADER --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2 text-sm text-zinc-500">
                        <a
                            href="{{ route('admin.clinics.index') }}"
                            class="transition hover:text-zinc-900 dark:hover:text-white"
                        >
                            Manajemen Klinik
                        </a>

                        <span>/</span>

                        <span class="text-zinc-700 dark:text-zinc-300">
                            Detail Klinik
                        </span>
                    </div>

                    <flux:heading size="xl" class="mt-2">
                        {{ $clinic->name }}
                    </flux:heading>

                    <flux:subheading class="mt-1">
                        Informasi klinik dan pengguna yang terhubung dalam sistem.
                    </flux:subheading>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <flux:button
                        href="{{ route('admin.clinics.index') }}"
                        variant="ghost"
                        icon="arrow-left"
                    >
                        Kembali
                    </flux:button>

                    @if (auth()->user()->role === 'admin')
                        <flux:button
                            href="{{ route('admin.clinics.edit', $clinic) }}"
                            variant="primary"
                            icon="pencil"
                        >
                            Edit Klinik
                        </flux:button>

                        <form
                            id="toggle-status-form"
                            method="POST"
                            action="{{ route('admin.clinics.toggle-status', $clinic) }}"
                        >
                            @csrf
                            @method('PATCH')

                            @if ($clinic->is_active)
                                <flux:button
                                    type="submit"
                                    variant="danger"
                                    icon="x-circle"
                                >
                                    Nonaktifkan
                                </flux:button>
                            @else
                                <flux:button
                                    type="submit"
                                    variant="primary"
                                    icon="check-circle"
                                >
                                    Aktifkan Klinik
                                </flux:button>
                            @endif
                        </form>
                    @endif
                </div>
            </div>


            {{-- CLINIC IDENTITY --}}
            <flux:card class="overflow-hidden p-0">
                <div class="flex flex-col gap-5 p-6 sm:flex-row sm:items-center sm:justify-between">

                    <div class="flex min-w-0 items-center gap-4">
                        {{-- Clinic Icon --}}
                        <div class="flex size-16 shrink-0 items-center justify-center rounded-2xl border border-amber-200/70 bg-amber-50/70 text-amber-700 dark:border-amber-900 dark:bg-amber-950/30 dark:text-amber-400">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.8"
                                stroke="currentColor"
                                class="size-8"
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
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-lg font-semibold tracking-tight text-zinc-900 dark:text-white">
                                    {{ $clinic->name }}
                                </h2>

                                <flux:badge color="zinc">
                                    {{ $clinic->code }}
                                </flux:badge>

                                @if ($clinic->is_active)
                                    <flux:badge color="green" icon="check-circle">
                                        Aktif
                                    </flux:badge>
                                @else
                                    <flux:badge color="red" icon="x-circle">
                                        Nonaktif
                                    </flux:badge>
                                @endif
                            </div>

                            <p class="mt-1 text-sm text-zinc-500">
                                Informasi dan identitas klinik
                            </p>
                        </div>
                    </div>

                    {{-- User Count --}}
                    <div class="flex items-center gap-3 rounded-xl bg-zinc-50 px-4 py-3 dark:bg-zinc-800/60 sm:min-w-32 sm:justify-center">
                        <div class="flex size-10 items-center justify-center rounded-xl bg-white text-zinc-500 shadow-sm dark:bg-zinc-700 dark:text-zinc-300">
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
                                    d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m12-13a4 4 0 1 1-8 0 4 4 0 0 1 8 0Zm4 13v-2a4 4 0 0 0-3-3.87M17 3.13a4 4 0 0 1 0 7.75"
                                />
                            </svg>
                        </div>

                        <div>
                            <p class="text-xl font-bold tabular-nums text-zinc-900 dark:text-white">
                                {{ $clinic->users->count() }}
                            </p>

                            <p class="text-xs text-zinc-500">
                                Pengguna
                            </p>
                        </div>
                    </div>
                </div>
            </flux:card>


            {{-- CLINIC DETAILS --}}
            <flux:card>
                <div class="space-y-5">
                    <div>
                        <flux:heading size="lg">
                            Informasi Klinik
                        </flux:heading>

                        <flux:subheading class="mt-1">
                            Detail kontak dan informasi dasar klinik.
                        </flux:subheading>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">

                        {{-- Address --}}
                        <div class="flex items-start gap-3">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.8"
                                    stroke="currentColor"
                                    class="size-5"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 21s7-5.5 7-12a7 7 0 1 0-14 0c0 6.5 7 12 7 12Z"
                                    />
                                    <circle cx="12" cy="9" r="2.5" />
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm font-medium text-zinc-500">
                                    Alamat Klinik
                                </p>

                                <p class="mt-1 text-sm leading-6 text-zinc-900 dark:text-white">
                                    {{ $clinic->address ?: 'Belum diisi' }}
                                </p>
                            </div>
                        </div>

                        {{-- Phone --}}
                        <div class="flex items-start gap-3">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.8"
                                    stroke="currentColor"
                                    class="size-5"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M2.5 5.5A2.5 2.5 0 0 1 5 3h2l2 5-2.5 1.5a15 15 0 0 0 8 8L16 15l5 2v2a2.5 2.5 0 0 1-2.5 2.5A17.5 17.5 0 0 1 2.5 5.5Z"
                                    />
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm font-medium text-zinc-500">
                                    Nomor Telepon
                                </p>

                                <p class="mt-1 break-words text-sm text-zinc-900 dark:text-white">
                                    {{ $clinic->phone ?: 'Belum diisi' }}
                                </p>
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="flex items-start gap-3">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.8"
                                    stroke="currentColor"
                                    class="size-5"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M3 6.75A2.25 2.25 0 0 1 5.25 4.5h13.5A2.25 2.25 0 0 1 21 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 17.25V6.75Z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="m3.5 6 8.5 6.5L20.5 6"
                                    />
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm font-medium text-zinc-500">
                                    Email Klinik
                                </p>

                                <p class="mt-1 break-all text-sm text-zinc-900 dark:text-white">
                                    {{ $clinic->email ?: 'Belum diisi' }}
                                </p>
                            </div>
                        </div>

                        {{-- Clinic Status --}}
                        <div class="flex items-start gap-3">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.8"
                                    stroke="currentColor"
                                    class="size-5"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M9 12.75 11.25 15 15 9.75m6 2.25a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                    />
                                </svg>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-zinc-500">
                                    Status Klinik
                                </p>

                                <div class="mt-1">
                                    @if ($clinic->is_active)
                                        <flux:badge color="green" icon="check-circle">
                                            Aktif
                                        </flux:badge>
                                    @else
                                        <flux:badge color="red" icon="x-circle">
                                            Nonaktif
                                        </flux:badge>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </flux:card>


            {{-- CLINIC USERS --}}
            <flux:card class="overflow-hidden p-0">

                {{-- Section Header --}}
                <div class="flex flex-col gap-4 border-b border-zinc-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between dark:border-zinc-800">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <flux:heading size="lg">
                                Pengguna Klinik
                            </flux:heading>

                            <flux:badge color="zinc">
                                {{ $clinic->users->count() }}
                            </flux:badge>
                        </div>

                        <flux:subheading class="mt-1">
                            Daftar pengguna yang saat ini terhubung dengan klinik ini.
                        </flux:subheading>
                    </div>

                    {{-- ASSIGN USER BUTTON --}}
                    <flux:button
                        type="button"
                        variant="primary"
                        icon="user-plus"
                        x-on:click="showAssignModal = true"
                    >
                        Assign Pengguna
                    </flux:button>
                </div>


                {{-- User List --}}
                <div>
                    @forelse ($clinic->users as $user)

                        <div class="flex flex-col gap-4 border-b border-zinc-100 px-6 py-5 last:border-b-0 transition hover:bg-zinc-50/70 sm:flex-row sm:items-center sm:justify-between dark:border-zinc-800 dark:hover:bg-zinc-800/30">

                            {{-- User Identity --}}
                            <div class="flex min-w-0 items-center gap-3">

                                {{-- Profile Photo --}}
                                <div class="size-11 shrink-0 overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-800">
                                    @if ($user->profile_photo_path)
                                        <img
                                            src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($user->profile_photo_path) }}"
                                            alt="Foto profil {{ $user->name }}"
                                            class="size-full object-cover"
                                        >
                                    @else
                                        <div class="flex size-full items-center justify-center text-sm font-semibold text-zinc-600 dark:text-zinc-300">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <p class="truncate font-medium text-zinc-900 dark:text-white">
                                        {{ $user->name }}
                                    </p>

                                    <p class="mt-0.5 break-all text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $user->email }}
                                    </p>
                                </div>
                            </div>


                            {{-- User Status --}}
                            <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                                @if ($user->email_verified_at)
                                    <flux:badge color="green" icon="check-circle">
                                        Email Terverifikasi
                                    </flux:badge>
                                @else
                                    <flux:badge color="amber" icon="clock">
                                        Belum Terverifikasi
                                    </flux:badge>
                                @endif
                            </div>

                        </div>

                    @empty

                        {{-- Empty State --}}
                        <div class="px-6 py-16 text-center">
                            <div class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.6"
                                    stroke="currentColor"
                                    class="size-7"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m12-13a4 4 0 1 1-8 0 4 4 0 0 1 8 0Zm4 13v-2a4 4 0 0 0-3-3.87M17 3.13a4 4 0 0 1 0 7.75"
                                    />
                                </svg>
                            </div>

                            <p class="mt-4 font-medium text-zinc-900 dark:text-white">
                                Belum ada pengguna
                            </p>

                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                                Belum ada pengguna yang terhubung dengan klinik ini.
                            </p>

                            <div class="mt-5">
                                <flux:button
                                    type="button"
                                    variant="primary"
                                    icon="user-plus"
                                    x-on:click="showAssignModal = true"
                                >
                                    Assign Pengguna
                                </flux:button>
                            </div>
                        </div>

                    @endforelse
                </div>

            </flux:card>


            {{-- =========================================================
                MODAL ASSIGN PENGGUNA
            ========================================================== --}}
            <div
                x-cloak
                x-show="showAssignModal"
                x-transition.opacity
                class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-zinc-950/50 p-4 backdrop-blur-sm"
                x-on:keydown.escape.window="showAssignModal = false"
                x-on:click.self="showAssignModal = false"
            >
                <div
                    x-show="showAssignModal"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="my-auto flex max-h-[85vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-2xl dark:border-zinc-700 dark:bg-zinc-900"
                    x-on:click.stop
                >

                    {{-- Modal Header --}}
                    <div class="flex items-start justify-between border-b border-zinc-200 px-6 py-5 dark:border-zinc-800">
                        <div>
                            <flux:heading size="lg">
                                Pilih Pengguna
                            </flux:heading>

                            <flux:subheading class="mt-1">
                                Pilih pengguna yang akan dihubungkan dengan klinik ini.
                            </flux:subheading>
                        </div>

                        <button
                            type="button"
                            x-on:click="showAssignModal = false"
                            class="rounded-lg p-1 text-zinc-400 transition hover:bg-zinc-100 hover:text-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-white"
                            aria-label="Tutup modal"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                class="size-5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 18 18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>


                    {{-- Modal Content --}}
                    <div class="min-h-0 flex-1 overflow-y-auto px-5 py-4 sm:px-6">

                        @if ($availableUsers->isNotEmpty())

                            {{-- Search --}}
                            <div class="relative mb-4">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    class="pointer-events-none absolute left-3.5 top-1/2 size-5 -translate-y-1/2 text-zinc-400"
                                >
                                    <circle cx="11" cy="11" r="7" />
                                    <path
                                        stroke-linecap="round"
                                        d="m20 20-4-4"
                                    />
                                </svg>

                                <input
                                    type="text"
                                    x-model="searchUser"
                                    placeholder="Cari nama atau email pengguna..."
                                    class="w-full rounded-xl border border-zinc-200 bg-white py-3 pl-11 pr-4 text-sm text-zinc-800 shadow-sm outline-none transition placeholder:text-zinc-400 focus:border-amber-400 focus:ring-2 focus:ring-amber-100 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:focus:ring-amber-900/30"
                                />
                            </div>


                            {{-- User List --}}
                            <form
                                id="assign-user-form"
                                method="POST"
                                action="{{ route('admin.clinics.assign-user', $clinic) }}"
                            >
                                @csrf

                                <div class="space-y-2">

                                    @foreach ($availableUsers as $user)

                                        <label
                                            x-show="'{{ strtolower(addslashes($user->name)) }} {{ strtolower(addslashes($user->email)) }}'.includes(searchUser.toLowerCase())"
                                            class="flex cursor-pointer items-center gap-4 rounded-xl border p-4 transition"
                                            :class="selectedUser == '{{ $user->id }}'
                                                ? 'border-amber-400 bg-amber-50/70 dark:border-amber-500 dark:bg-amber-500/10'
                                                : 'border-zinc-200 bg-white hover:border-amber-300 hover:bg-amber-50/30 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:bg-zinc-800'"
                                        >
                                            <input
                                                type="radio"
                                                name="user_id"
                                                value="{{ $user->id }}"
                                                x-model="selectedUser"
                                                required
                                                class="size-4 shrink-0 border-zinc-300 text-amber-600 focus:ring-amber-500"
                                            />

                                            {{-- Avatar --}}
                                            <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-sm font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>

                                            {{-- Identity --}}
                                            <div class="min-w-0 flex-1">
                                                <p class="font-semibold text-zinc-900 dark:text-white">
                                                    {{ $user->name }}
                                                </p>

                                                <p class="mt-0.5 break-all text-sm text-zinc-500 dark:text-zinc-400">
                                                    {{ $user->email }}
                                                </p>
                                            </div>

                                            <flux:badge variant="neutral">
                                                Klinik
                                            </flux:badge>
                                        </label>

                                    @endforeach

                                </div>

                                {{-- No Search Results --}}
                                <div
                                    x-show="searchUser !== '' && !Array.from($el.parentElement.querySelectorAll('label')).some(label => label.offsetParent !== null)"
                                    class="py-10 text-center text-sm text-zinc-500"
                                >
                                    Tidak ada pengguna yang cocok dengan pencarian.
                                </div>

                            </form>

                        @else

                            {{-- Empty State --}}
                            <div class="py-12 text-center">
                                <div class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                        class="size-7"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m16 0v-2a4 4 0 0 0-3-3.87M10 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm10-3a4 4 0 0 1-3 3.87"
                                        />
                                    </svg>
                                </div>

                                <p class="mt-4 font-medium text-zinc-800 dark:text-zinc-200">
                                    Tidak ada pengguna tersedia
                                </p>

                                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                                    Semua pengguna klinik sudah terhubung dengan klinik lain atau belum tersedia.
                                </p>
                            </div>

                        @endif

                    </div>


                    {{-- Modal Footer --}}
                    <div class="flex flex-col-reverse gap-3 border-t border-zinc-200 px-6 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-zinc-800">

                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            Pilih satu pengguna untuk klinik ini.
                        </p>

                        <div class="flex items-center justify-end gap-3">
                            <flux:button
                                type="button"
                                variant="ghost"
                                x-on:click="showAssignModal = false"
                            >
                                Batal
                            </flux:button>

                            @if ($availableUsers->isNotEmpty())
                                <flux:button
                                    type="submit"
                                    form="assign-user-form"
                                    variant="primary"
                                    icon="user-plus"
                                    x-bind:disabled="!selectedUser"
                                >
                                    Assign Pengguna
                                </flux:button>
                            @endif
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </flux:main>

</x-layouts::app.sidebar>


{{-- SWEETALERT2: KONFIRMASI STATUS KLINIK --}}
<script>
    document.addEventListener('click', async function (event) {
        const target = event.target;

        if (!(target instanceof Element)) {
            return;
        }

        const button = target.closest(
            '#toggle-status-form button[type="submit"]'
        );

        if (!button) {
            return;
        }

        event.preventDefault();

        if (button.dataset.confirming === 'true') {
            return;
        }

        button.dataset.confirming = 'true';

        const form = button.closest('form');

        if (!form) {
            button.dataset.confirming = 'false';
            return;
        }

        const isActive = @json((bool) $clinic->is_active);

        try {
            const result = await Swal.fire({
                title: isActive
                    ? 'Nonaktifkan Klinik?'
                    : 'Aktifkan Klinik Kembali?',

                text: isActive
                    ? 'Klinik tidak dapat memulai assessment baru setelah dinonaktifkan.'
                    : 'Klinik dapat digunakan kembali untuk proses assessment baru.',

                icon: 'warning',
                showCancelButton: true,

                confirmButtonText: isActive
                    ? 'Ya, Nonaktifkan'
                    : 'Ya, Aktifkan',

                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true,

                confirmButtonColor: isActive
                    ? '#dc2626'
                    : '#b8954a',

                cancelButtonColor: '#71717a',
            });

            if (result.isConfirmed) {
                HTMLFormElement.prototype.submit.call(form);
            }
        } catch (error) {
            console.error(
                'Gagal menampilkan konfirmasi status klinik:',
                error
            );
        } finally {
            button.dataset.confirming = 'false';
        }
    });


    {{-- SUCCESS NOTIFICATION --}}
    @if (session('success'))
        Swal.fire({
            title: 'Berhasil!',
            text: @json(session('success')),
            icon: 'success',
            confirmButtonText: 'Mengerti',
            confirmButtonColor: '#b8954a',
        });
    @endif


    {{-- ERROR NOTIFICATION --}}
    @if (session('error'))
        Swal.fire({
            title: 'Gagal!',
            text: @json(session('error')),
            icon: 'error',
            confirmButtonText: 'Mengerti',
            confirmButtonColor: '#b8954a',
        });
    @endif
</script>
