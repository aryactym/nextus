@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3">Kursus</h1>
            <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">Tambah Kursus</a>
        </div>
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Judul (ID)</th>
                            <th>Kategori</th>
                            <th>Akses</th>
                            <th>Sertifikat</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($courses as $course)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $course->title['id'] ?? '' }}</td>
                            <td>{{ optional($course->category)->name['id'] ?? '-' }}</td>
                            <td>{{ $course->access_type }}</td>
                            <td>{{ $course->certificate_type }}</td>
                            <td><span class="badge bg-{{ $course->status === 'published' ? 'success' : 'secondary' }}">{{ $course->status }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('admin.courses.edit', $course->uuid) }}" class="btn btn-sm btn-warning">Ubah</a>
                                <form action="{{ route('admin.courses.destroy', $course->uuid) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus kursus ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{ $courses->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
