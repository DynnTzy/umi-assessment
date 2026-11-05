<?php

use App\Concerns\PasswordValidationRules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Pengaturan Keamanan')] class extends Component {
    use PasswordValidationRules;

    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    #[Computed]
    public function passwordStrength(): array
    {
        $password = $this->password;

        if ($password === '') {
            return [
                'score' => 0,
                'label' => 'Belum diisi',
            ];
        }

        $score = 0;

        $checks = [
            strlen($password) >= 8,
            preg_match('/[a-z]/', $password),
            preg_match('/[A-Z]/', $password),
            preg_match('/[0-9]/', $password),
            preg_match('/[^a-zA-Z0-9]/', $password),
        ];

        foreach ($checks as $check) {
            if ($check) {
                $score++;
            }
        }

        return [
            'score' => $score,
            'label' => match (true) {
                $score <= 2 => 'Lemah',
                $score <= 4 => 'Sedang',
                default => 'Kuat',
            },
        ];
    }

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => $this->currentPasswordRules(),
                'password' => $this->passwordRules(),
            ]);
        } catch (ValidationException $e) {
            $this->reset(
                'current_password',
                'password',
                'password_confirmation'
            );

            throw $e;
        }

        if (Hash::check(
            $validated['password'],
            Auth::user()->password
        )) {
            $this->addError(
                'password',
                'Password baru tidak boleh sama dengan password saat ini.'
            );

            $this->reset(
                'current_password',
                'password',
                'password_confirmation'
            );

            return;
        }

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset(
            'current_password',
            'password',
            'password_confirmation'
        );

        // Memberi tahu browser bahwa perubahan password berhasil.
        $this->dispatch('password-updated');
    }
}; ?>

