<x-layouts::app.sidebar :title="'Edit User'">

    <flux:main>
        <div class="space-y-6">

            {{-- PAGE HEADER --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2 text-sm text-zinc-500">
                        <a
                            href="{{ route('admin.users.index') }}"
                            class="transition hover:text-zinc-900 dark:hover:text-white"
                        >
                            Manajemen User
                        </a>

                        <span>/</span>

                        <span class="text-zinc-700 dark:text-zinc-300">
                            Edit User
                        </span>
                    </div>

                    <flux:heading size="xl" class="mt-2">
                        Edit User
                    </flux:heading>

                    <flux:subheading class="mt-1">
                        Perbarui informasi dan penempatan klinik pengguna.
                    </flux:subheading>
                </div>

                <flux:button
                    href="{{ route('admin.users.index') }}"
                    variant="ghost"
                    icon="arrow-left"
                >
                    Kembali
                </flux:button>
            </div>

            {{-- USER PROFILE --}}
            <flux:card>
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center">

                    {{-- PROFILE PHOTO --}}
                    <div class="flex size-22 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-amber-200/70 bg-amber-50/70 text-amber-700">
                        @if ($user->profile_photo_path)
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($user->profile_photo_path) }}"
                                alt="Foto profil {{ $user->name }}"
                                class="h-full w-full object-cover"
                            >
                        @else
                            <span class="text-2xl font-semibold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                        @endif
                    </div>

                    {{-- USER INFORMATION --}}
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">
                            Informasi Pengguna
                        </p>

                        <h1 class="mt-1 text-xl font-bold tracking-tight text-zinc-900 dark:text-white">
                            {{ $user->name }}
                        </h1>

                        <p class="mt-1 break-all text-sm text-zinc-500">
                            {{ $user->email }}
                        </p>
                    </div>

                    {{-- USER ROLE --}}
                    <div class="flex flex-row items-center justify-between gap-4 sm:flex-col sm:items-end">
                        <div class="sm:text-right">
                            <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">
                                Role Pengguna
                            </p>

                            <div class="mt-2">
                                <flux:badge color="zinc">
                                    {{ $user->role === 'klinik' ? 'PENGGUNA KLINIK' : strtoupper($user->role) }}
                                </flux:badge>
                            </div>

                            <p class="mt-2 text-xs text-zinc-400">
                                Role tidak dapat diubah dari halaman ini.
                            </p>
                        </div>
                    </div>

                </div>
            </flux:card>

            {{-- EDIT USER FORM --}}
            <flux:card class="overflow-hidden p-0">

                {{-- FORM HEADER --}}
                <div class="border-b border-zinc-200 px-6 py-5 dark:border-zinc-800">
                    <flux:heading size="lg">
                        Informasi Akun
                    </flux:heading>

                    <flux:subheading class="mt-1">
                        Ubah nama, email, dan penempatan klinik jika diperlukan.
                    </flux:subheading>
                </div>

                <form
                    id="updateUserForm"
                    method="POST"
                    action="{{ route('admin.users.update', $user) }}"
                >
                    @csrf
                    @method('PUT')

                    <div class="space-y-6 px-6 py-6">

                        {{-- BASIC INFORMATION --}}
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

                            {{-- NAME --}}
                            <div>
                                <label
                                    for="name"
                                    class="block text-sm font-medium text-zinc-700 dark:text-zinc-300"
                                >
                                    Nama Lengkap
                                </label>

                                <input
                                    id="name"
                                    name="name"
                                    type="text"
                                    value="{{ old('name', $user->name) }}"
                                    required
                                    maxlength="255"
                                    autocomplete="name"
                                    class="mt-2 block w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-700 shadow-sm transition placeholder:text-zinc-400 hover:border-zinc-400 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-zinc-600"
                                >

                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- EMAIL --}}
                            <div>
                                <label
                                    for="email"
                                    class="block text-sm font-medium text-zinc-700 dark:text-zinc-300"
                                >
                                    Alamat Email
                                </label>

                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email', $user->email) }}"
                                    required
                                    maxlength="255"
                                    autocomplete="email"
                                    class="mt-2 block w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-700 shadow-sm transition placeholder:text-zinc-400 hover:border-zinc-400 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-zinc-600"
                                >

                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                        </div>

                        {{-- CLINIC ASSIGNMENT --}}
                        @if ($user->role === 'klinik')
                            <div
                                x-data="{
                                    selectedClinicId: @js((string) old('clinic_id', $user->clinic_id)),
                                    originalClinicId: @js((string) old('clinic_id', $user->clinic_id)),
                                    clinicSearch: '',
                                    isClinicModalOpen: false,

                                    clinics: @js($clinics->map(fn ($clinic) => [
                                        'id' => (string) $clinic->id,
                                        'name' => $clinic->name,
                                        'code' => $clinic->code,
                                        'address' => $clinic->address ?? '',
                                    ])->values()),

                                    hasVisibleClinics() {
                                        const search = this.clinicSearch.trim().toLowerCase();

                                        return this.clinics.some(clinic => {
                                            const searchable = [
                                                clinic.name,
                                                clinic.code,
                                                clinic.address
                                            ].join(' ').toLowerCase();

                                            return searchable.includes(search);
                                        });
                                    },

                                    applyClinicSelection() {
                                        this.$refs.clinicInput.value = this.selectedClinicId;

                                        this.$refs.clinicInput.dispatchEvent(
                                            new Event('change', { bubbles: true })
                                        );

                                        this.isClinicModalOpen = false;
                                        this.clinicSearch = '';
                                    },

                                    closeClinicModal() {
                                        this.isClinicModalOpen = false;
                                        this.selectedClinicId = this.originalClinicId;
                                        this.clinicSearch = '';
                                    }
                                }"
                                @keydown.escape.window="if (isClinicModalOpen) closeClinicModal()"
                                class="border-t border-zinc-100 pt-6 dark:border-zinc-800"
                            >

                                {{-- HIDDEN FORM INPUT --}}
                                <input
                                    type="hidden"
                                    id="clinic_id"
                                    name="clinic_id"
                                    x-ref="clinicInput"
                                    value="{{ old('clinic_id', $user->clinic_id) }}"
                                >

                                {{-- SECTION HEADER --}}
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                        Penempatan Klinik
                                    </label>

                                    <p class="mt-1 text-sm text-zinc-500">
                                        Pengguna akan mengakses data sesuai dengan klinik yang dipilih.
                                    </p>
                                </div>

                                {{-- SELECTED CLINIC SUMMARY --}}
                                <div class="rounded-xl border border-zinc-200 bg-zinc-50/50 p-4 dark:border-zinc-700 dark:bg-zinc-900/40">

                                    @foreach ($clinics as $clinic)
                                        <div
                                            x-show="selectedClinicId === @js((string) $clinic->id)"
                                            x-cloak
                                            class="flex flex-col gap-4 sm:flex-row sm:items-center"
                                        >

                                            {{-- CLINIC ICON --}}
                                            <div class="flex size-14 shrink-0 items-center justify-center rounded-xl border border-amber-200/70 bg-amber-50/70 text-amber-700">
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

                                            {{-- CLINIC INFORMATION --}}
                                            <div class="min-w-0 flex-1">
                                                <p class="font-semibold text-zinc-900 dark:text-white">
                                                    {{ $clinic->name }}
                                                </p>

                                                <p class="mt-1 text-sm text-zinc-500">
                                                    Kode Klinik: {{ $clinic->code }}
                                                </p>

                                                @if ($clinic->address)
                                                    <p class="mt-1 text-sm text-zinc-500">
                                                        {{ $clinic->address }}
                                                    </p>
                                                @endif

                                                <div class="mt-2">
                                                    @if ($clinic->is_active)
                                                        <flux:badge color="green" icon="check-circle">
                                                            Klinik Aktif
                                                        </flux:badge>
                                                    @else
                                                        <flux:badge color="red" icon="x-circle">
                                                            Klinik Nonaktif
                                                        </flux:badge>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- CHANGE BUTTON --}}
                                            <div class="shrink-0">
                                                <flux:button
                                                    type="button"
                                                    variant="outline"
                                                    icon="pencil"
                                                    x-on:click="originalClinicId = selectedClinicId; isClinicModalOpen = true"
                                                >
                                                    Ubah
                                                </flux:button>
                                            </div>

                                        </div>
                                    @endforeach

                                    {{-- NO CLINIC SELECTED --}}
                                    <div
                                        x-show="!selectedClinicId"
                                        x-cloak
                                        class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                                    >
                                        <div class="flex items-center gap-3">
                                            <div class="flex size-14 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
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
                                                        d="M3 21h18M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"
                                                    />
                                                </svg>
                                            </div>

                                            <div>
                                                <p class="font-medium text-zinc-900 dark:text-white">
                                                    Belum ada klinik dipilih
                                                </p>

                                                <p class="mt-1 text-sm text-zinc-500">
                                                    Silakan tentukan penempatan klinik pengguna.
                                                </p>
                                            </div>
                                        </div>

                                        <flux:button
                                            type="button"
                                            variant="outline"
                                            icon="plus"
                                            x-on:click="originalClinicId = selectedClinicId; isClinicModalOpen = true"
                                        >
                                            Pilih Klinik
                                        </flux:button>
                                    </div>

                                </div>

                                @error('clinic_id')
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror

                                {{-- CLINIC SELECTION MODAL --}}
                                <div
                                    x-show="isClinicModalOpen"
                                    x-cloak
                                    x-transition.opacity
                                    class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-zinc-950/50 p-4 backdrop-blur-sm"
                                    x-on:click.self="closeClinicModal()"
                                >
                                    <div
                                        x-show="isClinicModalOpen"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="translate-y-3 scale-95 opacity-0"
                                        x-transition:enter-end="translate-y-0 scale-100 opacity-100"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="translate-y-0 scale-100 opacity-100"
                                        x-transition:leave-end="translate-y-3 scale-95 opacity-0"
                                        class="my-auto flex max-h-[85vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-zinc-900/10 dark:bg-zinc-900 dark:ring-white/10"
                                        role="dialog"
                                        aria-modal="true"
                                        aria-labelledby="clinicModalTitle"
                                    >

                                        {{-- MODAL HEADER --}}
                                        <div class="flex items-start justify-between gap-4 border-b border-zinc-200 px-5 py-5 dark:border-zinc-800 sm:px-6">
                                            <div>
                                                <h2
                                                    id="clinicModalTitle"
                                                    class="text-lg font-semibold text-zinc-900 dark:text-white"
                                                >
                                                    Pilih Klinik
                                                </h2>

                                                <p class="mt-1 text-sm text-zinc-500">
                                                    Pilih klinik yang akan dihubungkan dengan akun pengguna.
                                                </p>
                                            </div>

                                            <button
                                                type="button"
                                                x-on:click="closeClinicModal()"
                                                class="rounded-lg p-2 text-zinc-400 transition hover:bg-zinc-100 hover:text-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-zinc-200"
                                                aria-label="Tutup modal"
                                            >
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
                                                        d="M6 18 18 6M6 6l12 12"
                                                    />
                                                </svg>
                                            </button>
                                        </div>

                                        {{-- SEARCH --}}
                                        <div class="border-b border-zinc-100 px-5 py-4 dark:border-zinc-800 sm:px-6">
                                            <div class="relative">
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.8"
                                                    stroke="currentColor"
                                                    class="pointer-events-none absolute left-3 top-1/2 size-5 -translate-y-1/2 text-zinc-400"
                                                    aria-hidden="true"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="m21 21-4.34-4.34M19 10.5a8.5 8.5 0 1 1-17 0 8.5 8.5 0 0 1 17 0Z"
                                                    />
                                                </svg>

                                                <input
                                                    type="search"
                                                    x-model="clinicSearch"
                                                    placeholder="Cari nama, kode, atau alamat klinik..."
                                                    class="block w-full rounded-xl border border-zinc-300 bg-white py-3 pl-10 pr-4 text-sm text-zinc-700 shadow-sm outline-none transition placeholder:text-zinc-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-200"
                                                >
                                            </div>
                                        </div>

                                        {{-- CLINIC LIST --}}
                                        <div
                                            x-ref="clinicList"
                                            class="min-h-0 flex-1 space-y-2 overflow-y-auto p-4 sm:p-5"
                                        >

                                            @forelse ($clinics as $clinic)
                                                <button
                                                    type="button"
                                                    x-data="{
                                                        clinicId: @js((string) $clinic->id),
                                                        clinicName: @js(mb_strtolower($clinic->name)),
                                                        clinicCode: @js(mb_strtolower($clinic->code)),
                                                        clinicAddress: @js(mb_strtolower($clinic->address ?? ''))
                                                    }"
                                                    x-show="[clinicName, clinicCode, clinicAddress].some(value => value.includes(clinicSearch.trim().toLowerCase()))"
                                                    x-on:click="selectedClinicId = clinicId"
                                                    x-bind:class="selectedClinicId === clinicId
                                                        ? 'border-amber-400 bg-amber-50/70 ring-1 ring-amber-400/30 dark:border-amber-600 dark:bg-amber-950/20'
                                                        : 'border-zinc-200 bg-white hover:border-amber-300 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:bg-zinc-800'"
                                                    class="flex w-full items-center gap-3 rounded-xl border p-3 text-left transition sm:gap-4 sm:p-4"
                                                >

                                                    {{-- CLINIC ICON --}}
                                                    <div class="flex size-12 shrink-0 items-center justify-center rounded-xl border border-amber-200/70 bg-amber-50/70 text-amber-700">
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                            stroke-width="1.8"
                                                            stroke="currentColor"
                                                            class="size-6"
                                                            aria-hidden="true"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                d="M3 21h18M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16M9 7h1m4 0h1M9 11h1m4 0h1M9 15h1m4 0h1"
                                                            />
                                                        </svg>
                                                    </div>

                                                    {{-- CLINIC INFORMATION --}}
                                                    <div class="min-w-0 flex-1">
                                                        <p class="truncate font-semibold text-zinc-900 dark:text-white">
                                                            {{ $clinic->name }}
                                                        </p>

                                                        <p class="mt-1 text-xs text-zinc-500">
                                                            {{ $clinic->code }}
                                                        </p>

                                                        @if ($clinic->address)
                                                            <p class="mt-1 truncate text-xs text-zinc-500">
                                                                {{ $clinic->address }}
                                                            </p>
                                                        @endif

                                                        <div class="mt-2">
                                                            @if ($clinic->is_active)
                                                                <flux:badge color="green">
                                                                    Aktif
                                                                </flux:badge>
                                                            @else
                                                                <flux:badge color="red">
                                                                    Nonaktif
                                                                </flux:badge>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    {{-- SELECTION INDICATOR --}}
                                                    <div class="flex size-6 shrink-0 items-center justify-center rounded-full border transition"
                                                        x-bind:class="selectedClinicId === clinicId
                                                            ? 'border-amber-600 bg-amber-600 text-white'
                                                            : 'border-zinc-300 text-transparent dark:border-zinc-600'"
                                                    >
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                            stroke-width="2.5"
                                                            stroke="currentColor"
                                                            class="size-3.5"
                                                            aria-hidden="true"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                d="m5 12 4 4L19 6"
                                                            />
                                                        </svg>
                                                    </div>

                                                </button>
                                            @empty
                                                <div class="py-10 text-center">
                                                    <p class="text-sm text-zinc-500">
                                                        Belum ada data klinik.
                                                    </p>
                                                </div>
                                            @endforelse

                                            {{-- SEARCH EMPTY STATE --}}
                                            <div
                                                x-show="!hasVisibleClinics()"
                                                x-cloak
                                                class="py-10 text-center"
                                            >
                                                <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-zinc-100 text-zinc-400 dark:bg-zinc-800">
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke-width="1.8"
                                                        stroke="currentColor"
                                                        class="size-6"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            d="m21 21-4.34-4.34M19 10.5a8.5 8.5 0 1 1-17 0 8.5 8.5 0 0 1 17 0Z"
                                                        />
                                                    </svg>
                                                </div>

                                                <p class="mt-3 font-medium text-zinc-700 dark:text-zinc-300">
                                                    Klinik tidak ditemukan
                                                </p>

                                                <p class="mt-1 text-sm text-zinc-500">
                                                    Coba gunakan kata kunci pencarian lain.
                                                </p>
                                            </div>

                                        </div>

                                        {{-- MODAL FOOTER --}}
                                        <div class="flex flex-col-reverse gap-2 border-t border-zinc-200 px-5 py-4 dark:border-zinc-800 sm:flex-row sm:items-center sm:justify-between sm:px-6">

                                            <p class="text-xs text-zinc-500">
                                                Pilih satu klinik untuk akun pengguna ini.
                                            </p>

                                            <div class="flex items-center justify-end gap-2">
                                                <flux:button
                                                    type="button"
                                                    variant="ghost"
                                                    x-on:click="closeClinicModal()"
                                                >
                                                    Batal
                                                </flux:button>

                                                <flux:button
                                                    type="button"
                                                    variant="primary"
                                                    x-bind:disabled="!selectedClinicId"
                                                    x-on:click="applyClinicSelection()"
                                                >
                                                    Pilih Klinik
                                                </flux:button>
                                            </div>

                                        </div>

                                    </div>
                                </div>

                            </div>
                        @endif

                    </div>

                    {{-- FORM ACTIONS --}}
                    <div class="flex flex-col-reverse gap-2 border-t border-zinc-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-end dark:border-zinc-800">

                        <flux:button
                            href="{{ route('admin.users.index') }}"
                            variant="ghost"
                        >
                            Batal
                        </flux:button>

                        <flux:button
                            id="saveUserButton"
                            type="submit"
                            variant="primary"
                            disabled
                        >
                            Simpan Perubahan
                        </flux:button>

                    </div>
                </form>

            </flux:card>

        </div>
    </flux:main>

    {{-- SWEETALERT2 & FORM INTERACTION --}}
    <script>
        (() => {
            const form = document.getElementById('updateUserForm');
            const saveButton = document.getElementById('saveUserButton');

            if (!form || !saveButton) {
                return;
            }

            const fields = [
                document.getElementById('name'),
                document.getElementById('email'),
                document.getElementById('clinic_id'),
            ].filter(Boolean);

            const initialValues = new Map(
                fields.map(field => [field.name, field.value])
            );

            function hasChanges() {
                return fields.some(
                    field => field.value !== initialValues.get(field.name)
                );
            }

            function updateSaveButton() {
                saveButton.disabled = !hasChanges();
            }

            fields.forEach(field => {
                field.addEventListener('input', updateSaveButton);
                field.addEventListener('change', updateSaveButton);
            });

            updateSaveButton();

            form.addEventListener('submit', function (event) {
                event.preventDefault();

                if (!form.reportValidity()) {
                    return;
                }

                const clinicField = document.getElementById('clinic_id');

                if (clinicField && !clinicField.value) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Klinik Belum Dipilih',
                        text: 'Silakan pilih klinik untuk akun pengguna ini.',
                        confirmButtonText: 'Pilih Klinik',
                        confirmButtonColor: '#d97706',
                    });

                    return;
                }

                if (!hasChanges()) {
                    updateSaveButton();

                    Swal.fire({
                        icon: 'info',
                        title: 'Tidak Ada Perubahan',
                        text: 'Ubah setidaknya satu informasi sebelum menyimpan.',
                        confirmButtonText: 'Mengerti',
                        confirmButtonColor: '#d97706',
                    });

                    return;
                }

                Swal.fire({
                    title: 'Simpan Perubahan User?',
                    text: 'Informasi akun pengguna akan diperbarui sesuai perubahan yang kamu buat.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d97706',
                    cancelButtonColor: '#71717a',
                    reverseButtons: true,
                    focusCancel: true,
                }).then(result => {
                    if (result.isConfirmed) {
                        HTMLFormElement.prototype.submit.call(form);
                    }
                });
            });
        })();

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: @json(session('success')),
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#d97706',
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: @json(session('error')),
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#d97706',
            });
        @endif
    </script>

</x-layouts::app.sidebar>
