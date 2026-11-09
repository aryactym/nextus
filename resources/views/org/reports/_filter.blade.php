{{--
    Filter bar shared across all report tabs.
    Props: $f (filters array), $routeName, $courses (optional collection for course dropdown)
--}}
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route($routeName) }}" class="row g-2 align-items-end" id="report-filter-form">
            <input type="hidden" name="date_from" id="filter_date_from" value="{{ $f['date_from'] }}">
            <input type="hidden" name="date_to"   id="filter_date_to"   value="{{ $f['date_to'] }}">

            <div class="col-12 col-md-4">
                <label class="form-label form-label-sm">Periode pendaftaran</label>
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="icon" aria-hidden="true">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </span>
                    <input type="text" id="daterange-report" class="form-control" placeholder="Semua periode"
                           value="{{ ($f['date_from'] && $f['date_to']) ? $f['date_from'].' — '.$f['date_to'] : '' }}"
                           autocomplete="off" readonly>
                </div>
            </div>

            @if (isset($courses) && $courses->isNotEmpty())
                <div class="col-12 col-md-4">
                    <label class="form-label form-label-sm">Kursus</label>
                    <select name="course_id" id="course-filter" class="form-select">
                        <option value="">Semua kursus</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}"
                                @selected((string) ($f['course_id'] ?? '') === (string) $course->id)>
                                {{ $course->getTranslation('title', 'id') }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="col-12 col-md-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">Terapkan</button>
                @if ($f['date_from'] || $f['date_to'] || $f['course_id'])
                    <a href="{{ route($routeName) }}" class="btn btn-link btn-sm">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.7.3/dist/libs/tom-select/dist/css/tom-select.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.7.3/dist/libs/litepicker/dist/litepicker.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.7.3/dist/libs/tom-select/dist/js/tom-select.base.min.js"></script>
<script>
(function () {
    var picker = new Litepicker({
        element: document.getElementById('daterange-report'),
        singleMode: false,
        allowRepick: true,
        numberOfMonths: 2,
        numberOfColumns: 2,
        format: 'YYYY-MM-DD',
        setup: function (picker) {
            picker.on('selected', function (s, e) {
                document.getElementById('filter_date_from').value = s.format('YYYY-MM-DD');
                document.getElementById('filter_date_to').value   = e.format('YYYY-MM-DD');
            });
            picker.on('clear:selection', function () {
                document.getElementById('filter_date_from').value = '';
                document.getElementById('filter_date_to').value   = '';
            });
        },
    });

    @if ($f['date_from'] && $f['date_to'])
        picker.setDateRange('{{ $f['date_from'] }}', '{{ $f['date_to'] }}');
    @endif

    var courseEl = document.getElementById('course-filter');
    if (courseEl) {
        new TomSelect(courseEl, { allowEmptyOption: true, maxOptions: null });
    }
}());
</script>
@endpush
