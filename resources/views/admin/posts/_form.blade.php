<div class="row">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Konten artikel</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label" for="title">Judul</label>
                    <input type="text" id="title" name="title" form="post-form"
                           class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title', $post->title ?? '') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="slug">Slug</label>
                    <div class="input-group">
                        <span class="input-group-text text-muted">/blog/</span>
                        <input type="text" id="slug" name="slug" form="post-form"
                               class="form-control @error('slug') is-invalid @enderror"
                               value="{{ old('slug', $post->slug ?? '') }}" required>
                    </div>
                    @error('slug')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="meta_description">
                        Meta deskripsi
                        <span class="text-muted small">(maks. 160 karakter, tampil di Google)</span>
                    </label>
                    <textarea id="meta_description" name="meta_description" form="post-form" rows="2"
                              class="form-control @error('meta_description') is-invalid @enderror"
                              maxlength="160">{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
                    @error('meta_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-0">
                    <label class="form-label" for="content">Isi artikel</label>
                    <textarea id="content" name="content" form="post-form"
                              class="form-control @error('content') is-invalid @enderror"
                              rows="20">{{ old('content', $post->content ?? '') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Publikasi</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" form="post-form"
                            class="form-select @error('status') is-invalid @enderror" required>
                        <option value="draft" @selected(old('status', $post->status ?? 'draft') === 'draft')>Draf</option>
                        <option value="published" @selected(old('status', $post->status ?? '') === 'published')>Terbit</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @isset($post)
                    @if ($post->published_at)
                        <div class="text-muted small mb-3">
                            Diterbitkan: {{ $post->published_at->format('d M Y, H:i') }}
                        </div>
                    @endif
                @endisset

                <hr>

                <div class="mb-3">
                    <label class="form-label">Thumbnail</label>
                    @isset($post)
                        @if ($post->thumbnail)
                            <img src="{{ \Illuminate\Support\Facades\URL::signedRoute('admin.uploads.show', ['path' => $post->thumbnail]) }}"
                                 class="img-fluid rounded border mb-2" alt="Thumbnail artikel">
                        @else
                            <div class="text-muted small mb-2">Belum ada thumbnail.</div>
                        @endif
                    @endisset
                    <input type="file" name="thumbnail" form="post-form"
                           class="form-control @error('thumbnail') is-invalid @enderror" accept="image/*">
                    <div class="form-hint">JPG, PNG, atau WebP. Maks. 2 MB. Rasio 16:9 dianjurkan.</div>
                    @error('thumbnail')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" form="post-form" class="btn btn-primary">Simpan artikel</button>
                </div>
            </div>
        </div>
    </div>
</div>
