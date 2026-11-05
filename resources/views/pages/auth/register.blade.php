<x-layouts::auth :title="__('Daftar Akun')">
    <div class="flex flex-col gap-7">

        {{-- HEADER --}}
        <div class="space-y-2 text-center">
            <h1 class="text-3xl font-bold tracking-tight text-slate-800">
                Daftar Akun
            </h1>

            <p class="text-sm text-slate-400">
                Lengkapi data berikut untuk membuat akun Anda
            </p>
        </div>

        {{-- SESSION STATUS --}}
        <x-auth-session-status
            class="text-center"
            :status="session('status')"
        />

        {{-- REGISTER FORM --}}
        <form
            method="POST"
            action="{{ route('register.store') }}"
            class="flex flex-col gap-5"
        >
            @csrf

            {{-- NAME --}}
            <flux:input
                name="name"
                type="text"
                :label="__('Nama Lengkap')"
                :value="old('name')"
                placeholder="Masukkan nama lengkap"
                required
                autofocus
                autocomplete="name"
                :error="$errors->first('name')"
            />

            {{-- EMAIL --}}
            <flux:input
                name="email"
                type="email"
                :label="__('Alamat E-mail')"
                :value="old('email')"
                placeholder="nama@contoh.com"
                required
                autocomplete="email"
                :error="$errors->first('email')"
            />

            {{-- PASSWORD --}}
            <flux:input
                name="password"
                type="password"
                :label="__('Password')"
                placeholder="Masukkan password"
                required
                autocomplete="new-password"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
                :error="$errors->first('password')"
            />

            {{-- CONFIRM PASSWORD --}}
            <flux:input
                name="password_confirmation"
                type="password"
                :label="__('Konfirmasi Password')"
                placeholder="Masukkan ulang password"
                required
                autocomplete="new-password"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
                :error="$errors->first('password')"
            />

            {{-- REGISTER BUTTON --}}
            <flux:button
                type="submit"
                variant="primary"
                class="!h-14 !w-full !rounded-xl !border-[#C9A646] !bg-[#C9A646] !text-base !font-semibold !text-white hover:!border-[#B28D32] hover:!bg-[#B28D32]"
                data-test="register-user-button"
            >
                Buat Akun
            </flux:button>
        </form>

        {{-- LOGIN LINK --}}
        <p class="text-center text-sm text-slate-500">
            Sudah memiliki akun?
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
