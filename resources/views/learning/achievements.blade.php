<x-layouts.public
    title="Pencapaian"
    description="Level dan badge yang telah kamu peroleh."
    active-page="catalog"
>

  <section class="page-hero">
    <div class="container-xl">
      <ol class="crumbs">
        <li><a href="{{ route('home') }}">Beranda</a></li>
        <li><a href="{{ route('learning.dashboard') }}">Belajar saya</a></li>
        <li aria-current="page">Pencapaian</li>
      </ol>
      <h1>Pencapaian</h1>
      <p>
        @if ($level['next_threshold'] !== null)
          Level {{ $level['level'] }} {{ $level['name_id'] }}, {{ $totalXp }}/{{ $level['next_threshold'] }} XP
        @else
          Level {{ $level['level'] }} {{ $level['name_id'] }}, {{ $totalXp }} XP
        @endif
      </p>
    </div>
  </section>

  <section class="section">
    <div class="container-xl">
      <div class="row g-4">
        @foreach ($badges as $item)
          @php
            $badge = $item['badge'];
            $tier = $item['tier'] ?? ($badge['tiers'] ? array_key_first($badge['tiers']) : 'default');
            $earned = $item['earned'];
          @endphp
          <div class="col-md-4">
            <div class="card h-100">
              <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                <x-badge
                  :badge="$badge"
                  :tier="$tier"
                  :earned="$earned"
                  :progress-current="$item['progress_current']"
                  :progress-total="$item['progress_total']"
                  size="96"
                />
                <h2 class="h3 mt-3">{{ $badge['name_id'] }}</h2>
                <p class="text-secondary mb-2">{{ $badge['description_id'] }}</p>
                @if ($earned)
                  <span class="badge bg-green-lt">Diperoleh @if ($item['tier']){{ ucfirst($item['tier']) }}@endif</span>
                @else
                  <span class="badge bg-secondary-lt">Terkunci</span>
                @endif
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

</x-layouts.public>
