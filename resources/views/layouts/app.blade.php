<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $seo['title'] ?? config('app.name') }} · {{ config('app.name') }}</title>
    <meta name="description" content="{{ $seo['description'] ?? '' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Tabler UI (CDN selama development; ganti Vite build di produksi) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler-flags.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler-payments.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler-vintage.min.css">
    <style>
        .navbar { box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .sidebar { width: 250px; height: 100vh; position: fixed; top: 0; left: 0; background: #0f172a; color: #fff; overflow-y: auto; }
        .sidebar a { color: #cbd5e1; padding: 0.75rem 1.5rem; display: block; text-decoration: none; }
        .sidebar a:hover, .sidebar a.active { background: #1e293b; color: #fff; }
        .main { margin-left: 250px; padding: 2rem; }
        @media (max-width: 992px) { .sidebar { position: relative; width: 100%; height: auto; } .main { margin-left: 0; } }
    </style>
    @vite('resources/js/app.js')
</head>
<body class="antialiased">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">{{ config('app.name') }}</a>
        <div class="navbar-nav ms-auto">
            @if (auth()->check())
                <a class="nav-link" href="{{ route('admin.dashboard') }}">Admin</a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-danger btn-sm ml-2">Logout</button>
                </form>
            @else
                <a class="nav-link" href="{{ route('login') }}">Masuk</a>
                <a class="nav-link" href="{{ route('register') }}">Daftar</a>
            @endif
        </div>
    </div>
</nav>

<div class="sidebar">
    @if (auth()->check() && auth()->user()->can('manage courses'))
        <h6 class="p-3 text-muted text-uppercase">Menu Admin</h6>
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.categories.index') }}">Kategori</a>
        <a href="{{ route('admin.courses.index') }}">Kursus</a>
    @endif
</div>

<main class="main">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@stack('scripts')
</body>
</html>
