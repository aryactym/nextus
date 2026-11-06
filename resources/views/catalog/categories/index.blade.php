@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3">Kategori</h1>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Tambah Kategori</a>
        </div>
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama (ID)</th>
                            <th>Nama (EN)</th>
                            <th>Slug</th>
                            <th>Urutan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $category->name['id'] ?? '' }}</td>
                            <td>{{ $category->name['en'] ?? '-' }}</td>
                            <td>{{ $category->slug }}</td>
                            <td>{{ $category->sort }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.categories.edit', $category->uuid) }}" class="btn btn-sm btn-warning">Ubah</a>
                                <form action="{{ route('admin.categories.destroy', $category->uuid) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus kategori ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
