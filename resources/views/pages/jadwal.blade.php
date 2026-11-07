<x-layouts.public
    title="Jadwal"
    description="Webinar, pelatihan, dan batch kursus yang akan datang. Semua waktu dalam WIB."
    active-page="schedule"
>

  <section class="page-hero">
    <div class="container-xl">
      <ol class="crumbs">
        <li><a href="{{ route('home') }}">Beranda</a></li>
        <li aria-current="page">Jadwal</li>
      </ol>
      <h1>Jadwal</h1>
      <p>Webinar, pelatihan, dan batch kursus yang akan datang. Semua waktu dalam WIB.</p>
    </div>
  </section>

  <section class="section">
    <div class="container-xl">
      <div class="bar mb-4">
        <form class="row g-3" method="get" action="{{ route('jadwal') }}">
          <div class="col-md-4">
            <label class="form-label" for="q">Cari jadwal</label>
            <input class="form-control" id="q" name="q" type="search" value="{{ request('q') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label" for="jenis">Jenis</label>
            <select class="form-select" id="jenis" name="jenis">
              <option value="">Semua jenis</option>
              <option value="webinar" {{ request('jenis') === 'webinar' ? 'selected' : '' }}>Webinar</option>
              <option value="pelatihan" {{ request('jenis') === 'pelatihan' ? 'selected' : '' }}>Pelatihan</option>
              <option value="batch" {{ request('jenis') === 'batch' ? 'selected' : '' }}>Batch kursus</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label" for="format">Format</label>
            <select class="form-select" id="format" name="format">
              <option value="">Semua format</option>
              <option value="daring" {{ request('format') === 'daring' ? 'selected' : '' }}>Daring</option>
              <option value="luring" {{ request('format') === 'luring' ? 'selected' : '' }}>Luring</option>
            </select>
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100" type="submit">Tampilkan</button>
          </div>
        </form>
      </div>

      <div class="empty">
        <h2>Tidak ada jadwal yang cocok</h2>
        <p class="text-secondary">Coba ubah filter atau tampilkan jadwal yang sudah lewat.</p>
        <a class="btn btn-primary" href="{{ route('jadwal') }}">Hapus semua filter</a>
      </div>

      <div class="note-box mt-4">
        <p>Jadwal bisa berubah. Perubahan diinformasikan melalui email kepada peserta terdaftar. Butuh bantuan? <a href="{{ route('home') }}">Hubungi kami</a>.</p>
      </div>
    </div>
  </section>

</x-layouts.public>

