<div class="sidebar-user-menu w-full min-w-0">
    <flux:dropdown position="top" align="start">

        {{-- =====================================================
            SIDEBAR USER PROFILE
        ====================================================== --}}
        <button
            type="button"
            data-flux-sidebar-profile
            data-test="sidebar-menu-button"
            class="sidebar-user-trigger flex w-full min-w-0 items-center gap-2.5 rounded-xl border border-transparent px-2 py-2 text-left transition duration-150 hover:border-white/10 hover:bg-white/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-gold"
            aria-label="Open account menu"
        >
            {{-- User Icon --}}
            <span
                class="sidebar-user-icon flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/[0.07] text-slate-300 transition-colors duration-150"
                aria-hidden="true"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <circle cx="12" cy="8" r="4" />
                    <path d="M5 21v-2a7 7 0 0 1 14 0v2" />
                </svg>
            </span>

            {{-- User Information --}}
            <span class="flex min-w-0 flex-1 flex-col overflow-hidden">
                <span
                    class="block w-full truncate text-sm font-semibold leading-5 text-slate-200"
                    title="{{ auth()->user()->name }}"
                >
                    {{ auth()->user()->name }}
                </span>

                <span class="mt-0.5 block truncate text-xs leading-4 text-slate-400">
                    Account settings
                </span>
            </span>

            {{-- Dropdown Indicator --}}
            <span
                class="shrink-0 text-slate-500 transition-colors duration-150"
                aria-hidden="true"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="15"
                    height="15"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="m7 10 5 5 5-5" />
                </svg>
            </span>
        </button>

        {{-- =====================================================
            DROPDOWN MENU
        ====================================================== --}}
        <flux:menu class="w-64 max-w-[calc(100vw-2rem)]">

            {{-- User Information --}}
            <div class="flex min-w-0 items-center gap-3 px-2 py-2.5">

                {{-- User Icon --}}
                <span
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600"
                    aria-hidden="true"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <circle cx="12" cy="8" r="4" />
                        <path d="M5 21v-2a7 7 0 0 1 14 0v2" />
                    </svg>
                </span>

                <div class="min-w-0 flex-1 overflow-hidden">
                    <div
                        class="truncate text-sm font-semibold text-slate-900"
                        title="{{ auth()->user()->name }}"
                    >
                        {{ auth()->user()->name }}
                    </div>

                    <div
                        class="mt-0.5 truncate text-xs text-slate-500"
                        title="{{ auth()->user()->email }}"
                    >
                        {{ auth()->user()->email }}
                    </div>
                </div>

            </div>

            <flux:menu.separator />

            {{-- Settings --}}
            <flux:menu.item
                :href="route('profile.edit')"
                icon="cog"
                wire:navigate
            >
                {{ __('Settings') }}
            </flux:menu.item>

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
</div>