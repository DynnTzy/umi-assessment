<x-layouts::auth :title="__('Login')">
    <div class="flex flex-col gap-7">
        {{-- HEADER --}}
        <div class="space-y-2 text-center">
            <h1 class="text-3xl font-bold tracking-tight text-slate-800">
                Login
            </h1>

            <p class="text-sm text-slate-400">
                Masukkan e-mail dan password Anda
            </p>
        </div>

        {{-- SESSION STATUS --}}
        <x-auth-session-status
            class="text-center"
            :status="session('status')"
        />

        {{-- LOGIN ERROR --}}
        @if ($errors->has('email'))
            <div
                role="alert"
                class="flex items-start gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="mt-0.5 size-5 shrink-0"
                    aria-hidden="true"
                >
                    <circle cx="12" cy="12" r="10" />
                    <path d="M12 8v4" />
                    <path d="M12 16h.01" />
                </svg>

                <span>{{ $errors->first('email') }}</span>
            </div>
        @endif

        {{-- LOGIN FORM --}}
        <form
            method="POST"
            action="{{ route('login.store') }}"
            class="flex flex-col gap-5"
        >
            @csrf

            {{-- EMAIL --}}
            <div class="relative">
                <flux:input
                    name="email"
                    type="email"
                    :value="old('email')"
                    placeholder="Masukkan alamat e-mail"
                    required
                    autofocus
                    autocomplete="email"
                    aria-label="Alamat e-mail"
                    class="auth-input"
                />
            </div>

            {{-- PASSWORD --}}
            <div class="flex flex-col gap-2">
                <div class="relative">
                    <flux:input
                        name="password"
                        type="password"
                        placeholder="Masukkan password"
                        required
                        autocomplete="current-password"
                        viewable
                        aria-label="Password"
                        class="auth-input"
                    />
                </div>

                @if (Route::has('password.request'))
                    <div class="flex justify-end">
                        <flux:link
                            :href="route('password.request')"
                            wire:navigate
                            class="text-sm font-medium text-[#C9A646] hover:text-[#A9852D]"
                        >
                            Lupa Password?
                        </flux:link>
                    </div>
                @endif
            </div>

            {{-- REMEMBER ME --}}
            <flux:checkbox
                name="remember"
                label="Ingat saya"
                :checked="old('remember')"
            />

            {{-- LOGIN BUTTON --}}
            <flux:button
                variant="primary"
                type="submit"
                class="!h-14 !w-full !rounded-xl !border-[#C9A646] !bg-[#C9A646] !text-base !font-semibold !text-white !shadow-md hover:!border-[#B28D32] hover:!bg-[#B28D32]"
                data-test="login-button"
            >
                <span class="inline-flex items-center justify-center gap-2">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="size-5"
                        aria-hidden="true"
                    >
                        <path d="M10 17l5-5-5-5" />
                        <path d="M15 12H3" />
                        <path d="M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6" />
                    </svg>

                    Login
                </span>
            </flux:button>
        </form>

        {{-- REGISTER LINK --}}
        @if (Route::has('register'))
            <p class="text-center text-sm text-slate-500">
                Belum memiliki akun?

                <flux:link
                    :href="route('register')"
                    wire:navigate
                    class="font-medium text-[#C9A646] hover:text-[#A9852D]"
                >
                    Daftar sekarang
                </flux:link>
            </p>
        @endif
    </div>
</x-layouts::auth>
