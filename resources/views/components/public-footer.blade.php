{{--
    x-public-footer — Footer publik NextUs

    Tidak ada props wajib. Secara otomatis mengambil config('app.name') dan tahun berjalan.
--}}

<footer class="site-footer">
  <div class="container-xl">
    <div class="row g-4">

      {{-- Kolom 1: Brand --}}
      <div class="col-md-4">
        <a href="{{ route('home') }}">
          <img
            class="footer-logo"
            src="{{ asset('images/logo-nextus-putih.svg') }}"
            alt="{{ config('app.name') }}"
            height="44"
            onerror="this.src='{{ asset('images/logo-nextus-putih.png') }}'"
          >
        </a>
        <p class="mt-3">Belajar filantropi Islam untuk orang muda.<br>Ilmu baik, diteruskan.</p>
      </div>

      {{-- Kolom 2: Tentang --}}
      <div class="col-6 col-md-3">
        <h2 class="footer-title">Tentang</h2>
        <ul>
          <li><a href="{{ route('tentang') }}">Tentang NextUs</a></li>
          <li><a href="{{ route('home') }}#org">Solusi organisasi</a></li>
          <li><a href="{{ route('syarat') }}">Syarat dan ketentuan</a></li>
          <li><a href="{{ route('privasi') }}">Kebijakan privasi</a></li>
        </ul>
      </div>

      {{-- Kolom 3: Bantuan --}}
      <div class="col-6 col-md-3">
        <h2 class="footer-title">Bantuan</h2>
        <ul>
          <li><a href="{{ route('panduan') }}">Panduan memulai</a></li>
          @if (Route::has('jadwal'))
            <li><a href="{{ route('jadwal') }}">Jadwal kursus</a></li>
          @endif
          <li><a href="{{ route('verifikasi') }}">Verifikasi sertifikat</a></li>
          <li><a href="{{ route('home') }}" hreflang="en">English</a></li>
        </ul>
      </div>

      {{-- Kolom 4: Sosial --}}
      <div class="col-md-2">
        <h2 class="footer-title">Ikuti kami</h2>
        <ul class="social">
          <li>
            <a href="#" rel="noopener noreferrer" target="_blank" aria-label="Instagram">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="4"/><circle cx="12" cy="12" r="3"/><path d="M16.5 7.5v.01"/></svg>
              <span>Instagram</span>
            </a>
          </li>
          <li>
            <a href="#" rel="noopener noreferrer" target="_blank" aria-label="YouTube">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="4"/><path d="M10 9l5 3l-5 3z"/></svg>
              <span>YouTube</span>
            </a>
          </li>
          <li>
            <a href="#" rel="noopener noreferrer" target="_blank" aria-label="LinkedIn">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 11v5"/><path d="M8 8v.01"/><path d="M12 16v-5"/><path d="M16 16v-3a2 2 0 0 0-4 0"/><path d="M3 7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v10a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4z"/></svg>
              <span>LinkedIn</span>
            </a>
          </li>
        </ul>
      </div>

    </div>

    <div class="legal">&copy; {{ date('Y') }} {{ config('app.name') }}. Seluruh hak dilindungi.</div>
  </div>
</footer>

