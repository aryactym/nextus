<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-navbar-position="vertical" data-bs-layout="fluid">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ $seo['title'] ?? 'Panel Admin' }} · {{ config('app.name') }}</title>
    <meta name="description" content="{{ $seo['description'] ?? '' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- NextUs brand: Plus Jakarta Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tabler UI (fluid vertical layout) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.7.3/dist/css/tabler.min.css">

    <style>
        :root {
            --nu-zamrud: #0E6B5C;
            --nu-zamrud-dark: #0C5045;
            --nu-malam: #0A2A28;
            --nu-fajar: #F5A524;
            --nu-kertas: #F5F7F4;
            --nu-kabut: #E3E9E6;
            --nu-batu: #546663;

            --tblr-primary: #0E6B5C;
            --tblr-primary-rgb: 14, 107, 92;
            --tblr-primary-fg: #fff;
            --tblr-primary-darken: #0C5045;
            --tblr-link-color: #0E6B5C;
            --tblr-link-hover-color: #0C5045;
            --tblr-font-sans-serif: 'Plus Jakarta Sans', system-ui, -apple-system, 'Segoe UI', sans-serif;
        }

        body {
            font-family: var(--tblr-font-sans-serif);
            background-color: var(--nu-kertas);
        }

        /* Sidebar NextUs: gradient zamrud -> malam */
        .app-sidebar {
            --tblr-navbar-bg: transparent;
            --tblr-navbar-color: rgba(255, 255, 255, .72);
            --tblr-navbar-hover-color: #fff;
            --tblr-navbar-active-color: #fff;
            --tblr-navbar-active-bg: rgba(255, 255, 255, .12);
            --tblr-navbar-active-border-color: var(--nu-fajar);
            --tblr-navbar-border-color: rgba(255, 255, 255, .08);
            background-image: linear-gradient(180deg, var(--nu-zamrud) 0%, var(--nu-malam) 100%);
        }

        .app-sidebar .navbar-brand-image {
            height: 1.9rem;
            width: auto;
        }

        .app-sidebar .navbar-nav .nav-link {
            border-radius: .5rem;
            margin: 0 .5rem;
        }

        .app-sidebar .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, .08);
            color: #fff;
        }

        .app-sidebar .navbar-nav .nav-item.active > .nav-link,
        .app-sidebar .navbar-nav .nav-link.active {
            background-color: rgba(255, 255, 255, .12);
            color: #fff;
        }

        .app-sidebar .nav-section-title {
            color: rgba(255, 255, 255, .45);
            font-weight: 600;
            letter-spacing: .04em;
        }

        .app-sidebar .navbar-footer {
            border-top-color: rgba(255, 255, 255, .12);
        }

        .app-sidebar .navbar-footer .nav-link .avatar {
            background-color: var(--nu-fajar);
            color: var(--nu-malam);
            font-weight: 700;
        }

        /* Area konten fluid */
        .page-body {
            padding-top: 1rem;
            padding-bottom: 1.5rem;
        }

        .page-body .card {
            box-shadow: 0 1px 2px rgba(10, 42, 40, .06);
        }

        .footer-transparent {
            background-color: transparent;
        }
    </style>

    @stack('styles')
    @vite('resources/js/app.js')
