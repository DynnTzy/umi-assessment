<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClinicController extends Controller
{
    /**
     * Menampilkan daftar klinik.
     */
    public function index(Request $request): View
    {
        $search = trim($request->input('search', ''));
        $status = $request->input('status', '');

        $clinics = Clinic::query()
            ->withCount('users')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'ilike', "%{$search}%")
                        ->orWhere('code', 'ilike', "%{$search}%")
                        ->orWhere('address', 'ilike', "%{$search}%");
                });
            })
            ->when($status === 'active', function ($query) {
                $query->where('is_active', true);
            })
            ->when($status === 'inactive', function ($query) {
                $query->where('is_active', false);
            })
            ->orderBy('name')
            ->paginate(5)
            ->withQueryString();

        return view('admin.clinics.index', compact(
            'clinics',
            'search',
            'status'
        ));
    }

    /**
     * Menyimpan klinik baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:255',
                'unique:clinics,code',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],
        ]);

        Clinic::create($validated);

        return redirect()
            ->route('admin.clinics.index')
            ->with('success', 'Klinik berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail klinik.
     */
    public function show(Clinic $clinic): View
    {
        $clinic->load('users');

        $availableUsers = User::query()
            ->where('role', 'klinik')
            ->whereNull('clinic_id')
            ->orderBy('name')
            ->get();

        return view('admin.clinics.show', compact(
            'clinic',
            'availableUsers'
        ));
    }

    /**
     * Menampilkan form edit klinik.
     */
    public function edit(Clinic $clinic): View
    {
        return view('admin.clinics.edit', compact('clinic'));
    }

    /**
     * Menyimpan perubahan data klinik.
     */
    public function update(
        Request $request,
        Clinic $clinic
    ): RedirectResponse {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('clinics', 'code')
                    ->ignore($clinic->id),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],
        ]);

        $clinic->update($validated);

        return redirect()
            ->route('admin.clinics.show', $clinic)
            ->with('success', 'Data klinik berhasil diperbarui.');
    }

    /**
     * Mengubah status aktif klinik.
     */
    public function toggleStatus(Clinic $clinic): RedirectResponse
    {
        $clinic->update([
            'is_active' => ! $clinic->is_active,
        ]);

        $status = $clinic->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()
            ->route('admin.clinics.show', $clinic)
            ->with('success', "Klinik berhasil {$status}.");
    }

    /**
     * Menetapkan pengguna ke klinik.
     */
    public function assignUser(
        Request $request,
        Clinic $clinic
    ): RedirectResponse {
        $validated = $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
            ],
        ]);

        $user = User::findOrFail($validated['user_id']);

        // Pastikan hanya user klinik yang bisa di-assign.
        if ($user->role !== 'klinik') {
            abort(403);
        }

        // Pastikan user belum terhubung ke klinik lain.
        if ($user->clinic_id !== null) {
            return back()->withErrors([
                'user_id' => 'Pengguna tersebut sudah terhubung dengan klinik lain.',
            ]);
        }

        $user->update([
            'clinic_id' => $clinic->id,
        ]);

        return redirect()
            ->route('admin.clinics.show', $clinic)
            ->with('success', 'Pengguna berhasil ditetapkan ke klinik.');
    }
}
