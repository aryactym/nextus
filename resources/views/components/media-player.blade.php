{{--
    x-media-player — pemutar media untuk pelajaran (video, teks, PDF)

    Props:
      $type      — 'video' | 'text' | 'pdf'
      $videoUrl  — URL video (YouTube / Vimeo / Bunny); wajib jika $type='video'
      $content   — konten teks (HTML yang sudah di-sanitasi); untuk $type='text'
      $fileUrl   — URL bertanda (signed URL) PDF; untuk $type='pdf'
      $title     — judul pelajaran untuk aria-label
--}}
@props([
    'type'     => 'text',
    'videoUrl' => null,
    'content'  => null,
    'fileUrl'  => null,
    'title'    => 'Pelajaran',
])

@php
    $allowedHosts = ['youtube.com', 'www.youtube.com', 'youtu.be', 'vimeo.com', 'player.vimeo.com', 'iframe.mediadelivery.net'];

    $embedUrl = null;
    if ($type === 'video' && $videoUrl) {
        $host = parse_url($videoUrl, PHP_URL_HOST);
        if (in_array($host, $allowedHosts, true)) {
            // Konversi URL YouTube watch ke embed
            if (str_contains($host, 'youtube') || $host === 'youtu.be') {
                $videoId = null;
                if (preg_match('/[?&]v=([^&]+)/', $videoUrl, $m)) {
                    $videoId = $m[1];
                } elseif ($host === 'youtu.be') {
                    $videoId = ltrim(parse_url($videoUrl, PHP_URL_PATH), '/');
                }
                $embedUrl = $videoId ? "https://www.youtube.com/embed/{$videoId}" : null;
            } elseif (str_contains($host, 'vimeo')) {
                $path = ltrim(parse_url($videoUrl, PHP_URL_PATH), '/');
                $embedUrl = "https://player.vimeo.com/video/{$path}";
            } else {
                $embedUrl = $videoUrl;
            }
        }
    }
@endphp

<div class="media-player" role="region" aria-label="{{ $title }}">
    @if ($type === 'video')
        @if ($embedUrl)
            <div class="ratio ratio-16x9">
                <iframe
                    src="{{ $embedUrl }}"
                    title="{{ $title }}"
                    allowfullscreen
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    loading="lazy"
                    referrerpolicy="strict-origin-when-cross-origin"
                ></iframe>
            </div>
        @else
            <div class="alert alert-warning">URL video tidak didukung atau tidak valid.</div>
        @endif

    @elseif ($type === 'pdf')
        @if ($fileUrl)
            <div class="ratio" style="--bs-aspect-ratio: 75%;">
                <iframe
                    src="{{ $fileUrl }}"
                    title="{{ $title }}"
                    loading="lazy"
                    style="border:none"
                ></iframe>
            </div>
            <a href="{{ $fileUrl }}" class="btn btn-sm btn-outline-secondary mt-2" download>
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Unduh PDF
            </a>
        @else
            <div class="alert alert-warning">File PDF tidak tersedia.</div>
        @endif

    @elseif ($type === 'text')
        <div class="lesson-content prose">
            {!! $content !!}
        </div>

    @else
        <div class="alert alert-secondary">Tipe konten tidak dikenal: {{ $type }}</div>
    @endif
</div>
