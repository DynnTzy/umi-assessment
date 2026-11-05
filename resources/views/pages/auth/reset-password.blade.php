<x-layouts::auth :title="__('Atur Ulang Kata Sandi')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Atur Ulang Kata Sandi')"
            :description="__('Silakan masukkan kata sandi baru Anda')"
        />

        {{-- Session Status --}}
        <x-auth-session-status
            class="text-center"
            :status="session('status')"
        />

        <form
            method="POST"
            action="{{ route('password.update') }}"
            class="flex flex-col gap-6"
        >
            @csrf

            {{-- Token Reset Password --}}
            <input
                type="hidden"
                name="token"
                value="{{ request()->route('token') }}"
            >

            {{-- Email --}}
            <flux:input
                name="email"
                :value="old('email', request('email'))"
                :label="__('Alamat Email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            {{-- Kata Sandi Baru --}}
            <flux:input
                name="password"
                :label="__('Kata Sandi Baru')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Masukkan kata sandi baru')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            {{-- Konfirmasi Kata Sandi --}}
            <flux:input
                name="password_confirmation"
                :label="__('Konfirmasi Kata Sandi')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Ulangi kata sandi baru')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            {{-- Tombol Submit --}}
            <flux:button
                type="submit"
                variant="primary"
                class="w-full"
                data-test="reset-password-button"
            >
                {{ __('Atur Ulang Kata Sandi') }}
            </flux:button>
        </form>
    </div>
</x-layouts::auth>
