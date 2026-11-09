{{--
    Komponen HugeRTE WYSIWYG editor.

    Props:
      $selectors  — array ID textarea yang akan diaktifkan, mis. ['#description_id', '#description_en']
      $height     — tinggi editor dalam px (default 400)
--}}
@props([
    'selectors' => ['#content'],
    'height'    => 400,
])

@push('styles')
<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@tabler/core@1.7.3/dist/libs/hugerte/skins/ui/oxide/skin.min.css">
@endpush

@push('scripts')
<script>
(function () {
    var selectors = @json($selectors);
    var height    = {{ $height }};

    function initEditors() {
        selectors.forEach(function (sel) {
            hugerte.init({
                selector: sel,
                menubar: false,
                plugins: 'lists link image table code codesample',
                toolbar: 'undo redo | blocks | bold italic underline strikethrough'
                       + ' | link image | bullist numlist | table | code codesample',
                height: height,
                content_css: false,
                skin: false,
                content_style: [
                    'body { font-family: "Plus Jakarta Sans", system-ui, sans-serif;',
                    '       font-size: 15px; color: #0A2A28; line-height: 1.75; }',
                    'a { color: #0E6B5C; }',
                ].join(' '),
                setup: function (editor) {
                    editor.on('change', function () { editor.save(); });
                },
            });
        });
    }

    // Load HugeRTE dynamically then init
    var script  = document.createElement('script');
    script.src  = 'https://cdn.jsdelivr.net/npm/@tabler/core@1.7.3/dist/libs/hugerte/hugerte.min.js';
    script.onload = initEditors;
    document.head.appendChild(script);
}());
</script>
@endpush
