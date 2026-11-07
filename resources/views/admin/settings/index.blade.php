@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Sistem</div>
            <h2 class="page-title">Pengaturan</h2>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Pelacakan &amp; verifikasi</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="google_analytics_id">Google Analytics (GA4)</label>
                        <input type="text" id="google_analytics_id" name="google_analytics_id"
                               class="form-control @error('google_analytics_id') is-invalid @enderror"
                               value="{{ old('google_analytics_id', $settings['google_analytics_id']) }}"
                               placeholder="G-XXXXXXXXXX">
                        <div class="form-hint">Measurement ID GA4. Skrip pelacakan otomatis dipasang di halaman publik.</div>
                        @error('google_analytics_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="google_search_console">Google Search Console</label>
                        <input type="text" id="google_search_console" name="google_search_console"
                               class="form-control @error('google_search_console') is-invalid @enderror"
                               value="{{ old('google_search_console', $settings['google_search_console']) }}"
                               placeholder="nilai content dari meta tag verifikasi">
                        <div class="form-hint">
                            Tempel hanya nilai <code>content</code> dari meta tag verifikasi Google, bukan seluruh tag.
                        </div>
                        @error('google_search_console')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label" for="facebook_pixel_id">Facebook Pixel</label>
                        <input type="text" id="facebook_pixel_id" name="facebook_pixel_id"
                               class="form-control @error('facebook_pixel_id') is-invalid @enderror"
                               value="{{ old('facebook_pixel_id', $settings['facebook_pixel_id']) }}"
                               placeholder="123456789012345">
                        <div class="form-hint">Pixel ID berupa angka. Skrip pelacakan otomatis dipasang di halaman publik.</div>
                        @error('facebook_pixel_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">SMTP email</h3>
                    @if ($settings['mail_password_set'])
                        <div class="card-actions">
                            <span class="badge bg-green-lt">Password tersimpan</span>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label" for="mail_host">Host SMTP</label>
                            <input type="text" id="mail_host" name="mail_host"
                                   class="form-control @error('mail_host') is-invalid @enderror"
                                   value="{{ old('mail_host', $settings['mail_host']) }}"
                                   placeholder="smtp.contoh.com">
                            @error('mail_host')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="mail_port">Port</label>
                            <input type="number" id="mail_port" name="mail_port"
                                   class="form-control @error('mail_port') is-invalid @enderror"
                                   value="{{ old('mail_port', $settings['mail_port']) }}"
                                   min="1" max="65535" placeholder="587">
                            @error('mail_port')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="mail_username">Username</label>
                            <input type="text" id="mail_username" name="mail_username"
                                   class="form-control @error('mail_username') is-invalid @enderror"
                                   value="{{ old('mail_username', $settings['mail_username']) }}"
                                   autocomplete="off">
                            @error('mail_username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="mail_password">Password / API key</label>
                            <input type="password" id="mail_password" name="mail_password"
                                   class="form-control @error('mail_password') is-invalid @enderror"
                                   value=""
                                   autocomplete="new-password"
                                   placeholder="{{ $settings['mail_password_set'] ? '•••••••• (biarkan kosong jika tidak diubah)' : 'Masukkan password atau API key' }}">
                            <div class="form-hint">Disimpan terenkripsi. Kosongkan jika tidak ingin mengubah.</div>
                            @error('mail_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="mail_encryption">Enkripsi</label>
                            <select id="mail_encryption" name="mail_encryption"
                                    class="form-select @error('mail_encryption') is-invalid @enderror">
                                <option value="tls" @selected(old('mail_encryption', $settings['mail_encryption']) === 'tls')>TLS (port 587)</option>
                                <option value="ssl" @selected(old('mail_encryption', $settings['mail_encryption']) === 'ssl')>SSL (port 465)</option>
                                <option value="none" @selected(old('mail_encryption', $settings['mail_encryption']) === 'none')>Tanpa enkripsi</option>
                            </select>
                            @error('mail_encryption')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="mail_from_address">Email pengirim</label>
                            <input type="email" id="mail_from_address" name="mail_from_address"
                                   class="form-control @error('mail_from_address') is-invalid @enderror"
                                   value="{{ old('mail_from_address', $settings['mail_from_address']) }}"
                                   placeholder="halo@contoh.com">
                            @error('mail_from_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="mail_from_name">Nama pengirim</label>
                            <input type="text" id="mail_from_name" name="mail_from_name"
                                   class="form-control @error('mail_from_name') is-invalid @enderror"
                                   value="{{ old('mail_from_name', $settings['mail_from_name']) }}"
                                   placeholder="{{ config('app.name') }}">
                            @error('mail_from_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    @if ($settings['mail_password_set'])
                        <div class="form-check">
                            <input type="checkbox" id="clear_mail_password" name="clear_mail_password" value="1"
                                   class="form-check-input">
                            <label class="form-check-label" for="clear_mail_password">
                                Hapus password/API key yang tersimpan
                            </label>
                        </div>
                    @endif
                </div>
            </div>

            <div class="d-flex gap-2 mb-3">
                <button type="submit" class="btn btn-primary">Simpan pengaturan</button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-link">Batal</a>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Catatan</h3>
                </div>
                <div class="card-body text-secondary">
                    <p>Pengaturan pelacakan langsung aktif di halaman publik setelah disimpan.</p>
                    <p class="mb-3">Konfigurasi SMTP dipakai untuk seluruh email aplikasi (verifikasi, reset password, dan notifikasi).</p>
                    <ul class="mb-0 ps-3">
                        <li>GA4 memakai Measurement ID berawalan <code>G-</code>.</li>
                        <li>Search Console cukup nilai <code>content</code>-nya.</li>
                        <li>Facebook Pixel berupa deretan angka.</li>
                        <li>Gunakan port 587 untuk TLS dan 465 untuk SSL.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Uji pengiriman email</h3>
            </div>
            <div class="card-body">
                <p class="text-secondary">
                    Kirim email uji untuk memastikan konfigurasi SMTP berfungsi.
                    Saat pengembangan dengan Laragon, buka Mailpit di
                    <a href="http://127.0.0.1:8025" target="_blank" rel="noopener">127.0.0.1:8025</a>.
                </p>
                <form method="POST" action="{{ route('admin.settings.test-email') }}" class="row g-2">
                    @csrf
                    <div class="col-md-8">
                        <label class="form-label" for="test_email">Kirim ke alamat</label>
                        <input type="email" id="test_email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', auth()->user()->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary w-100">Kirim email uji</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
