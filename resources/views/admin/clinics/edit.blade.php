<x-layouts::app.sidebar :title="'Edit Klinik'">

    <flux:main>
        <div class="space-y-6">

            {{-- PAGE HEADER --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2 text-sm text-zinc-500">
                        <a
                            href="{{ route('admin.clinics.index') }}"
                            class="transition hover:text-zinc-900 dark:hover:text-white"
                        >
                            Manajemen Klinik
                        </a>

                        <span>/</span>

                        <span class="text-zinc-700 dark:text-zinc-300">
                            Edit Klinik
                        </span>
                    </div>

                    <flux:heading size="xl" class="mt-2">
                        Edit Klinik
                    </flux:heading>

                    <flux:subheading class="mt-1">
                        Perbarui informasi klinik yang terdaftar dalam sistem assessment.
                    </flux:subheading>
                </div>

                <flux:button
                    href="{{ route('admin.clinics.show', $clinic) }}"
                    variant="ghost"
                    icon="arrow-left"
                >
                    Kembali
                </flux:button>
            </div>

            {{-- CLINIC INFORMATION --}}
            <flux:card>
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center">

                    {{-- CLINIC ICON --}}
                    <div class="flex size-16 shrink-0 items-center justify-center rounded-2xl border border-amber-200/70 bg-amber-50/70 text-amber-700 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-400">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.8"
                            stroke="currentColor"
                            class="size-8"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3 21h18M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16M9 7h1m4 0h1M9 11h1m4 0h1M9 15h1m4 0h1"
                            />
                        </svg>
                    </div>

                    {{-- CLINIC IDENTITY --}}
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">
                            Informasi Klinik
                        </p>

                        <h2 class="mt-1 text-xl font-bold tracking-tight text-zinc-900 dark:text-white">
                            {{ $clinic->name }}
                        </h2>

                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <flux:badge color="zinc">
                                {{ $clinic->code }}
                            </flux:badge>

                            @if ($clinic->is_active)
                                <flux:badge color="green" icon="check-circle">
                                    Klinik Aktif
                                </flux:badge>
                            @else
                                <flux:badge color="red" icon="x-circle">
                                    Klinik Nonaktif
                                </flux:badge>
                            @endif
                        </div>
                    </div>

                    {{-- USER COUNT --}}
                    <div class="sm:text-right">
                        <p class="text-2xl font-semibold tabular-nums text-zinc-900 dark:text-white">
                            {{ $clinic->users->count() }}
                        </p>

                        <p class="text-xs text-zinc-500">
                            Pengguna Terhubung
                        </p>
                    </div>

                </div>
            </flux:card>

            {{-- EDIT CLINIC FORM --}}
            <flux:card class="overflow-hidden p-0">

                {{-- FORM HEADER --}}
                <div class="border-b border-zinc-200 px-6 py-5 dark:border-zinc-800">
                    <flux:heading size="lg">
                        Informasi Klinik
                    </flux:heading>

                    <flux:subheading class="mt-1">
                        Periksa dan sesuaikan informasi klinik berikut.
                    </flux:subheading>
                </div>

                <form
                    id="edit-clinic-form"
                    method="POST"
                    action="{{ route('admin.clinics.update', $clinic) }}"
                >
                    @csrf
                    @method('PUT')

                    <div class="space-y-6 px-6 py-6">

                        {{-- CODE & NAME --}}
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                            {{-- CLINIC CODE --}}
                            <flux:field>
                                <flux:label for="code">
                                    Kode Klinik
                                </flux:label>

                                <flux:input
                                    id="code"
                                    name="code"
                                    type="text"
                                    value="{{ old('code', $clinic->code) }}"
                                    placeholder="Contoh: CMG"
                                    maxlength="255"
                                    required
                                />

                                <flux:error name="code" />
                            </flux:field>

                            {{-- CLINIC NAME --}}
                            <flux:field>
                                <flux:label for="name">
                                    Nama Klinik
                                </flux:label>

                                <flux:input
                                    id="name"
                                    name="name"
                                    type="text"
                                    value="{{ old('name', $clinic->name) }}"
                                    placeholder="Contoh: Klinik Cipta Medika"
                                    maxlength="255"
                                    required
                                />

                                <flux:error name="name" />
                            </flux:field>

                        </div>

                        {{-- ADDRESS --}}
                        <flux:field>
                            <flux:label for="address">
                                Alamat Klinik
                            </flux:label>

                            <flux:textarea
                                id="address"
                                name="address"
                                rows="3"
                                placeholder="Masukkan alamat lengkap klinik"
                            >{{ old('address', $clinic->address) }}</flux:textarea>

                            <flux:error name="address" />
                        </flux:field>

                        {{-- PHONE & EMAIL --}}
                        <div class="grid grid-cols-1 gap-5 border-t border-zinc-100 pt-6 md:grid-cols-2 dark:border-zinc-800">

                            {{-- PHONE --}}
                            <flux:field>
                                <flux:label for="phone">
                                    Nomor Telepon
                                </flux:label>

                                <flux:input
                                    id="phone"
                                    name="phone"
                                    type="text"
                                    value="{{ old('phone', $clinic->phone) }}"
                                    maxlength="255"
                                    placeholder="Contoh: 0311234567"
                                />

                                <flux:error name="phone" />
                            </flux:field>

                            {{-- EMAIL --}}
                            <flux:field>
                                <flux:label for="email">
                                    Email Klinik
                                </flux:label>

                                <flux:input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email', $clinic->email) }}"
                                    maxlength="255"
                                    placeholder="contoh@klinik.com"
                                />

                                <flux:error name="email" />
                            </flux:field>

                        </div>

                    </div>

                    {{-- FORM ACTIONS --}}
                    <div class="flex flex-col-reverse gap-2 border-t border-zinc-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-end dark:border-zinc-800">

                        <flux:button
                            href="{{ route('admin.clinics.show', $clinic) }}"
                            variant="ghost"
                        >
                            Batal
                        </flux:button>

                        <flux:button
                            id="save-clinic-button"
                            type="submit"
                            variant="primary"
                            icon="check"
                            disabled
                        >
                            Simpan Perubahan
                        </flux:button>

                    </div>

                </form>
            </flux:card>

        </div>
    </flux:main>

    {{-- SWEETALERT2 & FORM INTERACTION --}}
    <script>
        (() => {
            const form = document.getElementById('edit-clinic-form');
            const saveButton = document.getElementById('save-clinic-button');

            if (!form || !saveButton) {
                return;
            }

            const fields = [
                document.getElementById('code'),
                document.getElementById('name'),
                document.getElementById('address'),
                document.getElementById('phone'),
                document.getElementById('email'),
            ].filter(Boolean);

            const initialValues = new Map(
                fields.map(field => [field.name, field.value])
            );

            function hasChanges() {
                return fields.some(
                    field => field.value !== initialValues.get(field.name)
                );
            }

            function updateSaveButton() {
                saveButton.disabled = !hasChanges();
            }

            fields.forEach(field => {
                field.addEventListener('input', updateSaveButton);
                field.addEventListener('change', updateSaveButton);
            });

            updateSaveButton();

            form.addEventListener('submit', async function (event) {
                event.preventDefault();

                if (!form.reportValidity()) {
                    return;
                }

                if (!hasChanges()) {
                    updateSaveButton();

                    Swal.fire({
                        icon: 'info',
                        title: 'Tidak Ada Perubahan',
                        text: 'Ubah setidaknya satu informasi sebelum menyimpan.',
                        confirmButtonText: 'Mengerti',
                        confirmButtonColor: '#b8954a',
                    });

                    return;
                }

                if (form.dataset.confirming === 'true') {
                    return;
                }

                form.dataset.confirming = 'true';

                try {
                    const result = await Swal.fire({
                        title: 'Simpan Perubahan Klinik?',
                        text: 'Informasi klinik akan diperbarui sesuai perubahan yang kamu buat.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Simpan',
                        cancelButtonText: 'Periksa Kembali',
                        confirmButtonColor: '#b8954a',
                        cancelButtonColor: '#71717a',
                        reverseButtons: true,
                        focusCancel: true,
                    });

                    if (result.isConfirmed) {
                        HTMLFormElement.prototype.submit.call(form);
                        return;
                    }
                } catch (error) {
                    console.error(
                        'Gagal menampilkan konfirmasi perubahan klinik:',
                        error
                    );
                }

                form.dataset.confirming = 'false';
            });
        })();

        @if (session('success'))
            Swal.fire({
                title: 'Berhasil!',
                text: @json(session('success')),
                icon: 'success',
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#b8954a',
            });
        @endif

        @if (session('error'))
            Swal.fire({
                title: 'Gagal!',
                text: @json(session('error')),
                icon: 'error',
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#b8954a',
            });
        @endif
    </script>

</x-layouts::app.sidebar>
