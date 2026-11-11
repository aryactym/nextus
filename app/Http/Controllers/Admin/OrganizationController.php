<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Organization\Models\Organization;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    /** @var array<string, string> */
    public const TYPES = [
        'divisi_dd' => 'Divisi DD',
        'mpz_dd' => 'MPZ DD',
        'cabang' => 'Cabang',
        'lainnya' => 'Others',
    ];

    public function index(Request $request): View
    {
        abort_unless(auth()->user()->can('manage organizations'), 403);

        $q = trim((string) $request->input('q', ''));
        $type = (string) $request->input('type', '');

        $organizations = Organization::withoutGlobalScopes()
            ->withCount('users')
            ->when($q !== '', fn ($query) => $query->where('name', 'like', '%'.$q.'%'))
            ->when($type !== '', fn ($query) => $query->where('type', $type))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.organizations.index', [
            'organizations' => $organizations,
            'q' => $q,
            'type' => $type,
            'types' => self::TYPES,
        ]);
    }

    public function create(): View
    {
        abort_unless(auth()->user()->can('manage organizations'), 403);

        return view('admin.organizations.create', ['types' => self::TYPES]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage organizations'), 403);

        $data = $this->validated($request);
        $data['name'] = ['id' => $data['name']];
        $data['slug'] = $this->uniqueSlug($data['name']['id']);
        $data['default_locale'] = 'id';

        Organization::create($data);

        return redirect()->route('admin.organizations.index')
            ->with('success', 'Organisasi berhasil ditambahkan.');
    }

    public function edit(Organization $organization): View
    {
        abort_unless(auth()->user()->can('manage organizations'), 403);

        return view('admin.organizations.edit', [
            'organization' => $organization,
            'types' => self::TYPES,
        ]);
    }

    public function update(Request $request, Organization $organization): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage organizations'), 403);

        $data = $this->validated($request);

        if ($data['name'] !== $organization->getTranslation('name', 'id')) {
            $data['name'] = ['id' => $data['name']];
            $data['slug'] = $this->uniqueSlug($data['name']['id'], $organization->id);
        }

        $organization->update($data);

        return redirect()->route('admin.organizations.index')
            ->with('success', 'Organisasi berhasil diperbarui.');
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage organizations'), 403);

        if ($organization->users()->count() > 0) {
            return redirect()->route('admin.organizations.index')
                ->with('error', 'Organisasi masih memiliki peserta dan tidak dapat dihapus.');
        }

        $organization->delete();

        return redirect()->route('admin.organizations.index')
            ->with('success', 'Organisasi berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:'.implode(',', array_keys(self::TYPES))],
            'status' => ['required', 'in:active,inactive'],
        ]);
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'organisasi';
        $slug = $base;
        $i = 2;

        while (Organization::withoutGlobalScopes()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
