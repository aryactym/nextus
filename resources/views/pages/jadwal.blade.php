<x-layouts.public
    title="Jadwal"
    description="Webinar, pelatihan, dan batch kursus yang akan datang. Semua waktu dalam WIB."
    active-page="schedule"
    canonical="{{ route('jadwal') }}"
>

@php
    $typeBadges = ['webinar' => 'bg-blue-lt', 'pelatihan' => 'bg-green-lt', 'batch' => 'bg-purple-lt'];
    $typeLabels = ['webinar' => 'Webinar', 'pelatihan' => 'Pelatihan', 'batch' => 'Batch kursus'];
    $statusBadges = ['open' => 'bg-green-lt', 'soon' => 'bg-azure-lt', 'full' => 'bg-orange-lt', 'closed' => 'bg-gray-lt'];
    $statusLabels = ['open' => 'Pendaftaran dibuka', 'soon' => 'Segera dibuka', 'full' => 'Penuh', 'closed' => 'Selesai'];
@endphp

<section class="page-hero" aria-labelledby="h-jadwal">
    <div class="container-xl">
        <nav class="crumb" aria-label="Breadcrumb">
            <ol>
                <li><a href="{{ route('home') }}">Beranda</a></li>
                <li aria-current="page">Jadwal</li>
            </ol>
        </nav>
        <h1 id="h-jadwal">Jadwal</h1>
        <p>Webinar, pelatihan, dan batch kursus yang akan datang. Semua waktu dalam WIB.</p>
    </div>
</section>

