{{--
    x-rich-editor — textarea untuk konten pelajaran.
    CKEditor 5 diinisialisasi secara global di layouts.app via selector [data-ckeditor].

    Props:
      $name     — nama input (mis: 'content[id]')
      $value    — nilai awal HTML
      $label    — label tampil di atas editor (opsional)
      $rows     — tinggi minimum area editor (default: 8)
      $required — required attribute (default: false)
--}}
@props([
    'name'     => 'content',
    'value'    => '',
    'label'    => null,
    'rows'     => 8,
    'required' => false,
])

@php
    $editorId = 'rich-editor-'.str()->uuid();
@endphp

<div class="rich-editor ck-editor__main" style="--rich-editor-min-height: {{ $rows * 1.5 }}rem">
    @if ($label)
        <label class="form-label" for="{{ $editorId }}">{{ $label }}</label>
    @endif

    {{-- Textarea tetap sebagai sumber nilai form. CKEditor diinisialisasi via [data-ckeditor] di layout. --}}
    <textarea
        id="{{ $editorId }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        class="form-control @error($name) is-invalid @enderror"
        data-ckeditor
        @if ($required) required @endif
    >{!! $value !!}</textarea>

    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
