<?php

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Pengaturan Profil')] class extends Component {
    use ProfileValidationRules;
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public $photo = null;

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfilePhoto(): void
    {
        $validated = $this->validate([
            'photo' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        $user = Auth::user();
        $oldPhoto = $user->profile_photo_path;

        $path = $validated['photo']->store(
            'profile-photos',
            'public'
        );

        $user->profile_photo_path = $path;
        $user->save();

        if ($oldPhoto) {
            Storage::disk('public')->delete($oldPhoto);
        }

        $this->reset('photo');

        $this->dispatch('profile-photo-updated');
    }

    public function deleteProfilePhoto(): void
    {
        $user = Auth::user();
        $oldPhoto = $user->profile_photo_path;

        if (! $oldPhoto) {
            return;
        }

        $user->profile_photo_path = null;
        $user->save();

        Storage::disk('public')->delete($oldPhoto);

        $this->reset('photo');

        $this->dispatch('profile-photo-deleted');
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate(
            $this->profileRules($user->id)
        );

        $emailChanged = $user->email !== $validated['email'];

        $user->fill($validated);

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
        }

        $this->dispatch('profile-information-updated');
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(
                default: route('dashboard', absolute: false)
            );

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail
            && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (
                Auth::user() instanceof MustVerifyEmail
                && Auth::user()->hasVerifiedEmail()
            );
    }
}; ?>

