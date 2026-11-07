<x-layouts.public
    title="Syarat dan ketentuan"
    description="Aturan penggunaan NextUs. Mohon dibaca sebelum membuat akun dan mengikuti kursus."
    active-page="terms"
>

  <section class="page-hero">
    <div class="container-xl">
      <ol class="crumbs">
        <li><a href="{{ route('home-id') }}">Beranda</a></li>
        <li aria-current="page">Syarat dan ketentuan</li>
      </ol>
      <h1>Syarat dan ketentuan</h1>
      <p>Aturan penggunaan NextUs. Mohon dibaca sebelum membuat akun dan mengikuti kursus.</p>
    </div>
  </section>

  <section class="section">
    <div class="container-xl">
      <div class="note-box mb-4">
        <p><strong>Draf template.</strong> Teks bertanda [...] harus diisi atau disesuaikan dengan praktik sebenarnya. Minta penasihat hukum meninjau dokumen ini sebelum dipublikasikan, lalu hapus kotak ini.</p>
      </div>

      <div class="row g-4">
        <aside class="col-lg-3" aria-label="Isi halaman">
          <div class="card">
            <div class="card-body">
              <h2 class="h3">Isi halaman</h2>
              <ol class="list-group list-group-flush list-group-numbered">
                @foreach (['Penerimaan syarat', 'Definisi', 'Akun dan keamanan', 'Penggunaan yang diperbolehkan', 'Kursus, akses, dan donasi', 'Sertifikat dan verifikasi', 'Hak kekayaan intelektual', 'Konten dari Pengguna', 'Penafian dan batasan tanggung jawab', 'Penangguhan dan penghentian', 'Perubahan Syarat', 'Hukum yang berlaku dan penyelesaian sengketa', 'Kontak'] as $i => $label)
                  <li class="list-group-item"><a class="text-reset text-decoration-none" href="#syarat-{{ $i + 1 }}">{{ $label }}</a></li>
                @endforeach
              </ol>
            </div>
          </div>
        </aside>

        <div class="col-lg-9">
          <article class="card mb-4" id="syarat-1"><div class="card-body">
            <h2 class="h3">1. Penerimaan syarat</h2>
            <p>Dengan membuat akun atau menggunakan NextUs ("Layanan"), kamu menyatakan telah membaca, memahami, dan menyetujui Syarat dan Ketentuan ini serta <a href="{{ route('privasi') }}">Kebijakan Privasi</a>. Jika tidak setuju, mohon tidak menggunakan Layanan. Layanan dikelola oleh [nama badan hukum] ("kami").</p>
          </div></article>
          <article class="card mb-4" id="syarat-2"><div class="card-body">
            <h2 class="h3">2. Definisi</h2>
            <p><strong>Pengguna:</strong> setiap orang yang mengakses atau memakai Layanan. <strong>Akun:</strong> akun pribadi untuk mengikuti kursus. <strong>Kursus:</strong> materi pembelajaran daring beserta kuis dan penilaiannya. <strong>Sertifikat:</strong> bukti penyelesaian kursus berbentuk digital dengan kode verifikasi. <strong>Kegiatan:</strong> webinar, pelatihan, atau batch kursus pada halaman <a href="{{ route('jadwal') }}">Jadwal</a>.</p>
          </div></article>
          <article class="card mb-4" id="syarat-3"><div class="card-body">
            <h2 class="h3">3. Akun dan keamanan</h2>
            <p>Kamu bertanggung jawab atas kerahasiaan kata sandi dan seluruh aktivitas pada akunmu. Segera beri tahu kami jika terjadi penggunaan tanpa izin.</p>
          </div></article>
          <article class="card mb-4" id="syarat-4"><div class="card-body">
            <h2 class="h3">4. Penggunaan yang diperbolehkan</h2>
            <p>Gunakan Layanan untuk tujuan pembelajaran yang sah. Dilarang menyalin, menjual, atau menyebarluaskan materi kursus tanpa izin tertulis.</p>
          </div></article>
          <article class="card mb-4" id="syarat-5"><div class="card-body">
            <h2 class="h3">5. Kursus, akses, dan donasi</h2>
            <p>Setiap kursus dapat berakses gratis, via donasi, atau berbayar sesuai ketentuan pada halaman kursus. Donasi dan pembayaran diproses melalui Midtrans; akses dibuka setelah pembayaran berstatus lunas.</p>
          </div></article>
          <article class="card mb-4" id="syarat-6"><div class="card-body">
            <h2 class="h3">6. Sertifikat dan verifikasi</h2>
            <p>Sertifikat diterbitkan atas nama peserta berdasarkan penyelesaian kursus. Dilarang memalsukan atau mengubah isi sertifikat. Keaslian dapat diperiksa pada halaman <a href="{{ route('verifikasi') }}">Verifikasi</a>.</p>
          </div></article>
          <article class="card mb-4" id="syarat-7"><div class="card-body">
            <h2 class="h3">7. Hak kekayaan intelektual</h2>
            <p>Seluruh materi, logo, dan konten Layanan dilindungi hukum dan menjadi milik kami atau pemberi lisensinya.</p>
          </div></article>
          <article class="card mb-4" id="syarat-8"><div class="card-body">
            <h2 class="h3">8. Konten dari Pengguna</h2>
            <p>Konten yang kamu unggah menjadi tanggung jawabmu dan tidak boleh melanggar hak pihak lain atau hukum yang berlaku.</p>
          </div></article>
          <article class="card mb-4" id="syarat-9"><div class="card-body">
            <h2 class="h3">9. Penafian dan batasan tanggung jawab</h2>
            <p>Layanan disediakan "sebagaimana adanya". Kami tidak bertanggung jawab atas kerugian yang timbul dari penggunaan Layanan, sejauh diizinkan hukum.</p>
          </div></article>
          <article class="card mb-4" id="syarat-10"><div class="card-body">
            <h2 class="h3">10. Penangguhan dan penghentian</h2>
            <p>Kami dapat menangguhkan atau menghentikan akun yang melanggar Syarat ini, dengan pemberitahuan bila memungkinkan.</p>
          </div></article>
          <article class="card mb-4" id="syarat-11"><div class="card-body">
            <h2 class="h3">11. Perubahan Syarat</h2>
            <p>Perubahan Syarat akan diumumkan pada halaman ini. Penggunaan berkelanjutan setelah perubahan berarti menerima Syarat yang baru.</p>
          </div></article>
          <article class="card mb-4" id="syarat-12"><div class="card-body">
            <h2 class="h3">12. Hukum yang berlaku dan penyelesaian sengketa</h2>
            <p>Syarat ini tunduk pada hukum Republik Indonesia. Sengketa diselesaikan secara musyawarah, dan apabila gagal, melalui pengadilan [domisili pengadilan].</p>
          </div></article>
          <article class="card mb-0" id="syarat-13"><div class="card-body">
            <h2 class="h3">13. Kontak</h2>
            <p>Pertanyaan mengenai Syarat ini dapat disampaikan melalui [email kontak].</p>
          </div></article>
        </div>
      </div>
    </div>
  </section>

</x-layouts.public>