<section class="section" style="padding-top:clamp(2rem,4vw,3rem)">
    <div class="container-xl">

        {{-- Filter bar --}}
        <form method="GET" action="{{ route('jadwal') }}" class="bar mb-4" role="search">
            <div>
                <label class="form-label" for="q">Cari jadwal</label>
                <input class="form-control" id="q" name="q" type="search"
                       value="{{ $q }}" placeholder="Misalnya: zakat">
            </div>
            <div>
                <label class="form-label" for="type">Jenis</label>
                <select class="form-select" id="type" name="type">
                    <option value="">Semua jenis</option>
                    @foreach ($typeLabels as $val => $label)
                        <option value="{{ $val }}" @selected($type === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label" for="format">Format</label>
                <select class="form-select" id="format" name="format">
                    <option value="">Semua format</option>
                    <option value="daring" @selected($format === 'daring')>Daring</option>
                    <option value="luring" @selected($format === 'luring')>Luring</option>
                </select>
            </div>
            <div>
                <label class="form-label" for="status">Pendaftaran</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Semua</option>
                    <option value="open"  @selected($status === 'open')>Dibuka</option>
                    <option value="soon"  @selected($status === 'soon')>Segera dibuka</option>
                    <option value="full"  @selected($status === 'full')>Penuh</option>
                </select>
            </div>
            <div class="d-flex align-items-end gap-2">
                <button class="btn btn-primary" type="submit">Tampilkan</button>
                @if ($q || $type || $format || $status || $showPast)
                    <a href="{{ route('jadwal') }}" class="btn btn-link">Reset</a>
                @endif
            </div>
        </form>

        {{-- Toggle tampilkan yang sudah lewat --}}
        <div class="bar-foot">
            <p class="text-muted">
                @if ($events->isEmpty())
                    Tidak ada jadwal yang cocok
                @else
                    {{ $events->count() }} jadwal ditampilkan
                @endif
            </p>
            <label class="form-check mb-0">
                <input class="form-check-input" type="checkbox" id="past-toggle"
                       @checked($showPast)>
                <span class="form-check-label">Tampilkan yang sudah lewat</span>
            </label>
        </div>

        {{-- Event list grouped by month --}}
        @if ($grouped->isEmpty())
            <div class="empty mt-4">
                <h2>Tidak ada jadwal yang cocok</h2>
                <p class="text-secondary">Coba ubah filter atau centang "Tampilkan yang sudah lewat".</p>
                <a class="btn btn-primary" href="{{ route('jadwal') }}">Hapus semua filter</a>
            </div>
        @else
            @foreach ($grouped as $month => $monthEvents)
                <div class="month">
                    <h2>{{ $month }}</h2>
                    @foreach ($monthEvents as $event)
                        @php
                            $isPast = $event->starts_at->isPast();
                        @endphp
                        <article class="event {{ $isPast ? 'past' : '' }}" aria-label="{{ $event->title }}">
                            {{-- Date block --}}
                            <div class="date" aria-hidden="true">
                                <b>{{ $event->starts_at->format('d') }}</b>
                                <span>{{ $event->starts_at->translatedFormat('M') }}</span>
                            </div>

                            {{-- Info --}}
                            <div>
                                <div class="d-flex flex-wrap gap-2 mb-1">
                                    <span class="badge {{ $typeBadges[$event->type] ?? 'bg-secondary-lt' }}">
                                        {{ $typeLabels[$event->type] ?? $event->type }}
                                    </span>
                                    <span class="badge bg-secondary-lt">{{ ucfirst($event->format) }}</span>
                                    <span class="badge {{ $statusBadges[$event->status] ?? 'bg-gray-lt' }}">
                                        {{ $statusLabels[$event->status] ?? $event->status }}
                                    </span>
                                </div>

                                <h3 class="mb-1">{{ $event->title }}</h3>

                                <ul class="event-info" aria-label="Informasi jadwal">
                                    <li>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                                             stroke-linejoin="round" aria-hidden="true">
                                            <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/>
                                        </svg>
                                        {{ $event->starts_at->translatedFormat('l, d F Y') }},
                                        {{ $event->starts_at->format('H:i') }}–{{ $event->ends_at->format('H:i') }} WIB
                                    </li>
                                    @if ($event->location)
                                        <li>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                                 fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                                                 stroke-linejoin="round" aria-hidden="true">
                                                <path d="M12 11m-3 0a3 3 0 1 0 6 0a3 3 0 1 0-6 0"/>
                                                <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1-2.827 0l-4.244-4.243a8 8 0 1 1 11.314 0z"/>
                                            </svg>
                                            {{ $event->location }}
                                        </li>
                                    @endif
                                    @if ($event->quota)
                                        <li>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                                 fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                                                 stroke-linejoin="round" aria-hidden="true">
                                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                                <circle cx="9" cy="7" r="4"/>
                                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                            </svg>
                                            Kuota {{ number_format($event->quota) }} peserta
                                        </li>
                                    @endif
                                </ul>

                                @if ($event->description)
                                    <p class="small text-muted mt-2 mb-0">{{ Str::limit(strip_tags($event->description), 160) }}</p>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="event-actions">
                                @if ($event->registration_url && ! $isPast && $event->status === 'open')
                                    <a href="{{ $event->registration_url }}" target="_blank" rel="noopener"
                                       class="btn btn-primary">Daftar sekarang</a>
                                @elseif ($event->status === 'soon')
                                    <span class="btn btn-outline-primary disabled">Segera dibuka</span>
                                @elseif ($event->status === 'full')
                                    <span class="btn btn-outline-secondary disabled">Penuh</span>
                                @elseif ($event->course)
                                    <a href="{{ route('catalog.show', $event->course->slug) }}"
                                       class="btn btn-outline-primary">Lihat kursus</a>
                                @endif
                                <a href="{{ route('jadwal.ics', $event->uuid) }}"
                                   class="btn btn-ghost-secondary btn-sm"
                                   title="Tambahkan ke kalender (.ics)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" aria-hidden="true">
                                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                    .ics
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endforeach
        @endif

        <div class="note-box mt-4">
            <p><strong>Jadwal bisa berubah.</strong> Perubahan diinformasikan melalui email kepada peserta terdaftar.
               Butuh bantuan? <a href="{{ route('home') }}">Hubungi kami</a>.</p>
        </div>
    </div>
</section>

@push('scripts')
<script>
(function () {
    var toggle = document.getElementById('past-toggle');
    if (! toggle) { return; }
    toggle.addEventListener('change', function () {
        var url = new URL(window.location.href);
        if (this.checked) {
            url.searchParams.set('past', '1');
        } else {
            url.searchParams.delete('past');
        }
        window.location.href = url.toString();
    });
}());
</script>
@endpush

</x-layouts.public>