<section class="w-full">

    <x-pages::settings.layout>
        <div class="space-y-5">

            {{-- INFORMASI KEAMANAN --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex items-start gap-4">
                    <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                        <flux:icon.shield-check class="size-6" />
                    </div>

                    <div class="min-w-0">
                        <flux:heading size="lg">
                            Keamanan akun
                        </flux:heading>

                        <flux:text class="mt-1 text-sm leading-relaxed text-slate-500">
                            Jaga keamanan akun dengan menggunakan password
                            yang kuat dan tidak mudah ditebak.
                        </flux:text>
                    </div>
                </div>
            </div>

            {{-- FORM UBAH PASSWORD --}}
            <form
                x-data="{
                    passwordUpdated: false,

                    async confirmUpdate() {
                        if (!window.Swal) {
                            await $wire.updatePassword();
                            return;
                        }

                        const result = await window.Swal.fire({
                            title: 'Ubah password?',
                            text: 'Password akun Anda akan diperbarui. Pastikan informasi yang dimasukkan sudah benar.',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, ubah password',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: '#b8954a',
                            cancelButtonColor: '#71717a',
                            reverseButtons: true,
                            focusCancel: true,
                        });

                        if (!result.isConfirmed) {
                            return;
                        }

                        this.passwordUpdated = false;

                        window.Swal.fire({
                            title: 'Memperbarui password...',
                            text: 'Mohon tunggu sebentar.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => window.Swal.showLoading(),
                        });

                        try {
                            await $wire.updatePassword();

                            // Jika validasi gagal, event sukses tidak dikirim.
                            // Tutup dialog loading agar pesan validasi terlihat.
                            if (!this.passwordUpdated) {
                                window.Swal.close();
                            }
                        } catch (error) {
                            window.Swal.close();
                            throw error;
                        }
                    },

                    showSuccessToast() {
                        this.passwordUpdated = true;

                        window.Swal.fire({
                            toast: true,
                            position: 'top',
                            icon: 'success',
                            title: 'Password berhasil diperbarui.',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            background: '#ECFDF5',
                            color: '#065F46',
                            customClass: {
                                popup: 'assessment-success-toast',
                            },
                            didOpen: (toast) => {
                                toast.style.border = '1px solid #A7F3D0';
                                toast.style.borderRadius = '12px';
                                toast.style.boxShadow = '0 8px 24px rgba(6, 95, 70, 0.12)';

                                toast.addEventListener(
                                    'mouseenter',
                                    window.Swal.stopTimer
                                );

                                toast.addEventListener(
                                    'mouseleave',
                                    window.Swal.resumeTimer
                                );
                            },
                        });
                    },
                }"
                x-on:submit.prevent="confirmUpdate()"
                x-on:password-updated.window="showSuccessToast()"
                class="flex min-w-0 flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6"
            >
                {{-- HEADER --}}
                <div class="flex items-start gap-4">
                    <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                        <flux:icon.lock-closed class="size-6" />
                    </div>

                    <div class="min-w-0">
                        <flux:heading size="lg">
                            Ubah password
                        </flux:heading>

                        <flux:text class="mt-1 text-sm leading-relaxed text-slate-500">
                            Masukkan password saat ini, kemudian tentukan
                            password baru untuk akun Anda.
                        </flux:text>
                    </div>
                </div>

                <flux:separator variant="subtle" class="my-5" />

                {{-- INPUT PASSWORD --}}
                <div class="space-y-5">

                    {{-- PASSWORD SAAT INI --}}
                    <div>
                        <flux:input
                            wire:model="current_password"
                            label="Password saat ini"
                            type="password"
                            required
                            autocomplete="current-password"
                            viewable
                        />

                        <p class="mt-1.5 text-xs leading-relaxed text-slate-500">
                            Masukkan password yang sedang Anda gunakan.
                        </p>
                    </div>

                    {{-- PASSWORD BARU --}}
                    <div>
                        <flux:input
                            wire:model.live.debounce.400ms="password"
                            label="Password baru"
                            type="password"
                            required
                            autocomplete="new-password"
                            passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                            viewable
                        />

                        <p class="mt-1.5 text-xs leading-relaxed text-slate-500">
                            Gunakan kombinasi huruf besar, huruf kecil,
                            angka, dan simbol agar password lebih sulit ditebak.
                        </p>

                        {{-- INDIKATOR KEKUATAN PASSWORD --}}
                        @if ($password !== '')
                            <div class="mt-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-xs font-medium text-slate-600">
                                        Kekuatan password
                                    </span>

                                    <span @class([
                                        'text-xs font-semibold',
                                        'text-red-600' => $this->passwordStrength['score'] <= 2,
                                        'text-amber-700' => $this->passwordStrength['score'] >= 3 && $this->passwordStrength['score'] <= 4,
                                        'text-emerald-700' => $this->passwordStrength['score'] === 5,
                                    ])>
                                        {{ $this->passwordStrength['label'] }}
                                    </span>
                                </div>

                                <div class="mt-2 flex gap-1.5">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <div @class([
                                            'h-1.5 flex-1 rounded-full transition-colors',
                                            'bg-red-400' => $this->passwordStrength['score'] >= $i && $this->passwordStrength['score'] <= 2,
                                            'bg-amber-400' => $this->passwordStrength['score'] >= $i && $this->passwordStrength['score'] >= 3 && $this->passwordStrength['score'] <= 4,
                                            'bg-emerald-500' => $this->passwordStrength['score'] >= $i && $this->passwordStrength['score'] === 5,
                                            'bg-slate-200' => $this->passwordStrength['score'] < $i,
                                        ])></div>
                                    @endfor
                                </div>

                                <p class="mt-2 text-xs leading-relaxed text-slate-500">
                                    Indikator ini merupakan perkiraan.
                                    Password tetap harus memenuhi aturan validasi sistem.
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- KONFIRMASI PASSWORD --}}
                    <div>
                        <flux:input
                            wire:model="password_confirmation"
                            label="Konfirmasi password baru"
                            type="password"
                            required
                            autocomplete="new-password"
                            passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                            viewable
                        />

                        <p class="mt-1.5 text-xs leading-relaxed text-slate-500">
                            Masukkan kembali password baru yang sama.
                        </p>
                    </div>

                </div>

                <flux:separator variant="subtle" class="my-5" />

                {{-- TOMBOL SIMPAN --}}
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs leading-relaxed text-slate-500">
                        Pastikan password baru Anda mudah diingat,
                        tetapi sulit ditebak orang lain.
                    </p>

                    <div class="flex justify-end">
                        <flux:button
                            type="submit"
                            class="!border-amber-200 !bg-amber-100 !text-amber-800 hover:!bg-amber-200"
                            wire:loading.attr="disabled"
                            wire:target="updatePassword"
                            data-test="update-password-button"
                        >
                            <span wire:loading.remove wire:target="updatePassword">
                                Simpan perubahan
                            </span>

                            <span wire:loading wire:target="updatePassword">
                                Menyimpan...
                            </span>
                        </flux:button>
                    </div>
                </div>
            </form>

        </div>
    </x-pages::settings.layout>

</section>