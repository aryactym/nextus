@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Organisasi / Peserta</div>
            <h2 class="page-title">Tambah peserta</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('admin.org.learners.index') }}" class="btn btn-link">Kembali</a>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.org.learners.store') }}">
    @csrf
    @include('org.learners._form')
</form>
@endsection
