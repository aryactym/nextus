@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Organisasi / Peserta</div>
            <h2 class="page-title">Hasil import</h2>
        </div>
        <div class="col-auto ms-auto d-flex gap-2">
            <a href="{{ route('admin.org.learners.import') }}" class="btn btn-outline-secondary">Import lagi</a>
            <a href="{{ route('admin.org.learners.index') }}" class="btn btn-primary">Lihat daftar peserta</a>
        </div>
    </div>
</div>

{{-- Ringkasan --}}
<div class="row row-cards mb-3">
    <div class="col-4">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Total baris</div>
                <div class="h1 mt-1">{{ $results['total'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Berhasil ditambahkan</div>
                <div class="h1 mt-1 text-green">{{ $results['success'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Dilewati / Error</div>
                <div class="h1 mt-1 {{ $results['skipped'] > 0 ? 'text-orange' : '' }}">
                    {{ $results['skipped'] }}
                </div>
            </div>
        </div>
    </div>
</div>

@if ($results['success'] > 0)
    <div class="alert alert-success">
        <strong>{{ $results['success'] }} peserta</strong> berhasil ditambahkan ke organisasi Anda.
    </div>
@endif

@if (! empty($results['errors']))
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detail baris yang dilewati</h3>
            <div class="card-actions">
                <span class="badge bg-orange-lt">{{ count($results['errors']) }} baris</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table card-table table-vcenter">
                <thead>
                    <tr>
                        <th class="w-1">Baris</th>
                        <th>Email</th>
                        <th>Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($results['errors'] as $error)
                        <tr>
                            <td class="text-secondary">{{ $error['row'] }}</td>
                            <td><code>{{ $error['email'] }}</code></td>
                            <td class="text-orange">{{ $error['reason'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="alert alert-success">
        Semua baris berhasil diproses tanpa error.
    </div>
@endif
@endsection
