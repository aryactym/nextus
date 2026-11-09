<x-layouts.public
    title="Blog"
    description="Artikel dan insight seputar filantropi Islam, pengelolaan program, dan pengembangan diri."
    active-page="blog"
    canonical="{{ route('blog.index') }}"
>

<section class="course-hero" aria-labelledby="h-blog">
    <div class="container-xl">
        <h1 id="h-blog">Blog</h1>
        <p class="lead-text">Artikel dan insight dari tim {{ config('app.name') }}</p>
    </div>
</section>

<section class="section" style="padding-top:clamp(2rem,4vw,3rem)">
    <div class="container-xl">
        @if ($posts->isEmpty())
            <p class="text-secondary text-center py-5">Belum ada artikel yang diterbitkan.</p>
        @else
            <div class="row row-cards g-4">
                @foreach ($posts as $post)
                    <div class="col-md-6 col-lg-4">
                        <article class="course-card h-100">
                            @if ($post->thumbnail)
                                <img src="{{ route('media.thumbnail', ['path' => $post->thumbnail]) }}"
                                     alt="{{ $post->title }}" class="course-img" loading="lazy">
                            @else
                                <div class="course-img bg-light d-flex align-items-center justify-content-center text-muted" style="aspect-ratio:16/9">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z"/></svg>
                                </div>
                            @endif
                            <div class="card-body d-flex flex-column">
                                <div class="course-meta mb-2">
                                    <span>{{ $post->published_at?->format('d M Y') }}</span>
                                    <span>{{ $post->author?->name }}</span>
                                </div>
                                <h3 class="mb-2">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="text-reset stretched-link">
                                        {{ $post->title }}
                                    </a>
                                </h3>
                                @if ($post->meta_description)
                                    <p class="text-muted small mb-0 flex-grow-1">{{ $post->meta_description }}</p>
                                @endif
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>

            @if ($posts->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    {{ $posts->links('vendor.pagination.tabler') }}
                </div>
            @endif
        @endif
    </div>
</section>

</x-layouts.public>
