{{--
    x-upload-dropzone — area upload file dengan drag-and-drop (Alpine.js)

    Props:
      $name        — nama input file (default: 'file')
      $accept      — MIME/ekstensi yang diterima (default: '*')
      $label       — label teks (default: 'Pilih atau seret file ke sini')
      $hint        — teks bantuan di bawah label (opsional)
      $maxMb       — ukuran maksimal tampil di hint (opsional)
      $multiple    — allow multiple files (default: false)
      $required    — required attribute (default: false)
--}}
@props([
    'name'     => 'file',
    'accept'   => '*',
    'label'    => 'Pilih atau seret file ke sini',
    'hint'     => null,
    'maxMb'    => null,
    'multiple' => false,
    'required' => false,
])
<div
    x-data="{
        dragOver: false,
        files: [],
        handleFiles(fileList) {
            this.files = Array.from(fileList).map(f => f.name);
        }
    }"
    x-on:dragover.prevent="dragOver = true"
    x-on:dragleave.prevent="dragOver = false"
    x-on:drop.prevent="dragOver = false; handleFiles($event.dataTransfer.files); $refs.input.files = $event.dataTransfer.files"
    :class="dragOver ? 'border-primary bg-primary-lt' : 'border-secondary'"
    class="border-2 border-dashed rounded p-4 text-center cursor-pointer transition"
    x-on:click="$refs.input.click()"
>
    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" class="mb-2 text-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/><polyline points="7 9 12 4 17 9"/><line x1="12" y1="4" x2="12" y2="16"/></svg>

    <p class="mb-1 small">{{ $label }}</p>

    @if ($hint || $maxMb)
        <p class="text-muted" style="font-size:0.8rem">
            {{ $hint }}
            @if ($maxMb)
                &mdash; Maks {{ $maxMb }} MB
            @endif
        </p>
    @endif

    <template x-if="files.length > 0">
        <ul class="list-unstyled small mt-2 mb-0">
            <template x-for="f in files" :key="f">
                <li class="text-primary" x-text="f"></li>
            </template>
        </ul>
    </template>

    <input
        x-ref="input"
        type="file"
        name="{{ $name }}"
        accept="{{ $accept }}"
        @if ($multiple) multiple @endif
        @if ($required) required @endif
        class="d-none"
        x-on:change="handleFiles($event.target.files)"
        {{ $attributes }}
    >
</div>

