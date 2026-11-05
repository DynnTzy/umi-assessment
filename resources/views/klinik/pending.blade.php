
<x-layouts::app :title="'Menunggu Penetapan Klinik'">

    <div class="flex min-h-[calc(100vh-8rem)] items-center justify-center px-4 py-8 sm:px-6">

        <div class="w-full max-w-xl">

            {{-- Main Card --}}
            <section class="rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-sm sm:p-10">

                {{-- Icon --}}
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-amber-50 text-[#B28D32]">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.7"
                        stroke="currentColor"
                        class="h-9 w-9"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 6v6l4 2"
                        />
                        <circle cx="12" cy="12" r="9" />
                    </svg>
                </div>

                {{-- Heading --}}
                <p class="mt-6 text-sm font-semibold text-[#B28D32]">
                    Akun Berhasil Diverifikasi
                </p>

                <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    Menunggu Penetapan Klinik
                </h2>

                {{-- Description --}}
                <p class="mx-auto mt-4 max-w-md text-sm leading-6 text-slate-500">
                    Akun Anda sudah berhasil dibuat dan diverifikasi.
                    Saat ini akun Anda belum terhubung dengan klinik
                    tertentu.
                </p>

                <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-slate-500">
                    Silakan menunggu administrator menetapkan akun Anda
                    ke klinik yang sesuai. Setelah penetapan dilakukan,
                    Anda dapat mengakses assessment klinik melalui sistem.
                </p>

                {{-- Status --}}
                <div class="mt-7 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-left">
                    <div class="flex items-start gap-3">

                        <div class="mt-0.5 shrink-0 text-amber-600">
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
                                    d="M12 9v3.75m0 3h.008M10.343 3.94 2.94 17.06A1.5 1.5 0 0 0 4.25 19.25h15.5a1.5 1.5 0 0 0 1.31-2.19L13.657 3.94a1.5 1.5 0 0 0-2.628 0Z"
                                />
                            </svg>
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-amber-800">
                                Status akun
                            </p>
                            <p class="mt-1 text-sm leading-5 text-amber-700">
                                Menunggu administrator menetapkan klinik.
                            </p>
                        </div>

                    </div>
                </div>

                {{-- Account Information --}}
                <div class="mt-7 border-t border-slate-200 pt-6">

                    <div class="flex flex-col gap-2 text-sm sm:flex-row sm:items-center sm:justify-between">
                        <span class="text-slate-400">Akun</span>
                        <span class="break-all font-medium text-slate-700">
                            {{ auth()->user()->email }}
                        </span>
                    </div>

                    <div class="mt-4 flex flex-col gap-2 text-sm sm:flex-row sm:items-center sm:justify-between">
                        <span class="text-slate-400">Status</span>
                        <span class="inline-flex items-center justify-center self-start rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 sm:self-auto">
                            Menunggu Penetapan
                        </span>
                    </div>

                </div>

            </section>

            {{-- Information --}}
            <p class="mt-6 text-center text-xs leading-5 text-slate-400">
                Jika Anda sudah seharusnya terhubung dengan klinik,
                silakan hubungi administrator sistem.
            </p>

        </div>

    </div>

</x-layouts::app>
