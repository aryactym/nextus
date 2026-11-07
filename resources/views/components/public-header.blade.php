{{--
    x-public-header — Header publik NextUs (sticky, responsive)

    Props:
      $activePage — nama route aktif untuk aria-current ("catalog", "schedule", "verify", dll.)
                    Default: kosong (tidak ada yang aktif)
--}}
@props(['activePage' => ''])

@php
    $locale = app()->getLocale();
    $navItems = [
        ['route' => 'catalog.index',   'key' => 'catalog',  'label' => __('Katalog')],
        ['route' => 'jadwal',          'key' => 'schedule', 'label' => __('Jadwal')],
        ['route' => 'verifikasi',      'key' => 'verify',   'label' => __('Verifikasi sertifikat')],
    ];
@endphp

<a class="skip-link" href="#konten">Lewati ke konten utama</a>

<header class="site-header">
  <div class="container-xl">
    <div class="d-flex flex-wrap align-items-center justify-content-between py-3 gap-3">

      {{-- Brand --}}
      <a class="brand-mark" href="{{ route('home') }}" aria-label="{{ config('app.name') }}, ke beranda">
        <img
          class="brand-logo"
          src="{{ asset('images/logo-nextus.svg') }}"
          alt="{{ config('app.name') }}"
          height="44"
          onerror="this.src='{{ asset('images/logo-nextus.png') }}'"
        >
      </a>

      {{-- Navigasi utama --}}
      <nav aria-label="Navigasi utama">
        <ul class="nav">
          @foreach ($navItems as $item)
            @if (Route::has($item['route']))
              @php $isCurrent = $activePage === $item['key'] || request()->routeIs($item['route']); @endphp
              <li class="nav-item">
                <a
                  class="nav-link{{ $isCurrent ? ' active' : '' }}"
                  href="{{ route($item['route']) }}"
                  @if ($isCurrent) aria-current="page" @endif
                >{{ $item['label'] }}</a>
              </li>
            @endif
          @endforeach
        </ul>
      </nav>

      {{-- Bahasa + Auth --}}
      <div class="d-flex align-items-center gap-2">
        <div class="dropdown">
          <button
            class="btn btn-ghost-secondary dropdown-toggle"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
          ><span class="visually-hidden">Bahasa: </span>{{ $locale === 'en' ? 'English' : 'Indonesia' }}</button>
          <div class="dropdown-menu">
            <a class="dropdown-item{{ $locale === 'id' ? ' active' : '' }}" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('id') }}" hreflang="id">Indonesia</a>
            <a class="dropdown-item{{ $locale === 'en' ? ' active' : '' }}" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('en') }}" hreflang="en">English</a>
          </div>
        </div>

        @auth
          <a class="btn btn-outline-primary" href="{{ route('learning.dashboard') }}">Belajar saya</a>
          @can('manage courses')
            <a class="btn btn-ghost-secondary" href="{{ route('admin.dashboard') }}">Admin</a>
          @endcan
          <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-ghost-secondary" type="submit">Keluar</button>
          </form>
        @else
          <a class="btn btn-outline-primary" href="{{ route('login') }}"
            @if ($activePage === 'login') aria-current="page" @endif
          >Masuk</a>
          <a class="btn btn-primary" href="{{ route('register') }}"
            @if ($activePage === 'register') aria-current="page" @endif
          >Daftar gratis</a>
        @endauth
      </div>

    </div>
  </div>
</header>

