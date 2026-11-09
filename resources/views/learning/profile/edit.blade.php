<x-layouts.public
    title="Profil Saya"
    description="Kelola profil dan pendidikan Anda di {{ config('app.name') }}."
    active-page="belajar"
>

  <section class="page-hero">
    <div class="container-xl">
      <ol class="crumbs">
        <li><a href="{{ route('learning.dashboard') }}">Belajar saya</a></li>
        <li aria-current="page">Profil</li>
      </ol>
      <h1>Profil Saya</h1>
      <p>Kelola data pribadi dan pendidikan terakhir Anda.</p>
    </div>
  </section>

  <section class="section">
    <div class="container-xl">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
          @endif

          @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
          @endif

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Data pribadi</h3>
            </div>
            <div class="card-body">
              <form method="POST" action="{{ route('learning.profile.update') }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                  <label class="form-label" for="name">Nama lengkap</label>
                  <input type="text" id="name" name="name"
                         class="form-control @error('name') is-invalid @enderror"
                         value="{{ old('name', $user->name) }}" required>
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  <div class="form-hint">Nama ini akan dicetak di sertifikat.</div>
                </div>

                <div class="mb-3">
                  <label class="form-label" for="email">Email</label>
                  <input type="email" id="email" name="email"
                         class="form-control @error('email') is-invalid @enderror"
                         value="{{ old('email', $user->email) }}" required>
                  @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label" for="pendidikan_id">Pendidikan terakhir</label>
                  <select id="pendidikan_id" name="pendidikan_id"
                          class="form-select @error('pendidikan_id') is-invalid @enderror">
                    <option value="">Pilih pendidikan</option>
                    @foreach ($pendidikans as $p)
                      <option value="{{ $p->id }}"
                          @selected(old('pendidikan_id', $user->pendidikan_id) === (string) $p->id)>
                        {{ $p->name }}
                      </option>
                    @endforeach
                  </select>
                  @error('pendidikan_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  <div class="form-hint">Pendidikan ini digunakan untuk memenuhi syarat kelulusan kursus.</div>
                </div>

                <div class="mb-3">
                  <label class="form-label" for="nama_jabatan">Nama jabatan</label>
                  <input type="text" id="nama_jabatan" name="nama_jabatan"
                         class="form-control @error('nama_jabatan') is-invalid @enderror"
                         placeholder="Misal: Manajer Program"
                         value="{{ old('nama_jabatan', $user->nama_jabatan) }}">
                  @error('nama_jabatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label" for="kategori_jabatan">Kategori jabatan</label>
                  <select id="kategori_jabatan" name="kategori_jabatan"
                          class="form-select @error('kategori_jabatan') is-invalid @enderror">
                    <option value="">Pilih kategori</option>
                    @foreach (['Officer', 'Supervisor', 'Manajer', 'General Manajer', 'Direktur', 'Lainnya'] as $kategori)
                      <option value="{{ $kategori }}"
                          @selected(old('kategori_jabatan', $user->kategori_jabatan) === $kategori)>
                        {{ $kategori }}
                      </option>
                    @endforeach
                  </select>
                  @error('kategori_jabatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label" for="nama_organisasi">Nama organisasi</label>
                  <input type="text" id="nama_organisasi" name="nama_organisasi"
                         class="form-control @error('nama_organisasi') is-invalid @enderror"
                         placeholder="Misal: BAZNAS, Yayasan XYZ"
                         value="{{ old('nama_organisasi', $user->nama_organisasi) }}">
                  @error('nama_organisasi')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="d-grid gap-2">
                  <button type="submit" class="btn btn-primary">Simpan perubahan</button>
                  <a href="{{ route('learning.dashboard') }}" class="btn btn-link">Kembali ke dashboard</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

</x-layouts.public>
