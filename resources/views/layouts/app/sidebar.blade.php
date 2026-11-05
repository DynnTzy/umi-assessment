<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-slate-50">

    {{-- =========================================================
        DEFAULT CLINIC ASSESSMENT
    ========================================================== --}}
    @php
        $clinicAssessment = null;

        if (
            auth()->check() &&
            auth()->user()->role === 'klinik' &&
            auth()->user()->clinic_id
        ) {
            $clinicAssessment = \App\Models\Assessment::where(
                'clinic_id',
                auth()->user()->clinic_id
            )
                ->latest()
                ->first();
        }
    @endphp

    {{-- =========================================================
        SIDEBAR
    ========================================================== --}}
    <flux:sidebar
        sticky
        collapsible
        class="app-sidebar !w-[280px] border-e border-slate-200 bg-brand-navy"
    >

        {{-- =====================================================
            BRAND
        ====================================================== --}}
        <flux:sidebar.header class="border-b border-white/10">

            <a
                href="{{ route('dashboard') }}"
                wire:navigate
                class="group flex items-center gap-3 px-2 py-3"
            >
                <div
                    class="h-10 w-10 shrink-0 overflow-hidden rounded-xl bg-brand-gold shadow-lg shadow-black/20 ring-1 ring-white/10"
                >
                    <img
                        src="{{ asset('images/dr-umi-avatar.png') }}"
                        alt="Dr. Umi"
                        class="h-full w-full object-cover"
                    >
                </div>

                <div class="min-w-0">
                    <div class="truncate text-sm font-bold text-white">
                        Dr. Umi
                    </div>

                    <div class="truncate text-[11px] font-medium text-slate-400">
                        Assessment System
                    </div>
                </div>
            </a>

        </flux:sidebar.header>

        {{-- =====================================================
            NAVIGATION
        ====================================================== --}}
        <flux:sidebar.nav class="!px-0 py-4">

            {{-- UTAMA --}}
            <div
                class="mb-2 mt-1 px-3 text-[11px] font-semibold tracking-wider text-slate-400 in-data-flux-sidebar-collapsed-desktop:hidden"
            >
                UTAMA
            </div>

            <flux:sidebar.item
                icon="home"
                :href="route('dashboard')"
                :current="request()->routeIs('dashboard')"
                wire:navigate
                class="app-sidebar-item"
            >
                Dashboard
            </flux:sidebar.item>

            {{-- =================================================
                NAVIGATION BERDASARKAN ROLE
            ================================================== --}}
            @if (auth()->user()->role === 'asesor')

                {{-- ASSESSMENT --}}
                <div
                    class="mb-2 mt-6 px-3 text-[11px] font-semibold tracking-wider text-slate-400 in-data-flux-sidebar-collapsed-desktop:hidden"
                >
                    ASSESSMENT
                </div>

                <flux:sidebar.item
                    icon="clipboard-document-check"
                    :href="route('assessments.index')"
                    :current="request()->routeIs('assessments.index')"
                    wire:navigate
                    class="app-sidebar-item"
                >
                    Daftar Klinik
                </flux:sidebar.item>

                {{-- ADMINISTRASI --}}
                <div
                    class="mb-2 mt-6 px-3 text-[11px] font-semibold tracking-wider text-slate-400 in-data-flux-sidebar-collapsed-desktop:hidden"
                >
                    ADMINISTRASI
                </div>

                <flux:sidebar.item
                    icon="building-office"
                    :href="route('admin.clinics.index')"
                    :current="request()->routeIs('admin.clinics.*')"
                    wire:navigate
                    class="app-sidebar-item"
                >
                    Manajemen Klinik
                </flux:sidebar.item>

            @elseif (auth()->user()->role === 'klinik')

                <div
                    class="mb-2 mt-6 px-3 text-[11px] font-semibold tracking-wider text-slate-400 in-data-flux-sidebar-collapsed-desktop:hidden"
                >
                    ASSESSMENT
                </div>

                @if ($clinicAssessment)
                    <flux:sidebar.item
                        icon="clipboard-document-check"
                        :href="route('assessments.show', $clinicAssessment)"
                        :current="request()->routeIs('assessments.show')"
                        wire:navigate
                        class="app-sidebar-item"
                    >
                        Detail EP
                    </flux:sidebar.item>
                @endif

            @endif

        </flux:sidebar.nav>

        {{-- =====================================================
            SPACER
        ====================================================== --}}
        <flux:spacer />

        {{-- =====================================================
            DESKTOP USER MENU
        ====================================================== --}}
        <div class="border-t border-white/10 p-3">

            <x-desktop-user-menu
                class="hidden lg:block"
                :name="auth()->user()->name"
            />

        </div>

    </flux:sidebar>

    {{-- =========================================================
        DESKTOP HEADER
    ========================================================== --}}
    <x-layouts::app.header
        :title="$title ?? 'Dashboard'"
    />

    {{-- =========================================================
        MOBILE HEADER
    ========================================================== --}}
    <flux:header class="border-b border-slate-200 bg-white lg:hidden">

        <flux:sidebar.toggle
            class="lg:hidden"
            icon="bars-2"
            inset="left"
        />

        <flux:spacer />

        <flux:dropdown position="top" align="end">

            <flux:profile
                :initials="auth()->user()->initials()"
                icon-trailing="chevron-down"
            />

            <flux:menu>

                {{-- User Information --}}
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start">

                            <flux:avatar
                                :name="auth()->user()->name"
                                :initials="auth()->user()->initials()"
                            />

                            <div class="grid min-w-0 flex-1 text-start text-sm leading-tight">

                                <flux:heading class="truncate">
                                    {{ auth()->user()->name }}
                                </flux:heading>

                                <flux:text class="truncate">
                                    {{ auth()->user()->email }}
                                </flux:text>

                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                {{-- Settings --}}
                <flux:menu.radio.group>
                    <flux:menu.item
                        :href="route('profile.edit')"
                        icon="cog"
                        wire:navigate
                    >
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                {{-- Logout --}}
                <form
                    method="POST"
                    action="{{ route('logout') }}"
                    class="w-full"
                >
                    @csrf

                    <flux:menu.item
                        as="button"
                        type="submit"
                        icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer"
                        data-test="logout-button"
                    >
                        {{ __('Log out') }}
                    </flux:menu.item>
                </form>

            </flux:menu>

        </flux:dropdown>

    </flux:header>

    {{-- =========================================================
        MAIN CONTENT
    ========================================================== --}}
    <flux:main class="app-content">

        <div class="app-container">
            {{ $slot }}
        </div>

    </flux:main>

    {{-- =========================================================
        TOAST
    ========================================================== --}}
    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    {{-- =========================================================
        SCRIPTS
    ========================================================== --}}
    @fluxScripts

    @stack('scripts')

</body>
</html>
