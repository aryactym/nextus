<x-layouts.public
    title="{{ $page->title }}"
    description="{{ $page->meta_description ?? $page->title }}"
    :active-page="$page->slug === 'syarat' ? 'terms' : 'privacy'"
>

  <section class="page-hero">
    <div class="container-xl">
      <ol class="crumbs">
        <li><a href="{{ route('home') }}">Beranda</a></li>
        <li aria-current="page">{{ $page->title }}</li>
      </ol>
      <h1>{{ $page->title }}</h1>
      @if ($page->meta_description)
        <p>{{ $page->meta_description }}</p>
      @endif
    </div>
  </section>

  <section class="section">
    <div class="container-xl">
      <div class="row g-4">
        @if (count($toc) > 0)
          <aside class="col-lg-3" aria-label="Isi halaman">
            <div class="card">
              <div class="card-body">
                <h2 class="h3">Isi halaman</h2>
                <ol class="list-group list-group-flush list-group-numbered">
                  @foreach ($toc as $item)
                    <li class="list-group-item">
                      <a class="text-reset text-decoration-none" href="#{{ $item['id'] }}">{{ $item['label'] }}</a>
                    </li>
                  @endforeach
                </ol>
              </div>
            </div>
          </aside>
        @endif

        <div class="{{ count($toc) > 0 ? 'col-lg-9' : 'col-12' }}">
          <article class="card">
            <div class="card-body legal-content">
              {!! $content !!}
            </div>
          </article>
        </div>
      </div>
    </div>
  </section>

</x-layouts.public>

