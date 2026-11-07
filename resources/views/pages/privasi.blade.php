<x-layouts.public
    title="Kebijakan privasi"
    description="Cara kami mengumpulkan, menggunakan, dan melindungi data pribadi kamu."
    active-page="privacy"
>

  <section class="page-hero">
    <div class="container-xl">
      <ol class="crumbs">
        <li><a href="{{ route('home') }}">Beranda</a></li>
        <li aria-current="page">Kebijakan privasi</li>
      </ol>
      <h1>Kebijakan privasi</h1>
      <p>Cara kami mengumpulkan, menggunakan, dan melindungi data pribadi kamu.</p>
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
                @foreach (['Pendahuluan dan pengendali data', 'Data yang kami kumpulkan', 'Tujuan dan dasar pemrosesan', 'Data pada halaman verifikasi sertifikat', 'Pembagian data kepada pihak lain', 'Cookie dan penyimpanan lokal', 'Lama penyimpanan', 'Keamanan', 'Hak kamu', 'Data anak', 'Transfer data ke luar negeri', 'Perubahan kebijakan', 'Kontak dan pejabat pelindungan data'] as $i => $label)
                  <li class="list-group-item"><a class="text-reset text-decoration-none" href="#privasi-{{ $i + 1 }}">{{ $label }}</a></li>
                @endforeach
              </ol>
            </div>
          </div>
        </aside>

        <div class="col-lg-9">
          <article class="card mb-4" id="privasi-1"><div class="card-body">
            <h2 class="h3">1. Pendahuluan dan pengendali data</h2>
            <p>Kebijakan ini menjelaskan bagaimana [nama badan hukum] ("kami") mengumpulkan, menggunakan, menyimpan, dan melindungi data pribadi kamu saat memakai NextUs. Kami memproses data pribadi sesuai peraturan perundang-undangan yang berlaku, termasuk Undang-Undang Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi. Dalam kebijakan ini, kami bertindak sebagai Pengendali Data Pribadi.</p>
          </div></article>
          <article class="card mb-4" id="privasi-2"><div class="card-body">
            <h2 class="h3">2. Data yang kami kumpulkan</h2>
            <p>Kami hanya mengumpulkan data yang diperlukan untuk menjalankan Layanan: data akun (nama, email, kata sandi terenkripsi, peran, lembaga), data belajar (kursus yang diikuti, progres, hasil kuis), dan data teknis (log akses dan cookie yang diperlukan).</p>
          </div></article>
          <article class="card mb-4" id="privasi-3"><div class="card-body">
            <h2 class="h3">3. Tujuan dan dasar pemrosesan</h2>
            <p>Data dipakai untuk menyediakan layanan belajar, menerbitkan sertifikat, memproses pembayaran/donasi, dan mengirim notifikasi yang kamu setujui.</p>
          </div></article>
          <article class="card mb-4" id="privasi-4"><div class="card-body">
            <h2 class="h3">4. Data pada halaman verifikasi sertifikat</h2>
            <p>Halaman verifikasi menampilkan nama peserta, judul kursus, dan tanggal terbit sesuai persetujuan peserta, agar keaslian sertifikat dapat diperiksa publik.</p>
          </div></article>
          <article class="card mb-4" id="privasi-5"><div class="card-body">
            <h2 class="h3">5. Pembagian data kepada pihak lain</h2>
            <p>Kami tidak menjual data pribadi. Data dapat dibagikan kepada penyedia layanan (mis. Midtrans, penyedia email) sejauh diperlukan untuk menjalankan Layanan.</p>
          </div></article>
          <article class="card mb-4" id="privasi-6"><div class="card-body">
            <h2 class="h3">6. Cookie dan penyimpanan lokal</h2>
            <p>Kami menggunakan cookie yang diperlukan untuk sesi masuk dan preferensi bahasa. Tidak ada cookie pelacak iklan.</p>
          </div></article>
          <article class="card mb-4" id="privasi-7"><div class="card-body">
            <h2 class="h3">7. Lama penyimpanan</h2>
            <p>Data disimpan selama akun aktif dan sesuai kewajiban hukum yang berlaku, lalu dihapus atau dianonimkan.</p>
          </div></article>
          <article class="card mb-4" id="privasi-8"><div class="card-body">
            <h2 class="h3">8. Keamanan</h2>
            <p>Kata sandi disimpan dalam bentuk hash, koneksi dilindungi HTTPS, dan akses data dibatasi berdasarkan peran.</p>
          </div></article>
          <article class="card mb-4" id="privasi-9"><div class="card-body">
            <h2 class="h3">9. Hak kamu</h2>
            <p>Kamu berhak mengakses, memperbarui, dan menghapus data pribadimu, serta menarik persetujuan pemberian data, melalui [email kontak].</p>
          </div></article>
          <article class="card mb-4" id="privasi-10"><div class="card-body">
            <h2 class="h3">10. Data anak</h2>
            <p>Layanan tidak ditujukan untuk anak di bawah [13] tahun. Jika kamu menemukan data anak terkumpul tanpa izin, hubungi kami.</p>
          </div></article>
          <article class="card mb-4" id="privasi-11"><div class="card-body">
            <h2 class="h3">11. Transfer data ke luar negeri</h2>
            <p>Data dapat diproses pada layanan yang berlokasi di luar Indonesia. Kami memastikan perlindungan yang setara dengan undang-undang yang berlaku.</p>
          </div></article>
          <article class="card mb-4" id="privasi-12"><div class="card-body">
            <h2 class="h3">12. Perubahan kebijakan</h2>
            <p>Perubahan kebijakan akan diumumkan pada halaman ini beserta tanggal berlakunya.</p>
          </div></article>
          <article class="card mb-0" id="privasi-13"><div class="card-body">
            <h2 class="h3">13. Kontak dan pejabat pelindungan data</h2>
            <p>Untuk pertanyaan privasi, hubungi [email kontak] atau pejabat pelindungan data kami di [kontak].</p>
          </div></article>
        </div>
      </div>
    </div>
  </section>

</x-layouts.public>

