<?php

declare(strict_types=1);

namespace App\Domain\Learning\Http\Controllers;

use App\Domain\Catalog\Models\Pendidikan;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit(): View
    {
        $user = Auth::user();
        $pendidikans = Pendidikan::orderBy('level')->get();

        return view('learning.profile.edit', compact('user', 'pendidikans'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
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

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
