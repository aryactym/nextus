@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="h3">Dashboard Admin</h1>
        <p class="text-muted">Selamat datang di {{ config('app.name') }}.</p>
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ \App\Domain\Catalog\Models\Course::forOrganization()->count() }}</h5>
                        <p class="card-text text-muted">Kursus</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ \App\Domain\Catalog\Models\Category::forOrganization()->count() }}</h5>
                        <p class="card-text text-muted">Kategori</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
