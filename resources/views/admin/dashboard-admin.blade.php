<x-layouts::app :title="'Dashboard Admin'">

    {{-- =========================================================
        CONTENT
    ========================================================== --}}
    <div class="space-y-6 px-5 py-6 lg:px-8 lg:py-8">

        {{-- =================================================
            WELCOME CARD
        ================================================== --}}
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

            <div class="p-6 sm:p-8">

                <p class="text-sm font-semibold text-[#B28D32]">
                    Dr. Umi Assessment
                </p>

                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    Halo, {{ auth()->user()->name }}
                </h2>

                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500">
                    Kelola pengguna, klinik, asesor, dan akses sistem
                    melalui dashboard administrator.
                </p>

            </div>

        </section>


        {{-- =================================================
            STATISTICS
        ================================================== --}}
        <section class="grid grid-cols-1 gap-4 md:grid-cols-3">

            {{-- Total User --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <p class="text-sm font-medium text-slate-500">
                    Total User
                </p>

                <p class="mt-3 text-3xl font-bold text-slate-900">
                    {{ $totalUsers }}
                </p>

                <p class="mt-3 text-xs text-slate-400">
                    Seluruh pengguna sistem
                </p>

            </div>


            {{-- Menunggu Penetapan --}}
            <div class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm">

                <p class="text-sm font-medium text-slate-500">
                    Menunggu Penetapan
                </p>

                <p class="mt-3 text-3xl font-bold text-slate-900">
                    {{ $pendingClinicUsers }}
                </p>

                <p class="mt-3 text-xs text-slate-400">
                    User klinik yang belum memiliki klinik
                </p>

            </div>


            {{-- Total Klinik --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <p class="text-sm font-medium text-slate-500">
                    Total Klinik
                </p>

                <p class="mt-3 text-3xl font-bold text-slate-900">
                    {{ $totalClinics }}
                </p>

                <p class="mt-3 text-xs text-slate-400">
                    Klinik yang terdaftar
                </p>

            </div>

        </section>


        {{-- =================================================
            PENDING USERS
        ================================================== --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 px-6 py-5">

                <h2 class="font-semibold text-slate-900">
                    User Menunggu Penetapan Klinik
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    User klinik yang sudah terdaftar tetapi belum
                    terhubung dengan klinik.
                </p>

            </div>


            @if ($pendingUsers->count())

                <div class="divide-y divide-slate-100">

                    @foreach ($pendingUsers as $user)

                        <div class="flex flex-col gap-4 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

                            {{-- User Information --}}
                            <div>

                                <p class="font-semibold text-slate-900">
                                    {{ $user->name }}
                                </p>

                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $user->email }}
                                </p>

                            </div>


                            {{-- Status and Action --}}
                            <div class="flex flex-wrap items-center gap-3">

                                @if ($user->email_verified_at)

                                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                        Email Terverifikasi
                                    </span>

                                @else

                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                        Belum Terverifikasi
                                    </span>

                                @endif


                                <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                                    Belum Ditetapkan
                                </span>


                                <a
                                    href="{{ route('admin.users.edit', $user) }}"
                                    class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white transition hover:bg-slate-800"
                                >
                                    Tetapkan Klinik
                                </a>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="px-6 py-10 text-center">

                    <p class="text-sm font-medium text-slate-700">
                        Tidak ada user yang menunggu penetapan
                    </p>

                    <p class="mt-1 text-sm text-slate-400">
                        Semua user klinik sudah memiliki klinik.
                    </p>

                </div>

            @endif

        </section>


        {{-- =================================================
            QUICK ACTIONS
        ================================================== --}}
        <section class="grid grid-cols-1 gap-4 lg:grid-cols-2">

            {{-- Manajemen User --}}
            <a
                href="{{ route('admin.users.index') }}"
                class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md"
            >

                <p class="font-semibold text-slate-900">
                    Manajemen User
                </p>

                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Kelola akun user dan tetapkan user klinik
                    ke klinik yang sesuai.
                </p>

                <p class="mt-4 text-sm font-semibold text-[#B28D32]">
                    Kelola User →
                </p>

            </a>


            {{-- Manajemen Klinik --}}
            <a
                href="{{ route('admin.clinics.index') }}"
                class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md"
            >

                <p class="font-semibold text-slate-900">
                    Manajemen Klinik
                </p>

                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Kelola daftar klinik yang terdaftar
                    di dalam sistem.
                </p>

                <p class="mt-4 text-sm font-semibold text-[#B28D32]">
                    Kelola Klinik →
                </p>

            </a>

        </section>

    </div>

</x-layouts::app>