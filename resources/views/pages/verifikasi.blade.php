<x-layouts.public
    title="Verifikasi sertifikat"
    description="Periksa keaslian sertifikat NextUs dengan kode yang tertera pada sertifikat."
    active-page="verify"
>
  <section class="page-hero">
    <div class="container-xl">
      <ol class="crumbs">
        <li><a href="{{ route('home') }}">Beranda</a></li>
        <li aria-current="page">Verifikasi sertifikat</li>
      </ol>
      <h1>Verifikasi sertifikat</h1>
      <p>Periksa keaslian sertifikat NextUs dengan kode yang tertera pada sertifikat.</p>
    </div>
  </section>

  <section class="section">
    <div class="container-xl">
      <div class="row g-4">
        <div class="col-lg-5">
          <div class="card">
            <div class="card-body">
              <h2 class="h3">Cari sertifikat</h2>
              <form class="row g-2" action="{{ route('verifikasi') }}" method="get">
                <div class="col">
                  <label class="visually-hidden" for="kode">Kode sertifikat</label>
                  <input class="form-control" id="kode" name="kode" placeholder="Contoh: NX-2026-ABCD1234"
                         value="{{ $code ?? '' }}">
                </div>
                <div class="col-auto">
                  <button class="btn btn-primary h-100" type="submit">Periksa kode</button>
                </div>
              </form>
              <p class="text-secondary small mt-3 mb-0">
                Kode sertifikat tertera pada sertifikat dan bersifat unik.
              </p>
            </div>
          </div>
        </div>

        <div class="col-lg-7">
          @if ($code)
            @if ($status === 'valid' && $certificate)
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-green-lt">Valid</span>
                    <span class="text-secondary small">Sertifikat terverifikasi</span>
                  </div>

                  <h2 class="h3 mb-3">{{ $certificate->course->getTranslation('title', app()->getLocale()) }}</h2>

                  <dl class="row mb-3">
                    <dt class="col-sm-4 text-secondary">Penerima</dt>
                    <dd class="col-sm-8">{{ $certificate->user->name }}</dd>
                    <dt class="col-sm-4 text-secondary">Tanggal terbit</dt>
                    <dd class="col-sm-8">{{ $certificate->issued_at->translatedFormat('d F Y') }}</dd>
                    <dt class="col-sm-4 text-secondary">Kode</dt>
                    <dd class="col-sm-8 fw-bold">{{ $certificate->code }}</dd>
                  </dl>

                  <div class="d-flex align-items-center gap-3">
                    <div>
                      {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)->generate(route('certificates.verify', $certificate->code)) !!}
                    </div>
                    <div>
                      <p class="text-secondary small mb-2">
                        Pindai kode QR untuk membuka halaman verifikasi ini.
                      </p>
                      <a class="btn btn-outline-primary btn-sm"
                         href="https://www.linkedin.com/profile/add?startTask=CERTIFICATION_NAME&name={{ urlencode($certificate->course->getTranslation('title', 'id')) }}&organizationName={{ urlencode($certificate->course->organization?->name ?? config('app.name')) }}&issueYear={{ $certificate->issued_at->format('Y') }}&issueMonth={{ $certificate->issued_at->format('n') }}&certUrl={{ urlencode(route('certificates.verify', $certificate->code)) }}&certId={{ urlencode($certificate->code) }}"
                         target="_blank" rel="noopener">Tambahkan ke LinkedIn</a>
                    </div>
                  </div>
                </div>
              </div>
            @elseif ($status === 'revoked')
              <div class="card">
                <div class="card-body">
                  <span class="badge bg-red-lt">Dicabut</span>
                  <h2 class="h3 mt-2">Sertifikat dicabut</h2>
                  <p class="text-secondary mb-0">
                    Sertifikat dengan kode {{ $code }} tidak lagi berlaku.
                  </p>
                </div>
              </div>
            @else
              <div class="card">
                <div class="card-body">
                  <span class="badge bg-secondary-lt">Tidak ditemukan</span>
                  <h2 class="h3 mt-2">Sertifikat tidak ditemukan</h2>
                  <p class="text-secondary mb-0">
                    Tidak ada sertifikat dengan kode {{ $code }}.
                  </p>
                </div>
              </div>
            @endif
          @else
            <div class="empty">
              <h2>Cek keaslian sertifikat</h2>
              <p class="text-secondary">Masukkan kode sertifikat pada formulir di samping.</p>
            </div>
          @endif
        </div>
      </div>
    </div>
  </section>
</x-layouts.public>

