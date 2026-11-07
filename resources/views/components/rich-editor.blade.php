{{--
    x-rich-editor — WYSIWYG editor berbasis CKEditor 5 Classic (build gratis/GPL dari CDN).

    Props:
      $name     — nama input (mis: 'content[id]')
      $value    — nilai awal HTML
      $label    — label tampil di atas editor (opsional)
      $rows     — tinggi minimum area editor (default: 8)
      $required — required attribute (default: false)

    Catatan: CKEditor 5 build bawaan CDN membuang atribut/class HTML yang tidak
    dikenalnya. Untuk halaman hukum, gunakan heading H2/H3 agar daftar isi
    otomatis terbentuk.
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

<div class="rich-editor" style="--rich-editor-min-height: {{ $rows * 1.5 }}rem">
    @if ($label)
        <label class="form-label" for="{{ $editorId }}">{{ $label }}</label>
    @endif

    {{-- Textarea tetap ada sebagai fallback & sumber nilai form. --}}
    <textarea
        id="{{ $editorId }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        class="form-control @error($name) is-invalid @enderror"
        data-ckeditor
        @if ($required) required @endif
    >{{ $value }}</textarea>

    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

@once
@push('styles')
<style>
    .rich-editor .ck.ck-editor {
        width: 100%;
    }

    .rich-editor .ck-editor__editable_inline {
        min-height: var(--rich-editor-min-height, 12rem);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-ckeditor]').forEach(function (textarea) {
            ClassicEditor
                .create(textarea, {
                    language: 'id',
                    initialData: textarea.value,
                    heading: {
                        options: [
                            { model: 'paragraph', title: 'Paragraf', class: 'ck-heading_paragraph' },
                            { model: 'heading2', view: 'h2', title: 'Judul bagian (H2)', class: 'ck-heading_heading2' },
                            { model: 'heading3', view: 'h3', title: 'Subjudul (H3)', class: 'ck-heading_heading3' },
                        ],
                    },
                    toolbar: [
                        'heading', '|',
                        'bold', 'italic', 'underline', 'strikethrough', '|',
                        'bulletedList', 'numberedList', 'outdent', 'indent', '|',
                        'link', 'blockQuote', 'insertTable', '|',
                        'removeFormat', 'undo', 'redo',
                    ],
                })
                .then(function (editor) {
                    // Textarea disembunyikan CKEditor; pastikan nilai selalu sinkron.
                    var form = textarea.closest('form');

                    if (form) {
                        form.addEventListener('submit', function () {
                            editor.updateSourceElement();
                        });
                    }

                    editor.model.document.on('change:data', function () {
                        editor.updateSourceElement();
                    });
                })
                .catch(function (error) {
                    console.error('CKEditor gagal dimuat:', error);
                });
        });
    });
</script>
@endpush
@endonce

