<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? config('app.name') }}{{ isset($title) ? ' | ' . config('app.name') : '' }}</title>
  <meta name="theme-color" content="#0A2A28">
  <meta name="description" content="{{ $description ?? 'Platform belajar online untuk orang muda yang ingin memahami dan mempraktikkan filantropi Islam.' }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @if (! empty($googleSearchConsole))
    <meta name="google-site-verification" content="{{ $googleSearchConsole }}">
  @endif

  {{-- Analytics & Pixel (dari Pengaturan admin) --}}
  @if (! empty($googleAnalyticsId))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleAnalyticsId }}"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag() { dataLayer.push(arguments); }
      gtag('js', new Date());
      gtag('config', @json($googleAnalyticsId));
    </script>
  @endif

  @if (! empty($facebookPixelId))
    <script>
      !function(f,b,e,v,n,t,s)
      {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
      n.callMethod.apply(n,arguments):n.queue.push(arguments)};
      if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
      n.queue=[];t=b.createElement(e);t.async=!0;
      t.src=v;s=b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t,s)}(window,document,'script',
      'https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', @json($facebookPixelId));
      fbq('track', 'PageView');
    </script>
  @endif

  {{-- OG / SEO --}}
  @if (isset($ogImage))
    <meta property="og:image" content="{{ $ogImage }}">
  @endif
  @if (isset($canonical))
    <link rel="canonical" href="{{ $canonical }}">
  @endif

  {{-- Favicon (dari Pengaturan admin) --}}
  @if (! empty($favicon))
    <link rel="icon" type="image/{{ Storage::disk('public')->mimeType($favicon) }}" href="{{ Storage::disk('public')->url($favicon) }}">
    <link rel="apple-touch-icon" href="{{ Storage::disk('public')->url($favicon) }}">
  @endif

  {{-- Fonts: Plus Jakarta Sans --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  {{-- Tabler CSS --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.7.3/dist/css/tabler.min.css">

  {{-- NextUs brand tokens & public styles --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  @stack('head')
</head>
<body>

@if (! empty($facebookPixelId))
  <noscript>
    <img height="1" width="1" style="display:none" alt=""
         src="https://www.facebook.com/tr?id={{ $facebookPixelId }}&amp;ev=PageView&amp;noscript=1">
  </noscript>
@endif

<x-public-header :active-page="$activePage ?? ''" />

<main id="konten">
  {{ $slot }}
</main>

<x-public-footer />

@if ($sponsors->isNotEmpty())
    <section class="section section-alt">
        <div class="container-xl">
            <h2 class="text-center mb-4">Mitra &amp; Sponsor Kami</h2>
            <div class="sponsor-slider d-flex flex-wrap justify-content-center gap-4 align-items-center">
                @foreach ($sponsors as $sponsor)
                    @if ($sponsor->url)
                        <a href="{{ $sponsor->url }}" target="_blank" rel="noopener"
                           class="d-block text-decoration-none">
                            <img src="{{ Storage::disk('public')->url($sponsor->logo) }}"
                                 alt="{{ $sponsor->name }}"
                                 class="sponsor-logo img-fluid"
                                 style="max-height: 50px; max-width: 180px; object-fit: contain; opacity: 0.85; transition: opacity 0.2s;"
                                 onmouseover="this.style.opacity='1'"
                                 onmouseout="this.style.opacity='0.85'">
                        </a>
                    @else
                        <img src="{{ Storage::disk('public')->url($sponsor->logo) }}"
                             alt="{{ $sponsor->name }}"
                             class="sponsor-logo img-fluid"
                             style="max-height: 50px; max-width: 180px; object-fit: contain; opacity: 0.85; transition: opacity 0.2s;"
                             onmouseover="this.style.opacity='1'"
                             onmouseout="this.style.opacity='0.85'">
                    @endif
                @endforeach
            </div>
        </div>
    </section>
@endif

<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.7.3/dist/js/tabler.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js" defer></script>
@stack('scripts')

</body>
</html>

