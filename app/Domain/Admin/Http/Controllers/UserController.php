<?php

declare(strict_types=1);

namespace App\Domain\Admin\Http\Controllers;

use App\Domain\Catalog\Models\Pendidikan;
use App\Domain\Organization\Models\Organization;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()->can('manage users'), 403);

        $q = trim((string) $request->input('q', ''));
        $kategoriJabatan = trim((string) $request->input('kategori_jabatan', ''));
        $namaOrganisasi = trim((string) $request->input('nama_organisasi', ''));
        $orgId = $request->input('org_id') ? (int) $request->input('org_id') : null;

        $organizations = Organization::whereNull('deleted_at')
            ->where('status', 'active')
            ->orderBy('name->id')
            ->get(['id', 'name']);

        $users = User::withoutGlobalScopes()
            ->with('roles', 'organization')
            ->when($q !== '', fn ($query) => $query->where(function ($query) use ($q): void {
                $query->where('name', 'like', '%'.$q.'%')
                    ->orWhere('email', 'like', '%'.$q.'%')
                    ->orWhere('nama_jabatan', 'like', '%'.$q.'%')
                    ->orWhere('nama_organisasi', 'like', '%'.$q.'%');
            }))
            ->when($kategoriJabatan !== '', fn ($query) => $query->where('kategori_jabatan', $kategoriJabatan))
            ->when($namaOrganisasi !== '', fn ($query) => $query->where('nama_organisasi', 'like', '%'.$namaOrganisasi.'%'))
            ->when($orgId !== null, fn ($query) => $query->where('organization_id', $orgId))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q', 'kategoriJabatan', 'namaOrganisasi', 'orgId', 'organizations'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->can('manage users'), 403);

        $roles = Role::query()->orderBy('name')->get();
        $pendidikans = Pendidikan::orderBy('level')->get();

        return view('admin.users.create', compact('roles', 'pendidikans'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage users'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'pendidikan_id' => ['nullable', 'exists:pendidikans,id'],
            'nama_jabatan' => ['nullable', 'string', 'max:255'],
            'kategori_jabatan' => ['nullable', 'string', 'max:100'],
            'nama_organisasi' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'organization_id' => auth()->user()->organization_id,
            'pendidikan_id' => $validated['pendidikan_id'] ?? null,
            'nama_jabatan' => $validated['nama_jabatan'] ?? null,
            'kategori_jabatan' => $validated['kategori_jabatan'] ?? null,
            'nama_organisasi' => $validated['nama_organisasi'] ?? null,
            'locale' => 'id',
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        abort_unless(auth()->user()->can('manage users'), 403);

        $roles = Role::query()->orderBy('name')->get();
        $pendidikans = Pendidikan::orderBy('level')->get();

        return view('admin.users.edit', compact('user', 'roles', 'pendidikans'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage users'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'pendidikan_id' => ['nullable', 'exists:pendidikans,id'],
            'nama_jabatan' => ['nullable', 'string', 'max:255'],
            'kategori_jabatan' => ['nullable', 'string', 'max:100'],
            'nama_organisasi' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'pendidikan_id' => $validated['pendidikan_id'] ?? null,
            'nama_jabatan' => $validated['nama_jabatan'] ?? null,
            'kategori_jabatan' => $validated['kategori_jabatan'] ?? null,
            'nama_organisasi' => $validated['nama_organisasi'] ?? null,
        ]);

        if (! empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage users'), 403);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
