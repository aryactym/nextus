@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header"><h3 class="card-title">Ubah Kursus</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.courses.update', $course->uuid) }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Judul (Indonesia)</label>
                            <input type="text" name="title[id]" class="form-control @error('title.id') is-invalid @enderror"
                                   value="{{ old('title.id', $course->title['id'] ?? '') }}" required>
                            @error('title.id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Judul (Inggris)</label>
                            <input type="text" name="title[en]" class="form-control @error('title.en') is-invalid @enderror"
                                   value="{{ old('title.en', $course->title['en'] ?? '') }}">
                            @error('title.en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Slug</label>
                        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                               value="{{ old('slug', $course->slug) }}" required>
                        @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Kategori</label>
                            <select name="category_id" class="form-select">
                                <option value="">Tanpa kategori</option>
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}" {{ old('category_id', $course->category_id) == $c->id ? 'selected' : '' }}>
                                        {{ $c->name['id'] ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Tipe Akses</label>
                            <select name="access_type" class="form-select">
                                <option value="free" {{ old('access_type', $course->access_type) == 'free' ? 'selected' : '' }}>Gratis</option>
                                <option value="donation" {{ old('access_type', $course->access_type) == 'donation' ? 'selected' : '' }}>Donasi</option>
                                <option value="paid" {{ old('access_type', $course->access_type) == 'paid' ? 'selected' : '' }}>Berbayar</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Tipe Sertifikat</label>
                            <select name="certificate_type" class="form-select">
                                <option value="free" {{ old('certificate_type', $course->certificate_type) == 'free' ? 'selected' : '' }}>Gratis</option>
                                <option value="donation" {{ old('certificate_type', $course->certificate_type) == 'donation' ? 'selected' : '' }}>Donasi</option>
                                <option value="paid" {{ old('certificate_type', $course->certificate_type) == 'paid' ? 'selected' : '' }}>Berbayar</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Thumbnail saat ini</label><br>
                        @if ($course->thumbnail)
                            <img src="{{ route('admin.uploads.show', ['path' => $course->thumbnail]) }}" style="max-height:120px; border:1px solid #ddd;" alt="thumbnail">
                        @else
                            <span class="text-muted">Belum ada thumbnail</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label>Ganti Thumbnail</label>
                        <input type="file" name="thumbnail" class="form-control" accept="image/*">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Level</label>
                            <input type="text" name="level" class="form-control" value="{{ old('level', $course->level) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Passing Grade</label>
                            <input type="number" name="passing_grade" class="form-control" value="{{ old('passing_grade', $course->passing_grade) }}">
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label>Status</label>
                        <select name="status" class="form-select">
                            <option value="draft" {{ old('status', $course->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $course->status) == 'published' ? 'selected' : '' }}>Publish</option>
                            <option value="archived" {{ old('status', $course->status) == 'archived' ? 'selected' : '' }}>Arsip</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary ms-2">Batal</a>
                </form>
            </div>
        </div>

        <!-- Modul & Pelajaran -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Modul &amp; Pelajaran</h5>
            </div>
            <div class="card-body">
                @foreach ($course->modules as $module)
                    <div class="mb-3">
                        <strong>{{ $module->title['id'] ?? '' }}</strong>
                        <form action="{{ route('admin.modules.destroy', $module) }}" method="POST" class="d-inline float-end" onsubmit="return confirm('Hapus modul?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Hapus modul</button>
                        </form>
                        @foreach ($module->lessons as $lesson)
                            <div class="d-flex justify-content-between align-items-center border rounded p-2 mt-2">
                                <span>{{ $lesson->title['id'] ?? '' }} <small class="text-muted">({{ $lesson->type }})</small></span>
                                <form action="{{ route('admin.lessons.destroy', $lesson) }}" method="POST" onsubmit="return confirm('Hapus pelajaran?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </div>
                        @endforeach
                        <form action="{{ route('admin.modules.lessons.store', $module) }}" method="POST" enctype="multipart/form-data" class="mt-2">
                            @csrf
                            <div class="row">
                                <div class="col-md-5"><input type="text" name="title[id]" class="form-control" placeholder="Judul pelajaran (ID)" required></div>
                                <div class="col-md-4"><input type="text" name="title[en]" class="form-control" placeholder="Judul pelajaran (EN)"></div>
                                <div class="col-md-3"><select name="type" class="form-select"><option value="text">Teks</option><option value="video">Video</option><option value="pdf">PDF</option><option value="quiz">Kuis</option></select></div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6"><textarea name="content[id]" class="form-control" rows="2" placeholder="Konten pelajaran (ID)"></textarea></div>
                                <div class="col-md-6"><textarea name="content[en]" class="form-control" rows="2" placeholder="Konten pelajaran (EN)"></textarea></div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6"><input type="url" name="video_url" class="form-control" placeholder="URL video (untuk tipe video)"></div>
                                <div class="col-md-4"><input type="file" name="file" class="form-control" accept="application/pdf"></div>
                                <div class="col-md-2"><input type="number" name="duration" class="form-control" min="0" placeholder="Menit"></div>
                            </div>
                            <label class="form-check mt-2"><input type="checkbox" name="is_preview" value="1" class="form-check-input"><span class="form-check-label">Jadikan preview</span></label>
                            <button class="btn btn-sm btn-success mt-2">Tambah Pelajaran</button>
                        </form>
                    </div>
                @endforeach
                <form action="{{ route('admin.courses.modules.store', $course->uuid) }}" method="POST" class="mt-3">
                    @csrf
                    <input type="text" name="title[id]" class="form-control d-inline w-auto" placeholder="Judul modul baru" required>
                    <button class="btn btn-sm btn-outline-primary">Tambah Modul</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
