<x-layouts.public
    title="Sertifikat {{ $certificate->course->getTranslation('title', app()->getLocale()) }}"
    description="Sertifikat penyelesaian kursus di {{ config('app.name') }}."
    active-page="catalog"
>
  @push('head')
    <style>
      @media print {
        .site-header, .site-footer, .crumbs, .no-print { display: none !important; }
        .certificate-card { border: 2px solid #0E6B5C !important; box-shadow: none !important; }
        main { padding: 0 !important; }
        body { background: #fff !important; }
      }
    </style>
  @endpush

  <section class="page-hero no-print">
    <div class="container-xl">
      <ol class="crumbs">
        <li><a href="{{ route('learning.dashboard') }}">Belajar saya</a></li>
        <li aria-current="page">Sertifikat</li>
      </ol>
      <h1>Sertifikat penyelesaian</h1>
      <p>Simpan atau cetak sertifikat ini sebagai bukti penyelesaian kursus.</p>
    </div>
  </section>

  <section class="section">
    <div class="container-xl">
      <div class="row justify-content-center">
        <div class="col-lg-9">
          <div class="card certificate-card position-relative overflow-hidden">
            @if ($certificate->course->certificate_background)
              <img src="{{ \Illuminate\Support\Facades\URL::signedRoute('admin.uploads.show', ['path' => $certificate->course->certificate_background]) }}"
                   alt="" class="position-absolute top-0 start-0 w-100 h-100"
                   style="object-fit:cover;opacity:0.18;z-index:0;">
            @endif
            <div class="card-body p-4 p-md-5 text-center position-relative" style="z-index:1;">
              <p class="text-secondary text-uppercase mb-2" style="letter-spacing:.18em;font-size:.75rem;">
                Sertifikat penyelesaian
              </p>
              <h2 class="h2 mb-4">{{ config('app.name') }}</h2>

              <p class="text-secondary mb-1">Diberikan kepada</p>
              <p class="h1 mb-4">{{ $certificate->user->name }}</p>

              <p class="text-secondary mb-1">atas keberhasilan menyelesaikan kursus</p>
              <p class="h2 mb-4">{{ $certificate->course->getTranslation('title', app()->getLocale()) }}</p>

              <div class="row justify-content-center text-start mt-5">
                <div class="col-sm-5 mb-3 mb-sm-0">
                  <div class="text-secondary small">Tanggal terbit</div>
                  <div class="fw-bold">{{ $certificate->issued_at->translatedFormat('d F Y') }}</div>
                </div>
                <div class="col-sm-5">
                  <div class="text-secondary small">Kode sertifikat</div>
                  <div class="fw-bold">{{ $certificate->code }}</div>
                </div>
              </div>

              <hr class="my-4">

              <p class="text-secondary small mb-0">
                Keaslian sertifikat dapat diverifikasi melalui kode di atas.
                Diterbitkan oleh {{ config('app.name') }}.
              </p>
            </div>
          </div>

          <div class="d-flex flex-wrap gap-2 justify-content-center mt-4 no-print">
            <button type="button" class="btn btn-primary" onclick="window.print()">Cetak / simpan PDF</button>
            <a class="btn btn-outline-primary" href="{{ route('certificates.pdf', $certificate->code) }}">Unduh PDF</a>
            <a class="btn btn-outline-primary"
               href="https://www.linkedin.com/profile/add?startTask=CERTIFICATION_NAME&name={{ urlencode($certificate->course->getTranslation('title', 'id')) }}&organizationName={{ urlencode($certificate->course->organization?->name ?? config('app.name')) }}&issueYear={{ $certificate->issued_at->format('Y') }}&issueMonth={{ $certificate->issued_at->format('n') }}&certUrl={{ urlencode(route('certificates.verify', $certificate->code)) }}&certId={{ urlencode($certificate->code) }}"
               target="_blank" rel="noopener">Tambahkan ke LinkedIn</a>
            <a class="btn btn-outline-primary" href="{{ route('learning.dashboard') }}">Kembali ke Belajar saya</a>
          </div>
        </div>
      </div>
    </div>
  </section>
</x-layouts.public>
