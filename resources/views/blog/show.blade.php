<x-layouts.public
    :title="$post->title"
    :description="$post->meta_description ?? ''"
    active-page="blog"
    :canonical="route('blog.show', $post->slug)"
    :og-image="$post->thumbnail ? route('media.thumbnail', ['path' => $post->thumbnail]) : null"
>

@php
    $shareUrl  = urlencode(route('blog.show', $post->slug));
    $shareText = urlencode('"' . $post->title . '" — ' . config('app.name'));
@endphp

{{-- Hero --}}
<section class="course-hero" aria-labelledby="h-post">
    <div class="container-xl">
        <nav class="crumb" aria-label="Breadcrumb">
            <ol>
                <li><a href="{{ route('home') }}">Beranda</a></li>
                <li><a href="{{ route('blog.index') }}">Blog</a></li>
                <li aria-current="page">{{ $post->title }}</li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-lg-8">
                <h1 id="h-post">{{ $post->title }}</h1>
                <div class="chips">
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="7" r="4"/><path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/></svg>
                        {{ $post->author?->name }}
                    </li>
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        {{ $post->published_at?->translatedFormat('d F Y') }}
                    </li>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Konten --}}
<section class="section" style="padding-top:clamp(2rem,4vw,3rem)">
    <div class="container-xl">
        <div class="row g-5">
            <div class="col-lg-8 order-2 order-lg-1">

                @if ($post->thumbnail)
                    <img src="{{ route('media.thumbnail', ['path' => $post->thumbnail]) }}"
                         alt="{{ $post->title }}"
                         class="media-img mb-4" loading="eager">
                @endif

                <div class="blog-content">
                    {!! $post->content !!}
                </div>

                {{-- Share --}}
                <div class="share-widget mt-5" x-data="{ copied: false }">
                    <p class="share-label">Bagikan artikel ini</p>
                    <div class="share-buttons">
                        <a href="https://wa.me/?text={{ $shareText }}%20{{ $shareUrl }}"
                           target="_blank" rel="noopener noreferrer"
                           class="share-btn share-btn--wa" aria-label="Bagikan ke WhatsApp">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                            <span>WhatsApp</span>
                        </a>
                        <a href="https://x.com/intent/tweet?text={{ $shareText }}&url={{ $shareUrl }}"
                           target="_blank" rel="noopener noreferrer"
                           class="share-btn share-btn--x" aria-label="Bagikan ke X">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.738l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            <span>X</span>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}"
                           target="_blank" rel="noopener noreferrer"
                           class="share-btn share-btn--li" aria-label="Bagikan ke LinkedIn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                            <span>LinkedIn</span>
                        </a>
                        <button type="button" class="share-btn share-btn--copy" aria-label="Salin tautan"
                                @click="navigator.clipboard.writeText('{{ route('blog.show', $post->slug) }}').then(() => { copied = true; setTimeout(() => copied = false, 2000) })">
                            <template x-if="!copied">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                            </template>
                            <template x-if="copied">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                            </template>
                            <span x-text="copied ? 'Tersalin!' : 'Salin tautan'"></span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <aside class="col-lg-4 order-1 order-lg-2">
                <div class="enroll" style="position:sticky;top:6rem;">
                    <div class="body">
                        <div class="subheader mb-2">Tentang penulis</div>
                        <div class="fw-semibold mb-1">{{ $post->author?->name }}</div>
                        <div class="text-muted small mb-3">Tim {{ config('app.name') }}</div>

                        @if ($related->isNotEmpty())
                            <hr>
                            <div class="subheader mb-2">Artikel lainnya</div>
                            @foreach ($related as $rel)
                                <a href="{{ route('blog.show', $rel->slug) }}"
                                   class="d-block text-reset mb-3 text-decoration-none">
                                    <div class="fw-medium lh-sm">{{ $rel->title }}</div>
                                    <div class="text-muted small mt-1">{{ $rel->published_at?->format('d M Y') }}</div>
                                </a>
                            @endforeach
                        @endif

                        <hr>
                        <a href="{{ route('blog.index') }}" class="btn btn-outline-primary w-100 btn-sm">
                            Semua artikel
                        </a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

@push('head')
<script type="application/ld+json">
@php
    echo json_encode([
        '@context'      => 'https://schema.org',
        '@type'         => 'BlogPosting',
        'headline'      => $post->title,
        'description'   => $post->meta_description,
        'datePublished' => $post->published_at?->toIso8601String(),
        'author'        => ['@type' => 'Person', 'name' => $post->author?->name],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp
</script>
@endpush

</x-layouts.public>
