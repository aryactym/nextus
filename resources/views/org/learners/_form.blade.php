{{-- Shared form partial for create & edit learner --}}
<div class="row">
    <div class="col-lg-7">
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Informasi peserta</h3>
            </div>
            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label" for="name">Nama lengkap</label>
                    <input type="text" id="name" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $learner->name ?? '') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $learner->email ?? '') }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="organization_id">Organisasi</label>
                    @if (isset($organizations) && $organizations !== null)
                        @php
                            $typeLabels = [
                                'divisi_dd' => 'Divisi DD',
                                'mpz_dd' => 'MPZ DD',
                                'cabang' => 'Cabang',
                                'lainnya' => 'Others',
                            ];
                            $currentOrgId = old('organization_id', $learner->organization_id ?? auth()->user()->organization_id);
                        @endphp
                        <select id="organization_id" name="organization_id"
                                class="form-select @error('organization_id') is-invalid @enderror">
                            @foreach ($organizations as $org)
                                <option value="{{ $org->id }}" @selected((int) $currentOrgId === $org->id)>
                                    {{ $org->name }} — {{ $typeLabels[$org->type] ?? $org->type }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-hint">Pilih organisasi tempat peserta terdaftar.</div>
                        @error('organization_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @else
                        <input type="text" class="form-control" disabled
                               value="{{ $learner->organization?->name ?? auth()->user()->organization?->name ?? '-' }}">
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label" for="pendidikan_id">Pendidikan terakhir</label>
                    <select id="pendidikan_id" name="pendidikan_id"
                            class="form-select @error('pendidikan_id') is-invalid @enderror">
                        <option value="">— Pilih pendidikan —</option>
                        @foreach ($pendidikans as $p)
                            <option value="{{ $p->id }}"
                                @selected(old('pendidikan_id', $learner->pendidikan_id ?? '') == $p->id)>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('pendidikan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label" for="nama_jabatan">Nama jabatan</label>
                        <input type="text" id="nama_jabatan" name="nama_jabatan"
                               class="form-control @error('nama_jabatan') is-invalid @enderror"
                               value="{{ old('nama_jabatan', $learner->nama_jabatan ?? '') }}"
                               placeholder="cth: Staf Program">
                        @error('nama_jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-5">
                        <label class="form-label" for="kategori_jabatan">Kategori jabatan</label>
                        <select id="kategori_jabatan" name="kategori_jabatan"
                                class="form-select @error('kategori_jabatan') is-invalid @enderror">
                            <option value="">— Pilih —</option>
                            @foreach (['Officer', 'Supervisor', 'Manajer', 'General Manajer', 'Direktur', 'Lainnya'] as $kat)
                                <option value="{{ $kat }}"
                                    @selected(old('kategori_jabatan', $learner->kategori_jabatan ?? '') === $kat)>
                                    {{ $kat }}
                                </option>
                            @endforeach
                        </select>
                        @error('kategori_jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label class="form-label" for="nama_organisasi">Nama lembaga / unit kerja</label>
                    <input type="text" id="nama_organisasi" name="nama_organisasi"
                           class="form-control @error('nama_organisasi') is-invalid @enderror"
                           value="{{ old('nama_organisasi', $learner->nama_organisasi ?? '') }}"
                           placeholder="cth: Divisi Program, Cabang Surabaya">
                    <div class="form-hint">Unit kerja atau cabang spesifik di dalam organisasi (opsional).</div>
                    @error('nama_organisasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <hr>

                <div class="mb-3">
                    <label class="form-label" for="password">
                        Password
                        @isset($learner)
                            <span class="text-muted small">(kosongkan jika tidak diubah)</span>
                        @endisset
                    </label>
                    <input type="password" id="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           @isset($learner) autocomplete="new-password" @endisset
                           @empty($learner) required @endempty>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-0">
                    <label class="form-label" for="password_confirmation">Konfirmasi password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="form-control">
                </div>

            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                {{ isset($learner) ? 'Simpan perubahan' : 'Tambah peserta' }}
            </button>
            <a href="{{ route('admin.org.learners.index') }}" class="btn btn-link">Batal</a>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Catatan</h3>
            </div>
            <div class="card-body text-secondary small">
                <ul class="mb-0 ps-3">
                    <li>Peserta akan otomatis mendapatkan peran <strong>Learner</strong>.</li>
                    <li>Peserta bisa login dengan email dan password yang diset di sini.</li>
                    <li>Untuk tambah banyak peserta sekaligus, gunakan fitur <a href="{{ route('admin.org.learners.import') }}">Import CSV</a>.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
