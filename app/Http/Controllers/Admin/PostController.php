<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Services\UploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()->can('manage blog'), 403);

        $q = trim((string) $request->input('q', ''));
        $status = (string) $request->input('status', '');

        $posts = Post::query()
            ->with('author')
            ->when($q !== '', fn ($query) => $query->where(function ($query) use ($q): void {
                $query->where('title', 'like', '%'.$q.'%')
                    ->orWhere('slug', 'like', '%'.$q.'%');
            }))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.posts.index', compact('posts', 'q', 'status'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->can('manage blog'), 403);

        return view('admin.posts.create');
    }

    public function store(StorePostRequest $request, UploadService $upload): RedirectResponse
    {
        $data = $request->validated();
        $data['author_id'] = auth()->id();
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        if ($data['status'] === 'published') {
            $data['published_at'] = now();
        }

        if ($request->hasFile('thumbnail')) {
            $result = $upload->upload($request->file('thumbnail'), 'image', 'posts/thumbnails');
            $data['thumbnail'] = $result['path'];
        }

        Post::create($data);

        return redirect()->route('admin.posts.index')
            ->with('success', 'Artikel berhasil ditambahkan.');
    }

    public function edit(Post $post): View
    {
        abort_unless(auth()->user()->can('manage blog'), 403);

        return view('admin.posts.edit', compact('post'));
    }

    public function update(UpdatePostRequest $request, Post $post, UploadService $upload): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage blog'), 403);

        $data = $request->validated();

        if ($data['status'] === 'published' && $post->published_at === null) {
            $data['published_at'] = now();
        } elseif ($data['status'] === 'draft') {
            $data['published_at'] = null;
        }

        if ($request->hasFile('thumbnail')) {
            if ($post->thumbnail) {
                (new UploadService)->delete($post->thumbnail);
            }
            $result = $upload->upload($request->file('thumbnail'), 'image', 'posts/thumbnails');
            $data['thumbnail'] = $result['path'];
        }

        $post->update($data);

        return redirect()->route('admin.posts.index')
            ->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage blog'), 403);

        if ($post->thumbnail) {
            (new UploadService)->delete($post->thumbnail);
        }

        $post->delete();

        return redirect()->route('admin.posts.index')
            ->with('success', 'Artikel berhasil dihapus.');
    }
}
