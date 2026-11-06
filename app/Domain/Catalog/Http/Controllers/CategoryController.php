<?php

namespace App\Domain\Catalog\Http\Controllers;

use App\Domain\Catalog\Http\Requests\StoreCategoryRequest;
use App\Domain\Catalog\Http\Requests\UpdateCategoryRequest;
use App\Domain\Catalog\Models\Category;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::forOrganization()->orderBy('sort')->get();

        return view('catalog.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('catalog.categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $data['organization_id'] = organization_id();
        $data['sort'] = $data['sort'] ?? 0;
        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        abort_unless(auth()->user()->can('manage categories'), 403);

        return view('catalog.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        abort_unless(auth()->user()->can('manage categories'), 403);
        $category->update($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        abort_unless(auth()->user()->can('manage categories'), 403);
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
