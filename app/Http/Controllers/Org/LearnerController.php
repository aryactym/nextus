<?php

declare(strict_types=1);

namespace App\Http\Controllers\Org;

use App\Domain\Catalog\Models\Pendidikan;
use App\Domain\Organization\Models\Organization;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
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

    /**
     * Admin / Super Admin (pengelola user) boleh melihat peserta lintas organisasi,
     * mis. saat membuka detail dari laporan organisasi lain.
     */
    private function canAccessAllOrganizations(): bool
    {
        return auth()->user()->can('manage users');
    }

    private function ensureCanAccessLearner(User $learner): void
    {
        abort_unless(
            $this->canAccessAllOrganizations() || $learner->organization_id === $this->orgId(),
            403
        );
    }

    /**
     * Super admin (manage organizations) boleh memindahkan peserta ke organisasi
     * mana pun lewat dropdown; role lain tetap di organisasi semula.
     */
    private function organizationIdFromRequest(Request $request, array $validated, int $fallbackId): int
    {
        if (auth()->user()->can('manage organizations') && $request->filled('organization_id')) {
            $id = (int) $request->input('organization_id');

            if (Organization::withoutGlobalScopes()->whereKey($id)->exists()) {
                return $id;
            }
        }

        return $fallbackId;
    }

    /**
     * Dropdown pilih organisasi hanya untuk super admin (manage organizations).
     *
     * @return Collection<int, Organization>|null
     */
    private function organizationsForSelect(): ?Collection
    {
        if (! auth()->user()->can('manage organizations')) {
            return null;
        }

        return Organization::withoutGlobalScopes()
            ->orderBy('name')
            ->get(['id', 'name', 'type']);
    }

    public function index(Request $request): View
    {
        abort_unless(auth()->user()->can('manage org learners'), 403);

        $q = trim((string) $request->input('q', ''));
        $status = (string) $request->input('status', '');
        $canAllOrgs = auth()->user()->can('manage organizations');
        $orgId = $canAllOrgs && $request->filled('org_id') ? (int) $request->input('org_id') : null;

        $learners = User::withoutGlobalScopes()
            ->with(['roles', 'enrollments', 'organization'])
            ->when($orgId, fn ($query) => $query->where('organization_id', $orgId))
            ->when(! $canAllOrgs, fn ($query) => $query->where('organization_id', $this->orgId()))
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

        $organizations = $canAllOrgs
            ? Organization::withoutGlobalScopes()
                ->whereNull('deleted_at')
                ->where('status', 'active')
                ->has('users')
                ->orderBy('name->id')
                ->get(['id', 'name', 'type'])
            : null;

        return view('org.learners.index', compact('learners', 'q', 'status', 'organizations', 'orgId'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->can('manage org learners'), 403);

        $pendidikans = Pendidikan::orderBy('level')->get();

        return view('org.learners.create', [
            'pendidikans' => $pendidikans,
            'organizations' => $this->organizationsForSelect(),
        ]);
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

        $organizationId = $this->organizationIdFromRequest($request, $validated, (int) $this->orgId());

        $user = User::withoutGlobalScopes()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'organization_id' => $organizationId,
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
        $this->ensureCanAccessLearner($learner);

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
        $this->ensureCanAccessLearner($learner);

        $pendidikans = Pendidikan::orderBy('level')->get();

        $learner->loadMissing('organization');

        return view('org.learners.edit', [
            'learner' => $learner,
            'pendidikans' => $pendidikans,
            'organizations' => $this->organizationsForSelect(),
        ]);
    }

    public function update(Request $request, User $learner): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage org learners'), 403);
        $this->ensureCanAccessLearner($learner);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$learner->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'pendidikan_id' => ['nullable', 'exists:pendidikans,id'],
            'nama_jabatan' => ['nullable', 'string', 'max:255'],
            'kategori_jabatan' => ['nullable', 'string', 'max:100'],
            'nama_organisasi' => ['nullable', 'string', 'max:255'],
        ]);

        $organizationId = $this->organizationIdFromRequest($request, $validated, (int) $learner->organization_id);

        $learner->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'organization_id' => $organizationId,
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
        $this->ensureCanAccessLearner($learner);
        abort_unless($learner->id !== auth()->id(), 403);

        $learner->delete();

        return redirect()->route('admin.org.learners.index')
            ->with('success', 'Peserta berhasil dinonaktifkan.');
    }

    public function restore(User $learner): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage org learners'), 403);
        $this->ensureCanAccessLearner($learner);

        $learner->restore();

        return redirect()->route('admin.org.learners.index')
            ->with('success', 'Peserta berhasil diaktifkan kembali.');
    }
}
