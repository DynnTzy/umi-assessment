<x-layouts::auth :title="__('Verifikasi Email')">
    <div class="flex flex-col gap-7 text-center">

        {{-- Ikon --}}
        <div class="flex justify-center">
            <div class="flex size-16 items-center justify-center rounded-2xl border border-amber-200 bg-amber-50 text-amber-600 shadow-sm">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="size-8"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <rect x="3" y="5" width="18" height="14" rx="2" />
                    <path d="m3 7 9 6 9-6" />
                    <path d="m16 16 2 2 4-4" />
                </svg>
            </div>
        </div>

        {{-- Header --}}
        <div class="flex flex-col gap-3">
            <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">
                Verifikasi Alamat Email
            </h1>

            <p class="text-sm leading-6 text-zinc-500">
                Satu langkah lagi untuk mulai menggunakan
                <span class="font-medium text-zinc-700">Dr. Umi Assessment</span>.
                Silakan verifikasi alamat email Anda melalui tautan yang telah kami kirimkan.
            </p>
        </div>

        {{-- Informasi --}}
        <div class="rounded-xl border border-amber-200/80 bg-amber-50/70 p-4 text-left">
            <div class="flex items-start gap-3">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="mt-0.5 size-5 shrink-0 text-amber-600"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <circle cx="12" cy="12" r="10" />
                    <path d="M12 8v4" />
                    <path d="M12 16h.01" />
                </svg>

                <div class="space-y-1">
                    <p class="text-sm font-medium text-zinc-800">
                        Periksa kotak masuk email Anda
                    </p>
                    <p class="text-xs leading-5 text-zinc-600">
                        Jika email tidak ditemukan, periksa juga folder spam atau promosi.
                    </p>
                </div>
            </div>
        </div>

        {{-- Status pengiriman ulang --}}
        @if (session('status') == 'verification-link-sent')
            <div
                role="status"
                class="rounded-xl border border-green-200 bg-green-50 p-4 text-left"
            >
                <div class="flex items-start gap-3">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="mt-0.5 size-5 shrink-0 text-green-600"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="m5 12 4 4L19 6" />
                    </svg>

                    <div class="space-y-1">
                        <p class="text-sm font-medium text-green-800">
                            Email verifikasi berhasil dikirim
                        </p>
                        <p class="text-xs leading-5 text-green-700">
                            Tautan verifikasi baru telah dikirim ke alamat email Anda.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Tombol kirim ulang --}}
        <div class="flex flex-col gap-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <flux:button
                    type="submit"
                    variant="primary"
                    class="w-full"
                >
                    Kirim Ulang Email Verifikasi
                </flux:button>
            </form>

            <p class="text-xs leading-5 text-zinc-500">
                Belum menerima email? Anda dapat meminta tautan verifikasi baru.
            </p>

            <div class="border-t border-zinc-100 pt-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <flux:button
                        variant="ghost"
                        type="submit"
                        class="w-full cursor-pointer text-sm text-zinc-500 hover:text-zinc-800"
                        data-test="logout-button"
                    >
                        <span class="inline-flex items-center gap-2">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="size-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                <path d="M16 17l5-5-5-5" />
                                <path d="M21 12H9" />
                            </svg>
                            Keluar dari akun
                        </span>
                    </flux:button>
                </form>
            </div>
        </div>
    </div>
</x-layouts::auth>
