@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Organisasi / Peserta</div>
            <h2 class="page-title">Import peserta dari CSV</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('admin.org.learners.index') }}" class="btn btn-link">Kembali</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Upload file CSV</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.org.learners.import.store') }}"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label" for="csv_file">File CSV</label>
                        <input type="file" id="csv_file" name="csv_file"
                               class="form-control @error('csv_file') is-invalid @enderror"
                               accept=".csv,.txt" required>
                        <div class="form-hint">
                            Format: CSV, maksimal 2 MB, maksimal 500 baris.
                            <a href="{{ route('admin.org.learners.import.template') }}">Unduh template CSV</a>.
                        </div>
                        @error('csv_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="icon me-1" aria-hidden="true">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                        Proses import
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Format kolom</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm card-table mb-0">
                    <thead>
                        <tr>
                            <th>Kolom</th>
                            <th>Wajib</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>name</code></td>
                            <td><span class="badge bg-red-lt">Ya</span></td>
                            <td>Nama lengkap</td>
                        </tr>
                        <tr>
                            <td><code>email</code></td>
                            <td><span class="badge bg-red-lt">Ya</span></td>
                            <td>Email unik, untuk login</td>
                        </tr>
                        <tr>
                            <td><code>password</code></td>
                            <td><span class="badge bg-red-lt">Ya</span></td>
                            <td>Min. 8 karakter</td>
                        </tr>
                        <tr>
                            <td><code>nama_jabatan</code></td>
                            <td><span class="badge bg-gray-lt">Opsional</span></td>
                            <td>cth: Staf Program</td>
                        </tr>
                        <tr>
                            <td><code>kategori_jabatan</code></td>
                            <td><span class="badge bg-gray-lt">Opsional</span></td>
                            <td>Officer, Supervisor, Manajer, dll.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-body text-secondary small">
                <ul class="mb-0 ps-3">
                    <li>Peserta yang email-nya sudah terdaftar akan <strong>dilewati</strong> (tidak error).</li>
                    <li>Semua peserta baru otomatis mendapat peran <strong>Learner</strong>.</li>
                    <li>Baris yang tidak valid akan dilaporkan — baris lain tetap diproses.</li>
                    <li>Pastikan file disimpan dengan encoding <strong>UTF-8</strong>.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
