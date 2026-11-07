<x-layouts.public title="Daftar gratis" active-page="register">

  <section class="auth">
    <div class="container-xl">
      <div class="auth-grid row g-0">

        <div class="col-lg-5 d-none d-lg-block">
          <div class="auth-aside" style="--pos:60% 50%">
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
            <h1>Daftar gratis</h1>
            <p class="sub">Buat akun untuk mulai belajar dan mendapatkan sertifikat.</p>

            @if ($errors->any())
              <div class="form-alert show" role="alert" tabindex="-1">
                Periksa isian di bawah dan coba lagi.
              </div>
            @endif

            <form action="{{ route('register.post') }}" method="post" novalidate>
              @csrf

              <div class="mb-3">
                <label class="form-label" for="name">Nama lengkap</label>
                <input
                  class="form-control @error('name') is-invalid @enderror"
                  id="name" name="name" type="text"
                  autocomplete="name"
                  placeholder="Sesuai yang dicetak di sertifikat"
                  value="{{ old('name') }}"
                  required
                >
                @error('name')
                  <div class="field-error" style="display:block">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input
                  class="form-control @error('email') is-invalid @enderror"
                  id="email" name="email" type="email"
                  autocomplete="email"
                  placeholder="nama@lembaga.org"
                  value="{{ old('email') }}"
                  required
                >
                @error('email')
                  <div class="field-error" style="display:block">{{ $message }}</div>
                @enderror
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label" for="role">Peran</label>
                  <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                    <option value="">Pilih peran</option>
                    <option value="Amil" {{ old('role') === 'Amil' ? 'selected' : '' }}>Amil</option>
                    <option value="Relawan" {{ old('role') === 'Relawan' ? 'selected' : '' }}>Relawan</option>
                    <option value="Mitra lembaga" {{ old('role') === 'Mitra lembaga' ? 'selected' : '' }}>Mitra lembaga</option>
                    <option value="Umum" {{ old('role') === 'Umum' ? 'selected' : '' }}>Umum</option>
                  </select>
                  @error('role')
                    <div class="field-error" style="display:block">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="organization">Lembaga <span class="text-secondary fw-normal">(opsional)</span></label>
                  <input
                    class="form-control"
                    id="organization" name="organization"
                    autocomplete="organization"
                    value="{{ old('organization') }}"
                  >
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label" for="password">Kata sandi</label>
                <div class="input-group">
                  <input
                    class="form-control @error('password') is-invalid @enderror"
                    id="password" name="password" type="password"
                    autocomplete="new-password"
                    required
                    aria-describedby="pw-hint"
                  >
                  <button class="btn btn-outline-secondary toggle-pw" type="button" data-for="password" aria-pressed="false">Tampilkan</button>
                </div>
                @error('password')
                  <div class="field-error" style="display:block">{{ $message }}</div>
                @enderror
                <div class="pw-meter" id="meter" data-s="0" aria-hidden="true"><span></span><span></span><span></span></div>
                <div class="pw-text" id="pw-hint">Minimal 8 karakter, memuat huruf dan angka.</div>
              </div>

              <div class="mb-3">
                <label class="form-label" for="password_confirmation">Ulangi kata sandi</label>
                <div class="input-group">
                  <input
                    class="form-control"
                    id="password_confirmation" name="password_confirmation" type="password"
                    autocomplete="new-password"
                    required
                  >
                  <button class="btn btn-outline-secondary toggle-pw" type="button" data-for="password_confirmation" aria-pressed="false">Tampilkan</button>
                </div>
              </div>

              <div class="mb-4">
                <label class="form-check mb-0">
                  <input class="form-check-input @error('terms') is-invalid @enderror" id="terms" name="terms" type="checkbox" value="1" required>
                  <span class="form-check-label">Saya menyetujui <a href="{{ route('syarat') }}">Syarat dan ketentuan</a> serta <a href="{{ route('privasi') }}">Kebijakan privasi</a>.</span>
                </label>
                @error('terms')
                  <div class="field-error" style="display:block">{{ $message }}</div>
                @enderror
              </div>

              <button class="btn btn-primary btn-submit" type="submit">Buat akun</button>
            </form>

            <p class="auth-switch mb-0">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
          </div>
        </div>

      </div>
    </div>
  </section>

</x-layouts.public>

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

// Password strength meter
var pwInput = document.getElementById('password');
var meter = document.getElementById('meter');
if (pwInput && meter) {
  pwInput.addEventListener('input', function() {
    var v = this.value;
    var s = 0;
    if (v.length >= 8) s++;
    if (/[a-zA-Z]/.test(v) && /\d/.test(v)) s++;
    if (v.length >= 12 && /[^a-zA-Z0-9]/.test(v)) s++;
    meter.dataset.s = s;
  });
}
</script>
@endpush

