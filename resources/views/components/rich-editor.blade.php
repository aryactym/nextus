{{--
    x-rich-editor — textarea rich text editor (Alpine.js + minimal toolbar)

    Props:
      $name     — nama input (mis: 'content[id]')
      $value    — nilai awal HTML
      $label    — label tampil di atas editor (opsional)
      $rows     — tinggi textarea fallback (default: 8)
      $required — required attribute (default: false)
--}}
@props([
    'name'     => 'content',
    'value'    => '',
    'label'    => null,
    'rows'     => 8,
    'required' => false,
])
<div x-data="richEditor({{ Js::from($value) }})" class="rich-editor">
    @if ($label)
        <label class="form-label">{{ $label }}</label>
    @endif

    {{-- Toolbar --}}
    <div class="rich-editor-toolbar btn-group btn-group-sm mb-1" role="toolbar" aria-label="Format teks">
        <button type="button" class="btn btn-outline-secondary" x-on:click="exec('bold')" title="Tebal (Ctrl+B)"><strong>B</strong></button>
        <button type="button" class="btn btn-outline-secondary" x-on:click="exec('italic')" title="Miring (Ctrl+I)"><em>I</em></button>
        <button type="button" class="btn btn-outline-secondary" x-on:click="exec('underline')" title="Garis bawah (Ctrl+U)"><u>U</u></button>
        <button type="button" class="btn btn-outline-secondary" x-on:click="exec('insertUnorderedList')" title="Daftar poin">&#8226;&#8212;</button>
        <button type="button" class="btn btn-outline-secondary" x-on:click="exec('insertOrderedList')" title="Daftar angka">1.</button>
        <button type="button" class="btn btn-outline-secondary" x-on:click="createLink()" title="Sisipkan tautan">&#128279;</button>
    </div>

    {{-- Editable area --}}
    <div
        x-ref="editor"
        contenteditable="true"
        x-html="content"
        x-on:input="content = $event.target.innerHTML; $refs.hidden.value = content"
        class="form-control rich-editor-area"
        style="min-height: {{ $rows * 1.5 }}rem; overflow-y: auto;"
        role="textbox"
        aria-multiline="true"
        @if($label) aria-label="{{ $label }}" @endif
    ></div>

    {{-- Hidden textarea untuk submit form --}}
    <textarea
        x-ref="hidden"
        name="{{ $name }}"
        class="d-none"
        @if ($required) required @endif
    >{{ $value }}</textarea>
</div>

@once
@push('scripts')
<script>
function richEditor(initial) {
    return {
        content: initial || '',
        exec(command) {
            document.execCommand(command, false, null);
            this.$refs.hidden.value = this.$refs.editor.innerHTML;
        },
        createLink() {
            const url = prompt('Masukkan URL tautan:');
            if (url) {
                document.execCommand('createLink', false, url);
                this.$refs.hidden.value = this.$refs.editor.innerHTML;
            }
        },
    };
}
</script>
@endpush
@endonce
