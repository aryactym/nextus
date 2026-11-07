<x-layouts.public
    title="Verifikasi sertifikat"
    description="Periksa keaslian sertifikat NextUs dengan kode yang tertera pada sertifikat."
    active-page="verify"
>

  <section class="page-hero">
    <div class="container-xl">
      <ol class="crumbs">
        <li><a href="{{ route('home-id') }}">Beranda</a></li>
        <li aria-current="page">Verifikasi sertifikat</li>
      </ol>
      <h1>Verifikasi sertifikat</h1>
      <p>Periksa keaslian sertifikat NextUs dengan kode yang tertera pada sertifikat.</p>
    </div>
  </section>

  <section class="section">
    <div class="container-xl">
      <div class="row g-4">
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h2 class="h3">Periksa kode</h2>
              <form method="get" action="{{ route('verifikasi') }}">
                <label class="form-label" for="kode">Kode sertifikat</label>
                <input class="form-control" id="kode" name="kode" type="text" placeholder="AF-2026-000123" value="{{ request('kode') }}" autocomplete="off">
                <div class="form-hint">Kode ada di bagian bawah sertifikat. Kamu juga bisa memindai QR pada sertifikat.</div>
                <button class="btn btn-primary mt-3" type="submit">Periksa kode</button>
              </form>

              @if (request('kode'))
                <div class="note-box mt-4">
                  <p>Kode <strong>{{ request('kode') }}</strong> belum ditemukan dalam sistem kami. Periksa penulisan kode. Jika tetap gagal, <a href="{{ route('home-id') }}">hubungi kami</a>.</p>
                </div>
              @endif
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h2 class="h3">Cara memeriksa</h2>
              <dl>
                <dt>Cari kodenya</dt>
                <dd>Kode berformat AF-tahun-nomor dan tercetak di sertifikat digital maupun cetak.</dd>
                <dt>Atau pindai QR</dt>
                <dd>QR pada sertifikat langsung membuka halaman ini dengan kode terisi.</dd>
                <dt>Kode tidak ditemukan?</dt>
                <dd>Periksa penulisan kode. Jika tetap gagal, hubungi kami.</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

</x-layouts.public>
