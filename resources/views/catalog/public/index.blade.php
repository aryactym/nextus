<x-layouts.public
    title="Katalog kursus"
    description="Temukan kursus filantropi Islam sesuai topik, tingkat, dan kebutuhan kamu."
    active-page="catalog"
>

  @php
    $levelLabels = ['beginner' => 'Pemula', 'intermediate' => 'Menengah', 'advanced' => 'Lanjutan'];
    $accessLabels = ['free' => 'Gratis', 'donation' => 'Donasi untuk masuk', 'paid' => 'Berbayar'];
    $durationLabels = ['s' => 'Kurang dari 3 jam', 'm' => '3–5 jam', 'l' => 'Lebih dari 5 jam'];
  @endphp

  <section class="page-hero">
    <div class="container-xl">
      <ol class="crumbs">
        <li><a href="{{ route('home') }}">Beranda</a></li>
        <li aria-current="page">Katalog</li>
      </ol>
      <h1>Katalog kursus</h1>
      <p>Temukan kursus filantropi Islam sesuai topik, tingkat, dan kebutuhan kamu.</p>
    </div>
  </section>

  <section class="section">
    <div class="container-xl">
      <div class="row g-4">

        {{-- ===== SIDEBAR FILTER ===== --}}
        <aside class="col-lg-3" aria-label="Filter kursus">
          <button
            class="btn btn-outline-secondary w-100 d-lg-none mb-3"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#filters"
            aria-expanded="false"
            aria-controls="filters"
          >Tampilkan filter</button>

          <form class="filters collapse d-lg-block" id="filters" method="get" action="{{ route('catalog.index') }}">

            <div class="mb-3">
              <label class="form-label fw-bold" for="q">Cari kursus</label>
              <input class="form-control" id="q" name="q" type="search" placeholder="Misalnya: zakat" value="{{ $filters['q'] }}">
            </div>

            <fieldset>
              <legend>Topik</legend>
              @foreach ($categories as $category)
                <div class="form-check">
                  <label class="form-check-label" for="t-{{ $category->id }}">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      id="t-{{ $category->id }}"
                      name="kategori[]"
                      value="{{ $category->slug }}"
                      {{ in_array($category->slug, $filters['kategori'], true) ? 'checked' : '' }}
                    >
                    <span>{{ $category->getTranslation('name', app()->getLocale()) }}</span>
                    <span class="count">({{ $counts['kategori'][$category->slug] ?? 0 }})</span>
                  </label>
                </div>
              @endforeach
            </fieldset>

            <fieldset>
              <legend>Tingkat</legend>
              @foreach ($levelLabels as $val => $label)
                <div class="form-check">
                  <label class="form-check-label" for="l-{{ $val }}">
                    <input class="form-check-input" type="checkbox" id="l-{{ $val }}" name="level[]" value="{{ $val }}"
                      {{ in_array($val, $filters['level'], true) ? 'checked' : '' }}>
                    <span>{{ $label }}</span>
                    <span class="count">({{ $counts['level'][$val] ?? 0 }})</span>
                  </label>
                </div>
              @endforeach
            </fieldset>

            <fieldset>
              <legend>Akses</legend>
              @foreach ($accessLabels as $val => $label)
                <div class="form-check">
                  <label class="form-check-label" for="a-{{ $val }}">
                    <input class="form-check-input" type="checkbox" id="a-{{ $val }}" name="akses[]" value="{{ $val }}"
                      {{ in_array($val, $filters['akses'], true) ? 'checked' : '' }}>
                    <span>{{ $label }}</span>
                    <span class="count">({{ $counts['akses'][$val] ?? 0 }})</span>
                  </label>
                </div>
              @endforeach
            </fieldset>

            <fieldset>
              <legend>Durasi</legend>
              @foreach ($durationLabels as $val => $label)
                <div class="form-check">
                  <label class="form-check-label" for="d-{{ $val }}">
                    <input class="form-check-input" type="checkbox" id="d-{{ $val }}" name="durasi[]" value="{{ $val }}"
                      {{ in_array($val, $filters['durasi'], true) ? 'checked' : '' }}>
                    <span>{{ $label }}</span>
                    <span class="count">({{ $counts['durasi'][$val] ?? 0 }})</span>
                  </label>
                </div>
              @endforeach
            </fieldset>

            <button class="btn btn-primary w-100 mt-2" type="submit">Terapkan filter</button>
            <button
              class="btn btn-outline-primary w-100 mt-2"
              type="button"
              onclick="window.location='{{ route('catalog.index') }}'"
            >Atur ulang filter</button>
          </form>
        </aside>

        {{-- ===== GRID KURSUS ===== --}}
        <div class="col-lg-9">

          <div class="toolbar">
            <p class="result-count" role="status" aria-live="polite">
              Menampilkan {{ $courses->count() }} dari {{ $courses->total() }} kursus
            </p>
            <div class="d-flex align-items-center gap-2">
              <label class="form-label mb-0" for="sort">Urutkan</label>
              <select class="form-select" id="sort" name="sort" form="filters"
                onchange="document.getElementById('filters').submit()">
                <option value="az" {{ request('sort', 'az') === 'az' ? 'selected' : '' }}>Nama A–Z</option>
                <option value="short" {{ request('sort') === 'short' ? 'selected' : '' }}>Durasi tersingkat</option>
                <option value="long" {{ request('sort') === 'long' ? 'selected' : '' }}>Durasi terpanjang</option>
              </select>
            </div>
          </div>

          {{-- Active filter chips --}}
          @if ($hasActiveFilters)
            <ul class="chips" aria-label="Filter aktif">
              @if ($filters['q'] !== '')
                <li><button type="button" onclick="removeFilter('q')">{{ $filters['q'] }}</button></li>
              @endif
              @foreach ($filters['kategori'] as $slug)
                @php $category = $categories->firstWhere('slug', $slug); @endphp
                <li><button type="button" onclick="removeFilter('kategori', '{{ $slug }}')">{{ $category?->getTranslation('name', app()->getLocale()) ?? $slug }}</button></li>
              @endforeach
              @foreach ($filters['level'] as $level)
                <li><button type="button" onclick="removeFilter('level', '{{ $level }}')">{{ $levelLabels[$level] ?? $level }}</button></li>
              @endforeach
              @foreach ($filters['akses'] as $access)
                <li><button type="button" onclick="removeFilter('akses', '{{ $access }}')">{{ $accessLabels[$access] ?? $access }}</button></li>
              @endforeach
              @foreach ($filters['durasi'] as $duration)
                <li><button type="button" onclick="removeFilter('durasi', '{{ $duration }}')">{{ $durationLabels[$duration] ?? $duration }}</button></li>
              @endforeach
            </ul>
          @endif

          @if ($courses->isEmpty())
            <div class="empty">
              <h2>Tidak ada kursus yang cocok</h2>
              <p class="text-secondary">Coba kurangi filter atau gunakan kata kunci lain.</p>
              <a class="btn btn-primary" href="{{ route('catalog.index') }}">Hapus semua filter</a>
            </div>
          @else
            <div class="row g-4">
              @foreach ($courses as $course)
                <article class="col-md-6 col-xl-4">
                  <div class="card course-card h-100">
                    @if ($course->thumbnail)
                      <img
                        class="course-img"
                        src="{{ route('media.thumbnail', ['path' => $course->thumbnail]) }}"
                        alt="{{ $course->getTranslation('title', app()->getLocale()) }}"
                        width="640" height="360" loading="lazy"
                      >
                    @else
                      <div class="course-img" style="background:var(--nu-zamrud-100);display:flex;align-items:center;justify-content:center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--nu-zamrud-400)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                      </div>
                    @endif
                    <div class="card-body">
                      @if ($course->category)
                        <span class="topic-label">{{ $course->category->getTranslation('name', app()->getLocale()) }}</span>
                      @endif
                      <span class="badge {{ $course->access_type === 'free' ? 'bg-green-lt' : ($course->access_type === 'donation' ? 'bg-azure-lt' : 'bg-orange-lt') }} badge-access mb-2">
                        {{ $accessLabels[$course->access_type] ?? $course->access_type }}
                      </span>
                      <h3 class="h3">
                        <a class="text-reset text-decoration-none stretched-link" href="{{ route('catalog.show', $course->slug) }}">
                          {{ $course->getTranslation('title', app()->getLocale()) }}
                        </a>
                      </h3>
                      @if ($course->summary)
                        <p class="text-secondary">{{ Str::limit($course->getTranslation('summary', app()->getLocale()), 100) }}</p>
                      @endif
                      <div class="course-meta">
                        @if ($course->level)
                          <span>{{ $levelLabels[$course->level] ?? $course->level }}</span>
                        @endif
                        @if ($course->duration_minutes)
                          <span>{{ round($course->duration_minutes / 60) }} jam</span>
                        @endif
                      </div>
                    </div>
                    <div class="card-footer">
                      {{ $course->certificate_type === 'free' ? 'Sertifikat gratis' : ($course->certificate_type === 'donation' ? 'Sertifikat via donasi' : 'Sertifikat berbayar') }}
                    </div>
                  </div>
                </article>
              @endforeach
            </div>

            <div class="mt-4">
              {{ $courses->withQueryString()->links('vendor.pagination.tabler') }}
            </div>
          @endif

        </div>
      </div>
    </div>
  </section>

</x-layouts.public>

@push('scripts')
<script>
function removeFilter(key, value) {
  var url = new URL(window.location.href);
  if (value === undefined) {
    url.searchParams.delete(key);
  } else {
    var existing = url.searchParams.getAll(key + '[]');
    url.searchParams.delete(key + '[]');
    existing.filter(function(v) { return v !== value; })
      .forEach(function(v) { url.searchParams.append(key + '[]', v); });
  }
  window.location.href = url.toString();
}

(function () {
  var form = document.getElementById('filters');
  if (!form) return;

  form.querySelectorAll('input[type="checkbox"]').forEach(function (input) {
    input.addEventListener('change', function () { form.submit(); });
  });

  var search = document.getElementById('q');
  var timer;
  if (search) {
    search.addEventListener('input', function () {
      clearTimeout(timer);
      timer = setTimeout(function () { form.submit(); }, 500);
    });
  }
})();
</script>
@endpush

