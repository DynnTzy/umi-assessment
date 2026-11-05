<div class="mx-auto w-full max-w-[1440px] p-4 sm:p-6 lg:p-8">
    <div class="grid grid-cols-1 items-start gap-5 md:grid-cols-[200px_minmax(0,1fr)] lg:gap-6">

        {{-- Navigasi Pengaturan --}}
        <aside class="min-w-0">
            <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                <flux:navlist aria-label="Navigasi pengaturan">
                    <flux:navlist.item
                        :href="route('profile.edit')"
                        :current="request()->routeIs('profile.edit')"
                        wire:navigate
                    >
                        Profil
                    </flux:navlist.item>

                    <flux:navlist.item
                        :href="route('security.edit')"
                        :current="request()->routeIs('security.edit')"
                        wire:navigate
                    >
                        Keamanan
                    </flux:navlist.item>
                </flux:navlist>
            </div>
        </aside>

        {{-- Konten Pengaturan --}}
        <main class="min-w-0">
            @if (! empty($heading) || ! empty($subheading))
                <div class="mb-5">
                    @if (! empty($heading))
                        <flux:heading size="lg">
                            {{ $heading }}
                        </flux:heading>
                    @endif

                    @if (! empty($subheading))
                        <flux:text class="mt-1 text-sm text-slate-500">
                            {{ $subheading }}
                        </flux:text>
                    @endif
                </div>
            @endif

            {{ $slot }}
        </main>

    </div>
</div>
