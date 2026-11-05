
<x-layouts::app.sidebar :title="'Detail User'">

    <flux:main>
        <div class="space-y-6">

            {{-- PAGE HEADER --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2 text-sm text-zinc-500">
                        <a
                            href="{{ route('admin.users.index') }}"
                            class="transition hover:text-zinc-900"
                        >
                            Manajemen User
                        </a>

                        <span>/</span>

                        <span class="text-zinc-700">
                            Detail User
                        </span>
                    </div>

                    <flux:heading size="xl" class="mt-2">
                        Detail User
                    </flux:heading>

                    <flux:subheading class="mt-1">
                        Informasi akun dan penempatan pengguna dalam sistem.
                    </flux:subheading>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <flux:button
                        href="{{ route('admin.users.index') }}"
                        variant="ghost"
                        icon="arrow-left"
                    >
                        Kembali
                    </flux:button>

                    <flux:button
                        href="{{ route('admin.users.edit', $user) }}"
                        variant="primary"
                        icon="pencil"
                    >
                        Edit User
                    </flux:button>
                </div>
            </div>

            {{-- USER PROFILE --}}
            <flux:card>
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center">

                    {{-- USER PROFILE PHOTO --}}
                    <div class="flex size-22 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-amber-200/70 bg-amber-50/70 text-amber-700">
                        @if ($user->profile_photo_path)
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($user->profile_photo_path) }}"
                                alt="Foto profil {{ $user->name }}"
                                class="h-full w-full object-cover"
                            >
                        @else
                            <div class="flex h-full w-full items-center justify-center text-xl font-semibold text-zinc-600 dark:text-zinc-300">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    {{-- USER INFORMATION --}}
                    <div class="min-w-0 flex-1">
                        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
                            {{ $user->name }}
                        </h1>

                        <p class="mt-1 break-all text-sm text-zinc-500">
                            {{ $user->email }}
                        </p>

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <flux:badge color="zinc">
                                {{ $user->role === 'klinik' ? 'PENGGUNA KLINIK' : strtoupper($user->role) }}
                            </flux:badge>

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
                </div>
            </flux:card>

            {{-- ACCOUNT INFORMATION --}}
            <flux:card>
                <div class="space-y-5">
                    <div>
                        <flux:heading size="lg">
                            Informasi Akun
                        </flux:heading>

                        <flux:subheading class="mt-1">
                            Identitas dan informasi dasar pengguna.
                        </flux:subheading>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <p class="text-sm text-zinc-500">
                                Nama Lengkap
                            </p>

                            <p class="mt-1 font-medium text-zinc-900 dark:text-white">
                                {{ $user->name }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-zinc-500">
                                Alamat Email
                            </p>

                            <p class="mt-1 break-all font-medium text-zinc-900 dark:text-white">
                                {{ $user->email }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-zinc-500">
                                Role Pengguna
                            </p>

                            <p class="mt-1 font-medium text-zinc-900 dark:text-white">
                                {{ $user->role === 'klinik' ? 'Pengguna Klinik' : ucfirst($user->role) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-zinc-500">
                                Status Verifikasi Email
                            </p>

                            <p class="mt-1 font-medium text-zinc-900 dark:text-white">
                                {{ $user->email_verified_at ? 'Terverifikasi' : 'Belum Terverifikasi' }}
                            </p>
                        </div>
                    </div>
                </div>
            </flux:card>

            {{-- CLINIC ASSIGNMENT --}}
            @if ($user->role === 'klinik')
                <flux:card>
                    <div class="space-y-5">
                        <div>
                            <flux:heading size="lg">
                                Penempatan Klinik
                            </flux:heading>

                            <flux:subheading class="mt-1">
                                Klinik yang terhubung dengan akun pengguna ini.
                            </flux:subheading>
                        </div>

                        @if ($user->clinic)
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">

                                {{-- CLINIC ICON --}}
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
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
                                            d="M3.75 21h16.5M4.5 21V5.25L12 3l7.5 2.25V21M8.25 8.25h.008v.008H8.25V8.25Zm0 3.75h.008v.008H8.25V12Zm0 3.75h.008v.008H8.25V15.75Zm7.5-7.5h.008v.008h-.008V8.25Zm0 3.75h.008v.008h-.008V12Zm0 3.75h.008v.008h-.008V15.75ZM10.5 21v-3.75h3V21"
                                        />
                                    </svg>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="font-semibold text-zinc-900 dark:text-white">
                                        {{ $user->clinic->name }}
                                    </p>

                                    <p class="mt-1 text-sm text-zinc-500">
                                        Kode Klinik: {{ $user->clinic->code }}
                                    </p>

                                    @if (! $user->clinic->is_active)
                                        <div class="mt-2">
                                            <flux:badge color="red" icon="x-circle">
                                                Klinik Nonaktif
                                            </flux:badge>
                                        </div>
                                    @else
                                        <div class="mt-2">
                                            <flux:badge color="green" icon="check-circle">
                                                Klinik Aktif
                                            </flux:badge>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if (! $user->clinic->is_active)
                                <flux:callout variant="warning" icon="exclamation-triangle">
                                    Pengguna ini masih terhubung dengan klinik nonaktif. Kamu dapat memindahkan penempatannya melalui menu Edit User.
                                </flux:callout>
                            @endif
                        @else
                            <flux:callout variant="warning" icon="exclamation-triangle">
                                Pengguna ini belum memiliki penempatan klinik.
                            </flux:callout>
                        @endif
                    </div>
                </flux:card>
            @endif

        </div>
    </flux:main>

</x-layouts::app.sidebar>
