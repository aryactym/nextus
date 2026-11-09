@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Blog</div>
            <h2 class="page-title">Tulis artikel baru</h2>
        </div>
        <div class="col-auto ms-auto d-flex gap-2">
            <a href="{{ route('admin.posts.index') }}" class="btn btn-link">Batal</a>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.posts.store') }}" enctype="multipart/form-data" id="post-form">
    @csrf
    @include('admin.posts._form')
</form>

<x-hugerte :selectors="['#content']" :height="500" />

@push('scripts')
<script>
// Auto-generate slug dari title
(function () {
    const titleInput = document.getElementById('title');
    const slugInput  = document.getElementById('slug');
    if (! titleInput || ! slugInput) { return; }
    let slugEdited = slugInput.value !== '';

    slugInput.addEventListener('input', function () { slugEdited = true; });
    titleInput.addEventListener('input', function () {
        if (slugEdited) { return; }
        slugInput.value = titleInput.value
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .trim()
            .replace(/\s+/g, '-');
    });
}());
</script>
@endpush

@endsection
