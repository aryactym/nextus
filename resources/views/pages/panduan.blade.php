<x-layouts.public
    title="Panduan memulai"
    description="Enam langkah dari membuat akun sampai memegang sertifikat yang bisa diverifikasi."
    active-page="guide"
>

  <section class="page-hero">
    <div class="container-xl">
      <ol class="crumbs">
        <li><a href="{{ route('home-id') }}">Beranda</a></li>
        <li aria-current="page">Panduan memulai</li>
      </ol>
      <h1>Panduan memulai</h1>
      <p>Enam langkah dari membuat akun sampai memegang sertifikat yang bisa diverifikasi.</p>
    </div>
  </section>

  <section class="section">
    <div class="container-xl">
      <div class="row g-4">
        <aside class="col-lg-3" aria-label="Isi panduan">
          <div class="card">
            <div class="card-body">
              <h2 class="h3">Di halaman ini</h2>
              <ol class="list-group list-group-flush list-group-numbered">
                <li class="list-group-item"><a class="text-reset text-decoration-none" href="#langkah-1">Buat akun</a></li>
                <li class="list-group-item"><a class="text-reset text-decoration-none" href="#langkah-2">Lengkapi profil</a></li>
                <li class="list-group-item"><a class="text-reset text-decoration-none" href="#langkah-3">Pilih kursus</a></li>
                <li class="list-group-item"><a class="text-reset text-decoration-none" href="#langkah-4">Belajar dan kerjakan kuis</a></li>
                <li class="list-group-item"><a class="text-reset text-decoration-none" href="#langkah-5">Selesaikan kursus dan unduh sertifikat</a></li>
                <li class="list-group-item"><a class="text-reset text-decoration-none" href="#langkah-6">Verifikasi keaslian sertifikat</a></li>
              </ol>
            </div>
          </div>
        </aside>

        <div class="col-lg-9">
          <article class="card mb-4" id="langkah-1">
            <div class="card-body">
              <h2 class="h3">1. Buat akun</h2>
              <p>Akun gratis diperlukan untuk menyimpan progres belajar dan menerbitkan sertifikat. Buka halaman <a href="{{ route('register') }}">Daftar</a>, isi nama lengkap sesuai yang ingin dicetak di sertifikat, email aktif, dan kata sandi. Pilih peran kamu, lalu tekan <strong>Buat akun</strong>.</p>
              <div class="note-box"><p><strong>Tips:</strong> Gunakan nama lengkap sesuai identitas. Nama pada sertifikat tidak selalu bisa diubah setelah terbit.</p></div>
            </div>
          </article>

          <article class="card mb-4" id="langkah-2">
            <div class="card-body">
              <h2 class="h3">2. Lengkapi profil</h2>
              <p>Masuk, lalu buka menu profil. Lengkapi lembaga, bahasa, dan preferensi lain, lalu simpan perubahan. Gunakan bahasa pilihanmu untuk antarmuka dan sertifikat.</p>
            </div>
          </article>

          <article class="card mb-4" id="langkah-3">
            <div class="card-body">
              <h2 class="h3">3. Pilih kursus</h2>
              <p>Telusuri <a href="{{ route('catalog.index') }}">katalog kursus</a> dan gunakan filter agar cepat menemukan yang sesuai. Filter berdasarkan topik, tingkat, akses, dan durasi. Buka halaman kursus untuk membaca deskripsi, lalu tekan tombol daftar.</p>
            </div>
          </article>

          <article class="card mb-4" id="langkah-4">
            <div class="card-body">
              <h2 class="h3">4. Belajar dan kerjakan kuis</h2>
              <p>Ikuti video, bacaan, dan kuis dengan ritme kamu sendiri. Progres tersimpan otomatis, sehingga kamu bisa berhenti dan melanjutkan kapan saja dari <a href="{{ route('learning.dashboard') }}">Belajar saya</a>.</p>
            </div>
          </article>

          <article class="card mb-4" id="langkah-5">
            <div class="card-body">
              <h2 class="h3">5. Selesaikan kursus dan unduh sertifikat</h2>
              <p>Setelah semua pelajaran selesai dan kuis lulus, sertifikat digital terbit otomatis. Nama, judul kursus, dan tanggal penyelesaian tercetak pada sertifikat.</p>
            </div>
          </article>

          <article class="card mb-0" id="langkah-6">
            <div class="card-body">
              <h2 class="h3">6. Verifikasi keaslian sertifikat</h2>
              <p>Setiap sertifikat punya kode dan QR. Siapa saja bisa memeriksa keasliannya di halaman <a href="{{ route('verifikasi') }}">Verifikasi sertifikat</a>, dan kamu bisa menambahkannya ke LinkedIn.</p>
            </div>
          </article>
        </div>
      </div>
    </div>
  </section>

</x-layouts.public>
