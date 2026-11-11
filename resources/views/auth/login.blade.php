<x-layouts.public title="Masuk" active-page="login">

  <section class="auth">
    <div class="container-xl">
      <div class="auth-grid row g-0">

        <div class="col-lg-5 d-none d-lg-block">
          <div class="auth-aside" style="--pos:25% 40%">
            <img src="{{ asset('images/hero-auth.webp') }}" alt="" width="600" height="800" loading="lazy" onerror="this.style.display='none'">
            <div>
              <h2>Belajar filantropi Islam, lengkap dengan sertifikat</h2>
              <ul>
                <li>Kursus mandiri, bisa diakses dari HP</li>
                <li>Sertifikat dengan kode dan QR yang bisa diverifikasi</li>
                <li>Tersedia dalam bahasa Indonesia dan Inggris</li>
              </ul>
            </div>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="auth-form">
            <h1>Masuk</h1>
            <p class="sub">Lanjutkan belajar dan akses sertifikat kamu.</p>

            @if (session('error'))
              <div class="form-alert show" role="alert" tabindex="-1">{{ session('error') }}</div>
            @endif

            <form action="{{ route('login.post') }}" method="post" novalidate>
              @csrf

              <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input
                  class="form-control @error('email') is-invalid @enderror"
                  id="email" name="email" type="email"
                  autocomplete="username"
                  placeholder="nama@lembaga.org"
                  value="{{ old('email') }}"
                  required
                >
                @error('email')
                  <div class="field-error" style="display:block">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-3">
                <label class="form-label" for="password">Kata sandi</label>
                <div class="input-group">
                  <input
                    class="form-control @error('password') is-invalid @enderror"
                    id="password" name="password" type="password"
                    autocomplete="current-password"
                    required
                  >
                  <button class="btn btn-outline-secondary toggle-pw" type="button" data-for="password" aria-pressed="false">Tampilkan</button>
                </div>
                @error('password')
                  <div class="field-error" style="display:block">{{ $message }}</div>
                @enderror
              </div>

              <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <label class="form-check mb-0">
                  <input class="form-check-input" type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                  <span class="form-check-label">Ingat saya</span>
                </label>
                <a href="#">Lupa kata sandi?</a>
              </div>

              <x-captcha-field />

              <button class="btn btn-primary btn-submit" type="submit">Masuk</button>
            </form>

            <p class="auth-switch mb-0">Belum punya akun? <a href="{{ route('register') }}">Daftar gratis</a></p>
          </div>
        </div>

      </div>
    </div>
  @push('scripts')
  <script>
  // Toggle tampilkan/sembunyikan kata sandi
  document.querySelectorAll('.toggle-pw').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var input = document.getElementById(this.dataset.for);
      if (!input) return;
      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      this.textContent = show ? 'Sembunyikan' : 'Tampilkan';
      this.setAttribute('aria-pressed', show ? 'true' : 'false');
    });
  });
  </script>
  @endpush

  </section>
</x-layouts.public>

