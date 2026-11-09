@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Konten / Jadwal</div>
            <h2 class="page-title">Tambah jadwal</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('admin.events.index') }}" class="btn btn-link">Kembali</a>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.events.store') }}" id="event-form">
    @csrf
    @include('admin.events._form')
</form>
@endsection
