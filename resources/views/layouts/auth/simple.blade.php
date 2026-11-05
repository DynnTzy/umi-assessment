<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>

    <body class="min-h-screen bg-white antialiased text-slate-900">
        <div class="grid min-h-svh lg:grid-cols-[62%_38%]">

            {{-- LEFT PANEL: BRANDING --}}
            <section class="relative hidden overflow-hidden bg-[#17170F] lg:flex lg:flex-col lg:items-center lg:justify-center lg:px-12">
                <div class="absolute inset-0 bg-gradient-to-br from-[#302712] via-[#17170F] to-black"></div>

                <div class="relative z-10 flex max-w-xl flex-col items-center text-center">
                    <a
                        href="{{ route('home') }}"
                        class="flex flex-col items-center gap-7"
                        wire:navigate
                    >
                        <div class="flex size-36 items-center justify-center overflow-hidden rounded-full border-4 border-[#C9A646] bg-[#C9A646] shadow-[0_0_40px_rgba(201,166,70,0.2)]">
                            <img
                                src="{{ asset('images/dr-umi-avatar.png') }}"
                                alt="Dr. Umi"
                                class="h-full w-full object-cover"
                            >
                        </div>

                        <h1 class="text-5xl font-extrabold tracking-[0.10em] text-[#C9A646]">
                            Dr. Umi
                            <span class="mt-2 block text-3xl tracking-[0.20em]">
                                ASSESSMENT
                            </span>
                        </h1>

                        <span class="h-1 w-20 rounded-full bg-[#C9A646]"></span>

                        <p class="max-w-md text-sm leading-7 tracking-[0.12em] text-slate-300 sm:text-base">
                            SISTEM PENGELOLAAN DOKUMEN
                            <br>
                            AKREDITASI KLINIK
                        </p>
                    </a>
                </div>

                <p class="absolute bottom-8 text-xs text-slate-500">
                    &copy; {{ date('Y') }} Dr. Umi Assessment
                </p>
            </section>

            {{-- RIGHT PANEL: AUTHENTICATION --}}
            <section class="flex min-h-svh items-center justify-center px-5 py-10 sm:px-10 lg:px-12">
                <div class="w-full max-w-[400px]">

                    {{-- MOBILE BRANDING --}}
                    <a
                        href="{{ route('home') }}"
                        class="mb-8 flex flex-col items-center gap-3 text-center lg:hidden"
                        wire:navigate
                    >
                        <img
                            src="{{ asset('images/dr-umi-avatar.png') }}"
                            alt="Dr. Umi"
                            class="size-20 rounded-full border-2 border-[#C9A646] object-cover"
                        >

                        <span class="text-lg font-bold text-slate-900">
                            Dr. Umi Assessment
                        </span>

                        <span class="text-sm text-slate-500">
                            Sistem Pengelolaan Dokumen Akreditasi Klinik
                        </span>
                    </a>

                    {{-- AUTHENTICATION CONTENT --}}
                    <main>
                        {{ $slot }}
                    </main>

                    {{-- MOBILE FOOTER --}}
                    <p class="mt-8 text-center text-xs text-slate-400 lg:hidden">
                        &copy; {{ date('Y') }} Dr. Umi Assessment
                    </p>
                </div>
            </section>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