<section class="w-full">
    <x-pages::settings.layout>
        <div class="space-y-5">

            {{-- FOTO PROFIL DAN INFORMASI AKUN --}}
            <div class="grid grid-cols-1 items-stretch gap-5 xl:grid-cols-2">

                {{-- KARTU FOTO PROFIL --}}
                <div
                    class="flex h-full min-w-0 flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6"
                >
                    {{-- Header --}}
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <flux:heading size="lg">
                                Foto profil
                            </flux:heading>

                            <flux:subheading class="mt-1">
                                Foto yang ditampilkan pada akun Anda.
                            </flux:subheading>
                        </div>

                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                            <flux:icon name="camera" class="size-5" />
                        </div>
                    </div>

                    <flux:separator variant="subtle" class="my-5" />

                    {{-- Isi kartu --}}
                    <div class="flex flex-1 flex-col gap-5 sm:flex-row sm:items-stretch">

                        {{-- Pratinjau --}}
                        <div class="flex shrink-0 flex-col items-center gap-2 sm:w-32">
                            <div class="rounded-full bg-amber-50 p-1 ring-1 ring-amber-200">
                                @if ($photo)
                                    <img
                                        src="{{ $photo->temporaryUrl() }}"
                                        alt="Pratinjau foto profil"
                                        class="size-28 rounded-full object-cover"
                                    >
                                @elseif (Auth::user()->profile_photo_path)
                                    <img
                                        src="{{ Storage::disk('public')->url(Auth::user()->profile_photo_path) }}"
                                        alt="Foto profil"
                                        class="size-28 rounded-full object-cover"
                                    >
                                @else
                                    <div class="flex size-28 items-center justify-center rounded-full bg-brand-gold text-2xl font-semibold text-white">
                                        {{ Auth::user()->initials() }}
                                    </div>
                                @endif
                            </div>

                            <span class="text-xs font-medium text-slate-500">
                                Pratinjau foto
                            </span>
                        </div>

                        {{-- Form foto profil dan SweetAlert2 --}}
                        <form
                            x-data="{
                                actionSucceeded: false,

                                cropper: null,
                                cropImageUrl: null,
                                cropModalOpen: false,
                                cropUploading: false,

                                openCropper(event) {
                                    const input = event.target;
                                    const file = input.files?.[0];

                                    if (!file) return;

                                    input.value = '';

                                    const allowedTypes = [
                                        'image/jpeg',
                                        'image/png',
                                        'image/webp',
                                    ];

                                    if (!allowedTypes.includes(file.type)) {
                                        window.Swal?.fire({
                                            icon: 'error',
                                            title: 'Format tidak didukung',
                                            text: 'Pilih foto JPG, PNG, atau WebP.',
                                        });

                                        return;
                                    }

                                    if (file.size > 2 * 1024 * 1024) {
                                        window.Swal?.fire({
                                            icon: 'error',
                                            title: 'Foto terlalu besar',
                                            text: 'Ukuran foto maksimal 2 MB.',
                                        });

                                        return;
                                    }

                                    this.closeCropper();

                                    this.cropImageUrl = URL.createObjectURL(file);
                                    this.cropModalOpen = true;
                                },

                                initCropper() {
                                    if (
                                        !this.cropModalOpen ||
                                        !this.$refs.cropImage ||
                                        !window.Cropper
                                    ) {
                                        return;
                                    }

                                    this.destroyCropper();

                                    this.cropper = new window.Cropper(
                                        this.$refs.cropImage,
                                        {
                                            aspectRatio: 1,
                                            viewMode: 1,
                                            dragMode: 'move',
                                            autoCropArea: 0.8,
                                            responsive: true,
                                            background: false,
                                            guides: true,
                                            movable: true,
                                            zoomable: true,
                                            zoomOnWheel: true,
                                            rotatable: false,
                                            scalable: false,
                                        }
                                    );
                                },

                                async useCroppedPhoto() {
                                    if (!this.cropper || this.cropUploading) {
                                        return;
                                    }

                                    const canvas = this.cropper.getCroppedCanvas({
                                        width: 512,
                                        height: 512,
                                        imageSmoothingEnabled: true,
                                        imageSmoothingQuality: 'high',
                                    });

                                    if (!canvas) {
                                        window.Swal?.fire({
                                            icon: 'error',
                                            title: 'Gagal memotong foto',
                                            text: 'Silakan coba pilih foto lain.',
                                        });

                                        return;
                                    }

                                    this.cropUploading = true;

                                    canvas.toBlob((blob) => {
                                        if (!blob) {
                                            this.cropUploading = false;

                                            window.Swal?.fire({
                                                icon: 'error',
                                                title: 'Gagal memproses foto',
                                                text: 'Silakan coba kembali.',
                                            });

                                            return;
                                        }

                                        const croppedFile = new File(
                                            [blob],
                                            'profile-photo.jpg',
                                            { type: 'image/jpeg' }
                                        );

                                        this.$wire.upload(
                                            'photo',
                                            croppedFile,
                                            () => {
                                                this.cropUploading = false;
                                                this.closeCropper();
                                            },
                                            () => {
                                                this.cropUploading = false;

                                                window.Swal?.fire({
                                                    icon: 'error',
                                                    title: 'Upload gagal',
                                                    text: 'Foto gagal diunggah. Silakan coba lagi.',
                                                });
                                            },
                                        );
                                    }, 'image/jpeg', 0.9);
                                },

                                destroyCropper() {
                                    if (this.cropper) {
                                        this.cropper.destroy();
                                        this.cropper = null;
                                    }
                                },

                                closeCropper() {
                                    this.destroyCropper();

                                    if (this.cropImageUrl) {
                                        URL.revokeObjectURL(this.cropImageUrl);
                                        this.cropImageUrl = null;
                                    }

                                    this.cropModalOpen = false;
                                },

                                async executeAction(config) {
                                    if (!window.Swal) {
                                        await config.action();
                                        return;
                                    }

                                    const result = await window.Swal.fire({
                                        title: config.title,
                                        text: config.text,
                                        icon: config.icon || 'question',
                                        showCancelButton: true,
                                        confirmButtonText: config.confirmButtonText,
                                        cancelButtonText: 'Batal',
                                        confirmButtonColor: config.confirmColor || '#b8954a',
                                        cancelButtonColor: '#71717a',
                                        reverseButtons: true,
                                        focusCancel: true,
                                    });

                                    if (!result.isConfirmed) {
                                        return;
                                    }

                                    this.actionSucceeded = false;

                                    window.Swal.fire({
                                        title: config.loadingTitle,
                                        text: config.loadingText || 'Mohon tunggu sebentar.',
                                        allowOutsideClick: false,
                                        allowEscapeKey: false,
                                        showConfirmButton: false,
                                        didOpen: () => window.Swal.showLoading(),
                                    });

                                    try {
                                        await config.action();

                                        if (!this.actionSucceeded) {
                                            window.Swal.close();
                                        }
                                    } catch (error) {
                                        window.Swal.close();
                                        throw error;
                                    }
                                },

                                showSuccessToast(title) {
                                    this.actionSucceeded = true;

                                    if (!window.Swal) {
                                        return;
                                    }

                                    window.Swal.fire({
                                        toast: true,
                                        position: 'top',
                                        icon: 'success',
                                        title: title,
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
                            x-on:submit.prevent="executeAction({
                                title: 'Simpan foto profil?',
                                text: 'Foto profil Anda akan diperbarui menggunakan gambar yang dipilih.',
                                confirmButtonText: 'Ya, simpan foto',
                                loadingTitle: 'Mengunggah foto...',
                                loadingText: 'Mohon tunggu, foto profil sedang diperbarui.',
                                action: () => $wire.updateProfilePhoto()
                            })"
                            x-on:profile-photo-updated.window="showSuccessToast('Foto profil berhasil diperbarui.')"
                            x-on:profile-photo-deleted.window="showSuccessToast('Foto profil berhasil dihapus.')"
                            class="flex min-w-0 flex-1 flex-col"
                        >
                            <div>
                                <p class="text-sm font-semibold text-slate-800">
                                    Perbarui foto Anda
                                </p>

                                <p class="mt-1 text-sm leading-relaxed text-slate-500">
                                    Pilih foto yang jelas agar akun mudah dikenali.
                                </p>
                            </div>

                            {{-- Area pilih foto --}}
                            <div class="mt-4">
                                <input
                                    id="profile-photo"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                    x-on:change="openCropper($event)"
                                    class="sr-only"
                                    aria-label="Pilih foto profil"
                                >

                                <label
                                    for="profile-photo"
                                    class="flex min-h-40 cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center transition hover:border-amber-400 hover:bg-amber-50/50"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.6"
                                        class="mb-3 size-8 text-amber-600"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M12 16V4m0 0L7 9m5-5 5 5M4 16v3a1 1 0 001 1h14a1 1 0 001-1v-3"
                                        />
                                    </svg>

                                    <span class="text-sm font-semibold text-slate-800">
                                        Pilih foto profil
                                    </span>

                                    <span class="mt-1 text-xs leading-relaxed text-slate-500">
                                        JPG, PNG, atau WebP
                                    </span>

                                    <span class="text-xs text-slate-500">
                                        Maksimal 2 MB
                                    </span>
                                </label>

                                <div
                                    wire:loading
                                    wire:target="photo"
                                    class="mt-2 text-xs font-medium text-amber-700"
                                >
                                    Mengunggah hasil crop...
                                </div>
                            </div>

                            @error('photo')
                                <flux:text class="mt-2 text-sm !text-red-600">
                                    {{ $message }}
                                </flux:text>
                            @enderror

                            {{-- Tombol aksi foto --}}
                            <div class="mt-auto flex flex-wrap gap-2 pt-5">
                                <flux:button
                                    type="submit"
                                    class="!min-h-11 !border-amber-200 !bg-amber-100 !text-amber-800 hover:!bg-amber-200"
                                    wire:loading.attr="disabled"
                                    wire:target="photo,updateProfilePhoto"
                                >
                                    Simpan foto
                                </flux:button>

                                @if (Auth::user()->profile_photo_path)
                                    <flux:button
                                        type="button"
                                        class="!min-h-11 !border-red-200 !bg-red-50 !text-red-700 hover:!bg-red-100"
                                        x-on:click.prevent="executeAction({
                                            title: 'Hapus foto profil?',
                                            text: 'Foto profil Anda akan dihapus. Tindakan ini tidak dapat dibatalkan.',
                                            icon: 'warning',
                                            confirmButtonText: 'Ya, hapus foto',
                                            confirmColor: '#DC2626',
                                            loadingTitle: 'Menghapus foto...',
                                            loadingText: 'Mohon tunggu, foto profil sedang dihapus.',
                                            action: () => $wire.deleteProfilePhoto()
                                        })"
                                        wire:loading.attr="disabled"
                                        wire:target="deleteProfilePhoto"
                                    >
                                        Hapus foto
                                    </flux:button>
                                @endif
                            </div>

                            {{-- MODAL CROP --}}
                            <div
                                x-cloak
                                x-show="cropModalOpen"
                                x-transition.opacity
                                x-on:keydown.escape.window="if (!cropUploading) closeCropper()"
                                class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto bg-slate-950/60 p-4"
                                role="dialog"
                                aria-modal="true"
                                aria-label="Sesuaikan foto profil"
                            >
                                <div
                                    x-on:click.stop
                                    class="my-auto w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl"
                                >
                                    {{-- Header modal --}}
                                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                                        <flux:heading size="lg">
                                            Sesuaikan foto profil
                                        </flux:heading>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Geser foto dan sesuaikan zoom agar posisi wajah pas.
                                        </p>
                                    </div>

                                    {{-- Area crop --}}
                                    <div class="p-5">
                                        <div
                                            class="mx-auto w-full overflow-hidden rounded-xl bg-slate-100"
                                            style="max-height: 55vh;"
                                        >
                                            <img
                                                x-ref="cropImage"
                                                x-bind:src="cropImageUrl"
                                                x-on:load="initCropper()"
                                                alt="Foto yang akan dipotong"
                                                class="block max-w-full"
                                            >
                                        </div>

                                        <div class="mt-4 flex flex-wrap justify-center gap-2">
                                            <button
                                                type="button"
                                                x-on:click="cropper?.zoom(-0.1)"
                                                :disabled="!cropper || cropUploading"
                                                class="rounded-lg border border-slate-200 px-3 py-2 text-sm hover:bg-slate-50 disabled:opacity-50"
                                            >
                                                − Perkecil
                                            </button>

                                            <button
                                                type="button"
                                                x-on:click="cropper?.zoom(0.1)"
                                                :disabled="!cropper || cropUploading"
                                                class="rounded-lg border border-slate-200 px-3 py-2 text-sm hover:bg-slate-50 disabled:opacity-50"
                                            >
                                                + Perbesar
                                            </button>

                                            <button
                                                type="button"
                                                x-on:click="cropper?.reset()"
                                                :disabled="!cropper || cropUploading"
                                                class="rounded-lg border border-slate-200 px-3 py-2 text-sm hover:bg-slate-50 disabled:opacity-50"
                                            >
                                                Reset
                                            </button>
                                        </div>

                                        <p class="mt-3 text-center text-xs text-slate-500">
                                            Hasil crop berbentuk persegi dengan resolusi 512 × 512 piksel.
                                        </p>
                                    </div>

                                    {{-- Footer modal --}}
                                    <div class="flex flex-col-reverse gap-2 border-t border-slate-100 px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
                                        <button
                                            type="button"
                                            x-on:click="closeCropper()"
                                            :disabled="cropUploading"
                                            class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            Batal
                                        </button>

                                        <button
    type="button"
    x-on:click="useCroppedPhoto()"
    :disabled="!cropper || cropUploading"
    class="btn-crop-primary"