</head>
<body>
<div class="page">

    {{-- ==================== SIDEBAR ==================== --}}
    <aside class="navbar navbar-vertical navbar-expand-lg app-sidebar">
        <div class="container-fluid">

            {{-- Toggler (mobile) --}}
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
                    aria-controls="sidebar-menu" aria-expanded="false" aria-label="Buka menu navigasi">
                <span class="navbar-toggler-icon"></span>
            </button>

            {{-- Brand --}}
            <div class="navbar-brand">
                <a href="{{ route('admin.dashboard') }}" aria-label="{{ config('app.name') }}">
                    <img src="{{ asset('images/logo-nextus-putih.svg') }}" alt="{{ config('app.name') }}"
                         class="navbar-brand-image">
                </a>
            </div>

            {{-- User menu di bawah sidebar --}}
            @auth
                <div class="navbar-footer">
                    <ul class="navbar-nav">
                        <li class="nav-item dropup">
                            <a href="#" class="nav-link" data-bs-toggle="dropdown" aria-label="Menu pengguna">
                                <span class="avatar avatar-sm">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 2)) }}</span>
                                <span class="nav-link-title">
                                    {{ auth()->user()->name }}
                                    <div class="small text-secondary">{{ auth()->user()->email }}</div>
                                </span>
                            </a>
                            <div class="dropdown-menu">
                                <div class="dropdown-header">
                                    Masuk sebagai <strong>{{ auth()->user()->name }}</strong>
                                </div>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('home') }}" target="_blank" rel="noopener">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" class="icon dropdown-item-icon" aria-hidden="true">
                                        <path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6"/>
                                        <path d="M11 13l9 -9"/>
                                        <path d="M15 4h5v5"/>
                                    </svg>
                                    Lihat situs
                                </a>
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                             stroke-linejoin="round" class="icon dropdown-item-icon" aria-hidden="true">
                                            <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"/>
                                            <path d="M9 12h12l-3 -3"/>
                                            <path d="M18 15l3 -3"/>
                                        </svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            @endauth

            {{-- Navigasi utama --}}
            <nav class="collapse navbar-collapse" id="sidebar-menu" aria-label="Navigasi utama">
                <ul class="navbar-nav pt-lg-3">
                    <li class="nav-section-title">Ringkasan</li>
                    <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                           href="{{ route('admin.dashboard') }}">
                            <span class="nav-link-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="icon" aria-hidden="true">
                                    <path d="M5 12l-2 0l9 -9l9 9l-2 0"/>
                                    <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/>
                                    <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"/>
                                </svg>
                            </span>
                            <span class="nav-link-title">Dashboard</span>
                        </a>
                    </li>

                    @can('manage courses')
                        <li class="nav-section-title">Katalog</li>
                        <li class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                            <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
                               href="{{ route('admin.categories.index') }}">
                                <span class="nav-link-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" class="icon" aria-hidden="true">
                                        <path d="M4 4h6v6h-6z"/>
                                        <path d="M14 4h6v6h-6z"/>
                                        <path d="M4 14h6v6h-6z"/>
                                        <path d="M14 14h6v6h-6z"/>
                                    </svg>
                                </span>
                                <span class="nav-link-title">Kategori</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
                            <a class="nav-link {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}"
                               href="{{ route('admin.courses.index') }}">
                                <span class="nav-link-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" class="icon" aria-hidden="true">
                                        <path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/>
                                        <path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/>
                                        <path d="M3 6l0 13"/>
                                        <path d="M12 6l0 13"/>
                                        <path d="M21 6l0 13"/>
                                    </svg>
                                </span>
                                <span class="nav-link-title">Kursus</span>
                            </a>
                        </li>
                    @endcan

                    @can('manage pages')
                        <li class="nav-section-title">Konten</li>
                        <li class="nav-item {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                            <a class="nav-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}"
                               href="{{ route('admin.pages.index') }}">
                                <span class="nav-link-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" class="icon" aria-hidden="true">
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                                        <path d="M9 9l1 0"/>
                                        <path d="M9 13l6 0"/>
                                        <path d="M9 17l6 0"/>
                                    </svg>
                                </span>
                                <span class="nav-link-title">Halaman</span>
                            </a>
                        </li>
                    @endcan

                    <li class="nav-section-title">Situs</li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}" target="_blank" rel="noopener">
                            <span class="nav-link-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="icon" aria-hidden="true">
                                    <path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6"/>
                                    <path d="M11 13l9 -9"/>
                                    <path d="M15 4h5v5"/>
                                </svg>
                            </span>
                            <span class="nav-link-title">Lihat situs</span>
                        </a>
                    </li>

                    @can('manage settings')
                        <li class="nav-section-title">Sistem</li>
                        <li class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                            <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
                               href="{{ route('admin.settings.edit') }}">
                                <span class="nav-link-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" class="icon" aria-hidden="true">
                                        <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"/>
                                        <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"/>
                                    </svg>
                                </span>
                                <span class="nav-link-title">Pengaturan</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </nav>
        </div>
    </aside>

    {{-- ==================== KONTEN ==================== --}}
    <div class="page-wrapper">
        <div class="page-body">
            <div class="container-fluid">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>
                @endif

                @if (session('status'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>

        <footer class="footer footer-transparent d-print-none">
            <div class="container-fluid">
                <div class="row text-center align-items-center flex-row-reverse">
                    <div class="col-lg-auto ms-lg-auto">
                        <ul class="list-inline list-inline-dots mb-0">
                            <li class="list-inline-item"><a href="{{ route('syarat') }}">Syarat</a></li>
                            <li class="list-inline-item"><a href="{{ route('privasi') }}">Privasi</a></li>
                        </ul>
                    </div>
                    <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                        <div class="text-secondary">© {{ date('Y') }} {{ config('app.name') }}</div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.7.3/dist/js/tabler.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js" defer></script>
@stack('scripts')
</body>
</html>
