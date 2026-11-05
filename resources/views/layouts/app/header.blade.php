@props([
    'title' => 'Dashboard',
    'breadcrumb' => null,
])

{{-- =========================================================
    SHARED DESKTOP HEADER
========================================================== --}}
<flux:header
    class="max-lg:hidden !min-h-[88px] border-b border-slate-200 bg-white px-6 py-4 lg:px-8"
>

    {{-- =====================================================
        LEFT
    ====================================================== --}}
    <div class="flex min-w-0 items-center gap-4">

        {{-- Sidebar Toggle --}}
        <flux:sidebar.collapse
            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition hover:bg-slate-200 hover:text-slate-800"
            tooltip="Toggle sidebar"
        />

        {{-- Page Information --}}
        <div class="min-w-0">

            <h1 class="truncate text-lg font-semibold leading-tight text-slate-900">
                {{ $title }}
            </h1>

            <div class="mt-1 flex items-center gap-2 text-xs text-slate-400">

                <span class="truncate">
                    Dr. Umi Assessment
                </span>

                <span>
                    ›
                </span>

                <span class="truncate">
                    {{ $breadcrumb ?? $title }}
                </span>

            </div>

        </div>

    </div>

    {{-- =====================================================
        SPACER
    ====================================================== --}}
    <flux:spacer />

    {{-- =====================================================
        RIGHT / USER
    ====================================================== --}}
    <div class="flex items-center gap-3">

        <div class="flex items-center gap-3">

            {{-- Avatar --}}
            <div class="h-11 w-11 shrink-0 overflow-hidden rounded-xl bg-brand-gold shadow-sm">
                @if (auth()->user()->profile_photo_path)
                    <img
                        src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url(auth()->user()->profile_photo_path) }}"
                        alt="Foto profil {{ auth()->user()->name }}"
                        class="h-full w-full object-cover"
                    >
                @else
                    <div class="flex h-full w-full items-center justify-center text-sm font-bold text-white">
                        {{ auth()->user()->initials() }}
                    </div>
                @endif
            </div>

            {{-- User Information --}}
            <div class="hidden min-w-0 sm:block">

                <p class="truncate text-sm font-semibold text-slate-900">
                    {{ auth()->user()->name }}
                </p>

                <p class="mt-0.5 truncate text-[11px] font-medium text-slate-400">
                    {{ auth()->user()->email }}
                </p>

            </div>

        </div>

    </div>

</flux:header>
