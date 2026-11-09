<?php

declare(strict_types=1);

namespace App\Http\Controllers\Org;

use App\Domain\Catalog\Models\Pendidikan;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class LearnerController extends Controller
{
    private function orgId(): int
    {
        return (int) auth()->user()->organization_id;
    }

    public function index(Request $request): View
    {
        abort_unless(auth()->user()->can('manage org learners'), 403);

        $q = trim((string) $request->input('q', ''));
        $status = (string) $request->input('status', '');

        $learners = User::withoutGlobalScopes()
            ->with('roles', 'enrollments')
            ->where('organization_id', $this->orgId())
            ->when($q !== '', fn ($query) => $query->where(function ($query) use ($q): void {
                $query->where('name', 'like', '%'.$q.'%')
                    ->orWhere('email', 'like', '%'.$q.'%')
                    ->orWhere('nama_jabatan', 'like', '%'.$q.'%');
            }))
            ->when($status === 'active', fn ($q) => $q->whereNull('deleted_at'))
            ->when($status === 'inactive', fn ($q) => $q->withTrashed()->whereNotNull('deleted_at'))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('org.learners.index', compact('learners', 'q', 'status'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->can('manage org learners'), 403);

        $pendidikans = Pendidikan::orderBy('level')->get();

        return view('org.learners.create', compact('pendidikans'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage org learners'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'pendidikan_id' => ['nullable', 'exists:pendidikans,id'],
            'nama_jabatan' => ['nullable', 'string', 'max:255'],
            'kategori_jabatan' => ['nullable', 'string', 'max:100'],
            'nama_organisasi' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::withoutGlobalScopes()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'organization_id' => $this->orgId(),
            'pendidikan_id' => $validated['pendidikan_id'] ?? null,
            'nama_jabatan' => $validated['nama_jabatan'] ?? null,
            'kategori_jabatan' => $validated['kategori_jabatan'] ?? null,
            'nama_organisasi' => $validated['nama_organisasi'] ?? null,
            'locale' => 'id',
        ]);

        $user->assignRole('Learner');

        return redirect()->route('admin.org.learners.index')
            ->with('success', 'Peserta berhasil ditambahkan.');
    }

    public function show(User $learner): View
    {
        abort_unless(auth()->user()->can('manage org learners'), 403);
        abort_unless($learner->organization_id === $this->orgId(), 403);

        $learner->load([
            'enrollments.course.category',
            'enrollments.certificate',
            'pendidikan',
        ]);

        return view('org.learners.show', compact('learner'));
    }

    public function edit(User $learner): View
    {
        abort_unless(auth()->user()->can('manage org learners'), 403);
        abort_unless($learner->organization_id === $this->orgId(), 403);

        $pendidikans = Pendidikan::orderBy('level')->get();

        return view('org.learners.edit', compact('learner', 'pendidikans'));
    }

    public function update(Request $request, User $learner): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage org learners'), 403);
        abort_unless($learner->organization_id === $this->orgId(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$learner->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'pendidikan_id' => ['nullable', 'exists:pendidikans,id'],
            'nama_jabatan' => ['nullable', 'string', 'max:255'],
            'kategori_jabatan' => ['nullable', 'string', 'max:100'],
            'nama_organisasi' => ['nullable', 'string', 'max:255'],
        ]);

        $learner->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'pendidikan_id' => $validated['pendidikan_id'] ?? null,
            'nama_jabatan' => $validated['nama_jabatan'] ?? null,
            'kategori_jabatan' => $validated['kategori_jabatan'] ?? null,
            'nama_organisasi' => $validated['nama_organisasi'] ?? null,
        ]);

        if (! empty($validated['password'])) {
            $learner->update(['password' => Hash::make($validated['password'])]);
        }

        return redirect()->route('admin.org.learners.index')
            ->with('success', 'Data peserta berhasil diperbarui.');
    }

    public function destroy(User $learner): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage org learners'), 403);
        abort_unless($learner->organization_id === $this->orgId(), 403);
        abort_unless($learner->id !== auth()->id(), 403);

        $learner->delete();

        return redirect()->route('admin.org.learners.index')
            ->with('success', 'Peserta berhasil dinonaktifkan.');
    }

    public function restore(User $learner): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage org learners'), 403);
        abort_unless($learner->organization_id === $this->orgId(), 403);

        $learner->restore();

        return redirect()->route('admin.org.learners.index')
            ->with('success', 'Peserta berhasil diaktifkan kembali.');
    }
}
