<?php

declare(strict_types=1);

namespace App\Domain\Admin\Http\Controllers;

use App\Domain\Admin\Http\Requests\UpdatePageRequest;
use App\Domain\Admin\Models\Page;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()->can('manage pages'), 403);

        $filters = [
            'q' => trim((string) $request->input('q', '')),
            'status' => (string) $request->input('status', ''),
        ];

        $pages = Page::query()
            ->when($filters['q'] !== '', function ($query) use ($filters): void {
                $query->where(function ($query) use ($filters): void {
                    $query->where('title', 'like', '%'.$filters['q'].'%')
                        ->orWhere('slug', 'like', '%'.$filters['q'].'%');
                });
            })
            ->when($filters['status'] === 'published', fn ($query) => $query->where('is_published', true))
            ->when($filters['status'] === 'draft', fn ($query) => $query->where('is_published', false))
            ->orderBy('title')
            ->paginate(10)
            ->withQueryString();

        return view('admin.pages.index', compact('pages', 'filters'));
    }

    public function edit(Page $page): View
    {
        abort_unless(auth()->user()->can('manage pages'), 403);

        return view('admin.pages.edit', compact('page'));
    }

    public function update(UpdatePageRequest $request, Page $page): RedirectResponse
    {
        $page->update([
            ...$request->validated(),
            'is_published' => $request->boolean('is_published'),
            'updated_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Halaman berhasil diperbarui.');
    }
}
