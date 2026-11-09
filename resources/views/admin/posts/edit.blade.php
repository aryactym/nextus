@extends('layouts.app')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Blog</div>
            <h2 class="page-title">{{ $post->title }}</h2>
        </div>
        <div class="col-auto ms-auto d-flex gap-2">
            @if ($post->status === 'published')
                <a href="{{ route('blog.show', $post->slug) }}" target="_blank" rel="noopener"
                   class="btn btn-outline-secondary">Lihat artikel</a>
            @endif
            <a href="{{ route('admin.posts.index') }}" class="btn btn-link">Kembali</a>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.posts.update', $post->uuid) }}" enctype="multipart/form-data" id="post-form">
    @csrf
    @method('PUT')
    @include('admin.posts._form')
</form>

<x-hugerte :selectors="['#content']" :height="500" />
@endsection
