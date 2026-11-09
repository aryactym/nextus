@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Konten / Jadwal</div>
            <h2 class="page-title">{{ $event->title }}</h2>
        </div>
        <div class="col-auto ms-auto d-flex gap-2">
            <a href="{{ route('jadwal') }}" target="_blank" rel="noopener"
               class="btn btn-outline-secondary">Lihat publik</a>
            <a href="{{ route('admin.events.index') }}" class="btn btn-link">Kembali</a>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.events.update', $event->uuid) }}" id="event-form">
    @csrf
    @method('PUT')
    @include('admin.events._form')
</form>
@endsection
