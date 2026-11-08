<?php

declare(strict_types=1);

namespace App\Domain\Settings\Http\Controllers;

use App\Domain\Settings\Http\Requests\StoreSponsorRequest;
use App\Domain\Settings\Http\Requests\UpdateSponsorRequest;
use App\Domain\Settings\Models\Sponsor;
use App\Http\Controllers\Controller;
use App\Services\UploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SponsorController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()->can('manage settings'), 403);

        $q = trim((string) $request->input('q', ''));

        $sponsors = Sponsor::query()
            ->when($q !== '', fn ($query) => $query->where('name', 'like', '%'.$q.'%'))
            ->active()
            ->paginate(15)
            ->withQueryString();

        return view('admin.sponsors.index', compact('sponsors', 'q'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->can('manage settings'), 403);

        return view('admin.sponsors.create');
    }

    public function store(StoreSponsorRequest $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage settings'), 403);

        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $result = (new UploadService(disk: 'public', folder: 'sponsors'))
                ->upload($request->file('logo'), 'image');
            $data['logo'] = $result['path'];
        }

        Sponsor::create($data);

        return redirect()
            ->route('admin.sponsors.index')
            ->with('success', 'Sponsor berhasil ditambahkan.');
    }

    public function edit(Sponsor $sponsor): View
    {
        abort_unless(auth()->user()->can('manage settings'), 403);

        return view('admin.sponsors.edit', compact('sponsor'));
    }

    public function update(UpdateSponsorRequest $request, Sponsor $sponsor): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage settings'), 403);

        $data = $request->validated();

        if ($request->hasFile('logo')) {
            if ($sponsor->logo) {
                (new UploadService(disk: 'public'))->delete($sponsor->logo);
            }
            $result = (new UploadService(disk: 'public', folder: 'sponsors'))
                ->upload($request->file('logo'), 'image');
            $data['logo'] = $result['path'];
        }

        $sponsor->update($data);

        return redirect()
            ->route('admin.sponsors.index')
            ->with('success', 'Sponsor berhasil diperbarui.');
    }

    public function destroy(Sponsor $sponsor): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage settings'), 403);

        if ($sponsor->logo) {
            (new UploadService(disk: 'public'))->delete($sponsor->logo);
        }

        $sponsor->delete();

        return redirect()
            ->route('admin.sponsors.index')
            ->with('success', 'Sponsor berhasil dihapus.');
    }
}
