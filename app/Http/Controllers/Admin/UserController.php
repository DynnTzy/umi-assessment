<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Menampilkan daftar seluruh user.
     */
    public function index(Request $request): View
    {
        $search = trim($request->input('search', ''));
        $role = $request->input('role', '');
        $clinicId = $request->input('clinic_id', '');
        $verification = $request->input('verification', '');

        $users = User::query()
            ->with('clinic')

            // Pencarian nama dan email.
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'ilike', "%{$search}%")
                        ->orWhere('email', 'ilike', "%{$search}%");
                });
            })

            // Filter role.
            ->when(
                in_array($role, ['admin', 'asesor', 'klinik'], true),
                fn ($query) => $query->where('role', $role)
            )

            // Filter klinik.
            ->when(
                $clinicId !== '' && ctype_digit((string) $clinicId),
                fn ($query) => $query->where('clinic_id', $clinicId)
            )

            // Filter verifikasi email.
            ->when($verification === 'verified', function ($query) {
                $query->whereNotNull('email_verified_at');
            })
            ->when($verification === 'unverified', function ($query) {
                $query->whereNull('email_verified_at');
            })

            ->orderBy('name')
            ->paginate(5)
            ->withQueryString();

        // Semua klinik ditampilkan agar penempatan klinik nonaktif
        // yang masih dimiliki user tetap bisa dicari.
        $clinics = Clinic::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.users.index', compact(
            'users',
            'clinics',
            'search',
            'role',
            'clinicId',
            'verification'
        ));
    }

    /**
     * Menampilkan detail pengguna.
     */
    public function show(User $user): View
    {
        $user->load('clinic');

        return view('admin.users.show', compact('user'));
    }

    /**
     * Menampilkan form edit user.
     */
    public function edit(User $user): View
    {
        $user->load('clinic');

        // Klinik aktif selalu tersedia.
        // Klinik nonaktif milik user saat ini tetap ditampilkan
        // agar Admin dapat melihat penempatan yang sedang berlaku.
        $clinics = Clinic::query()
            ->where('is_active', true)
            ->orWhere('id', $user->clinic_id)
            ->orderBy('name')
            ->get();

        return view('admin.users.edit-clinic', compact(
            'user',
            'clinics'
        ));
    }

    /**
     * Menyimpan perubahan informasi dan penempatan user.
     */
    public function update(
        Request $request,
        User $user
    ): RedirectResponse {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'clinic_id' => [
                Rule::requiredIf($user->role === 'klinik'),
                'nullable',
                'integer',
                'exists:clinics,id',
            ],
        ]);

        // Validasi penempatan klinik hanya untuk user dengan role klinik.
        if ($user->role === 'klinik') {
            $clinic = Clinic::findOrFail($validated['clinic_id']);

            // Klinik nonaktif yang sudah menjadi penempatan saat ini
            // boleh dipertahankan, tetapi tidak boleh dipilih sebagai
            // penempatan baru.
            if (
                ! $clinic->is_active
                && (int) $clinic->id !== (int) $user->clinic_id
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'clinic_id' => 'Klinik nonaktif tidak dapat dipilih sebagai penempatan baru.',
                    ]);
            }
        }

        DB::transaction(function () use ($user, $validated) {
            $data = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];

            // Penempatan klinik hanya diperbarui untuk user role klinik.
            // Role Admin dan Asesor tidak mengalami perubahan clinic_id.
            if ($user->role === 'klinik') {
                $data['clinic_id'] = $validated['clinic_id'];
            }

            $user->update($data);
        });

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Informasi pengguna berhasil diperbarui.');
    }
}