>
    <span x-show="!cropUploading">
        Gunakan foto
    </span>

    <span x-cloak x-show="cropUploading">
        Memproses foto...
    </span>
</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- KARTU INFORMASI AKUN --}}
                <form
                    x-data="{
                        actionSucceeded: false,

                        async confirmUpdate() {
                            if (!window.Swal) {
                                await $wire.updateProfileInformation();
                                return;
                            }

                            const result = await window.Swal.fire({
                                title: 'Simpan perubahan profil?',
                                text: 'Nama dan alamat email akun Anda akan diperbarui. Pastikan informasi yang dimasukkan sudah benar.',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, simpan perubahan',
                                cancelButtonText: 'Batal',
                                confirmButtonColor: '#b8954a',
                                cancelButtonColor: '#71717a',
                                reverseButtons: true,
                                focusCancel: true,
                            });

                            if (!result.isConfirmed) {
                                return;
                            }

                            this.actionSucceeded = false;

                            window.Swal.fire({
                                title: 'Menyimpan perubahan...',
                                text: 'Mohon tunggu sebentar.',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => window.Swal.showLoading(),
                            });

                            try {
                                await $wire.updateProfileInformation();

                                if (!this.actionSucceeded) {
                                    window.Swal.close();
                                }
                            } catch (error) {
                                window.Swal.close();
                                throw error;
                            }
                        },

                        showSuccessToast() {
                            this.actionSucceeded = true;

                            if (!window.Swal) {
                                return;
                            }

                            window.Swal.fire({
                                toast: true,
                                position: 'top',
                                icon: 'success',
                                title: 'Informasi akun berhasil diperbarui.',
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
                    x-on:profile-information-updated.window="showSuccessToast()"
                    class="flex h-full min-w-0 flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6"
                >
                    {{-- Header --}}
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <flux:heading size="lg">
                                Informasi akun
                            </flux:heading>

                            <flux:subheading class="mt-1">
                                Kelola nama dan alamat email akun Anda.
                            </flux:subheading>
                        </div>

                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                            <flux:icon name="user" class="size-5" />
                        </div>
                    </div>

                    <flux:separator variant="subtle" class="my-5" />

                    {{-- Form informasi --}}
                    <div class="flex-1 space-y-5">
                        <div class="space-y-1.5">
                            <flux:input
                                wire:model="name"
                                label="Nama lengkap"
                                type="text"
                                required
                                autofocus
                                autocomplete="name"
                            />

                            <p class="text-xs leading-relaxed text-slate-500">
                                Nama ini akan ditampilkan pada akun Anda.
                            </p>
                        </div>

                        <div class="space-y-1.5">
                            <flux:input
                                wire:model="email"
                                label="Alamat email"
                                type="email"
                                required
                                autocomplete="email"
                            />

                            <p class="text-xs leading-relaxed text-slate-500">
                                Gunakan alamat email aktif untuk menerima pemberitahuan.
                            </p>

                            @if ($this->hasUnverifiedEmail)
                                <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-4">
                                    <div class="flex items-start gap-2">
                                        <flux:icon
                                            name="information-circle"
                                            class="mt-0.5 size-5 shrink-0 text-amber-700"
                                        />

                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-amber-900">
                                                Alamat email Anda belum terverifikasi.
                                            </p>

                                            <button
                                                type="button"
                                                class="mt-1 cursor-pointer text-left text-sm font-medium text-amber-800 underline underline-offset-2 hover:text-amber-950"
                                                wire:click="resendVerificationNotification"
                                            >
                                                Kirim ulang tautan verifikasi
                                            </button>

                                            @if (session('status') === 'verification-link-sent')
                                                <p class="mt-2 text-sm font-medium text-green-700">
                                                    Tautan verifikasi baru telah dikirim ke alamat email Anda.
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="mt-auto border-t border-slate-100 pt-5">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-xs leading-relaxed text-slate-500">
                                Pastikan data yang dimasukkan sudah benar.
                            </p>

                            <flux:button
                                type="submit"
                                class="!min-h-11 sm:shrink-0 !border-amber-200 !bg-amber-100 !text-amber-800 hover:!bg-amber-200"
                                wire:loading.attr="disabled"
                                wire:target="updateProfileInformation"
                                data-test="update-profile-button"
                            >
                                <span wire:loading.remove wire:target="updateProfileInformation">
                                    Simpan perubahan
                                </span>

                                <span wire:loading wire:target="updateProfileInformation">
                                    Menyimpan...
                                </span>
                            </flux:button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- HAPUS AKUN --}}
            @if ($this->showDeleteUser)
                <div class="rounded-2xl border border-red-200 bg-white p-5 shadow-sm sm:px-6 sm:py-5">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-4">
                            <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600">
                                <flux:icon
                                    name="exclamation-triangle"
                                    class="size-6"
                                />
                            </div>

                            <div class="min-w-0">
                                <flux:heading size="base">
                                    Hapus akun
                                </flux:heading>

                                <flux:subheading class="mt-1">
                                    Hapus akun beserta seluruh data terkait secara permanen.
                                </flux:subheading>
                            </div>
                        </div>

                        <div class="shrink-0 sm:pl-4">
                            <livewire:pages::settings.delete-user-form />
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </x-pages::settings.layout>
</section>
