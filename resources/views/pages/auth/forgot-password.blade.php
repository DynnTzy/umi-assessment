<x-layouts::auth :title="__('Lupa Password')">
    <div class="flex flex-col gap-7">

        {{-- HEADER --}}
        <div class="space-y-2 text-center">
            <h1 class="text-3xl font-bold tracking-tight text-slate-800">
                Lupa Password?
            </h1>

            <p class="text-sm leading-6 text-slate-400">
                Masukkan e-mail Anda untuk menerima tautan reset password
            </p>
        </div>

        {{-- SESSION STATUS --}}
        <x-auth-session-status
            class="text-center"
            :status="session('status')"
        />

        {{-- PASSWORD RESET FORM --}}
        <form
            method="POST"
            action="{{ route('password.email') }}"
            class="flex flex-col gap-5"
        >
            @csrf

            {{-- EMAIL --}}
            <flux:input
                name="email"
                :label="__('Alamat E-mail')"
                type="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="email"
                placeholder="nama@contoh.com"
                :error="$errors->first('email')"
            />

            {{-- SUBMIT BUTTON --}}
            <flux:button
                variant="primary"
                type="submit"
                class="!h-14 !w-full !rounded-xl !border-[#C9A646] !bg-[#C9A646] !text-base !font-semibold !text-white hover:!border-[#B28D32] hover:!bg-[#B28D32]"
                data-test="email-password-reset-link-button"
            >
                Kirim Tautan Reset Password
            </flux:button>
        </form>

        {{-- BACK TO LOGIN --}}
        <p class="text-center text-sm text-slate-400">
            Atau, kembali ke
            <flux:link
                :href="route('login')"
                wire:navigate
                class="font-medium text-[#C9A646] hover:text-[#A9852D]"
            >
                Login
            </flux:link>
        </p>
    </div>
</x-layouts::auth>
