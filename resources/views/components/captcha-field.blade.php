@props(['id' => 'captcha'])

<div class="mb-3">
  <label class="form-label" for="{{ $id }}">Kode verifikasi</label>
  <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
    <img
      src="{{ route('captcha.image') }}"
      alt="Kode verifikasi berupa gambar; klik gambar untuk mengganti kode"
      id="{{ $id }}-img"
      width="190" height="60"
      style="border-radius:.5rem;border:1px solid #d8dedb;background:#f5f7f4"
      loading="lazy"
      title="Kode tidak terbaca? Klik gambar untuk mengganti"
    >
    <button
      class="btn btn-outline-secondary" type="button"
      id="{{ $id }}-refresh"
      aria-label="Ganti kode verifikasi"
    >Ganti</button>
  </div>
  <input
    class="form-control @error('captcha') is-invalid @enderror"
    id="{{ $id }}" name="captcha" type="text"
    inputmode="text" autocomplete="off" spellcheck="false"
    maxlength="6" placeholder="Ketik kode pada gambar"
    required
  >
  @error('captcha')
    <div class="field-error" style="display:block">{{ $message }}</div>
  @enderror
</div>

@push('scripts')
<script>
(function() {
  var img = document.getElementById('{{ $id }}-img');
  var btn = document.getElementById('{{ $id }}-refresh');
  if (!img) return;

  var reload = function() {
    img.src = '{{ route('captcha.image') }}?t=' + Date.now();
  };

  img.style.cursor = 'pointer';
  img.addEventListener('click', reload);
  if (btn) btn.addEventListener('click', reload);
})();
</script>
@endpush
