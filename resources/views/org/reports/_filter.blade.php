{{--
    Filter bar shared across all report tabs.
    Props:
      $f          — filters array (date_from, date_to, course_id, org_id)
      $routeName  — named route for form action
      $courses    — optional collection for course dropdown
      $orgs       — collection of organisations (only non-empty for Super Admin / Admin)
      $orgId      — currently selected org ID
--}}
@php $canPickOrg = isset($orgs) && $orgs->isNotEmpty(); @endphp

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route($routeName) }}" class="row g-2 align-items-end" id="report-filter-form">
            <input type="hidden" name="date_from" id="filter_date_from" value="{{ $f['date_from'] }}">
            <input type="hidden" name="date_to"   id="filter_date_to"   value="{{ $f['date_to'] }}">

            {{-- Org picker — Super Admin / Admin only --}}
            @if ($canPickOrg)
                <div class="col-12 col-md-3">
                    <label class="form-label form-label-sm">Organisasi</label>
                    <select name="org_id" id="org-filter" class="form-select form-select-sm">
                        <option value="">— Pilih organisasi —</option>
                        @foreach ($orgs as $org)
                            <option value="{{ $org->id }}"
                                @selected((string) ($f['org_id'] ?? $orgId) === (string) $org->id)>
                                {{ $org->getTranslation('name', 'id') }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Date range --}}
            <div class="col-12 col-md-{{ $canPickOrg ? '3' : '4' }}">
                <label class="form-label form-label-sm">Periode pendaftaran</label>
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="icon" aria-hidden="true">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </span>
                    <input type="text" id="daterange-report" class="form-control form-control-sm"
                           placeholder="Semua periode"
                           value="{{ ($f['date_from'] && $f['date_to']) ? $f['date_from'].' — '.$f['date_to'] : '' }}"
                           autocomplete="off" readonly>
                </div>
            </div>

            {{-- Course picker --}}
            @if (isset($courses) && $courses->isNotEmpty())
                <div class="col-12 col-md-3">
                    <label class="form-label form-label-sm">Kursus</label>
                    <select name="course_id" id="course-filter" class="form-select form-select-sm">
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
                @if ($f['date_from'] || $f['date_to'] || $f['course_id'] || ($f['org_id'] ?? null))
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
        setup: function (p) {
            p.on('selected', function (s, e) {
                document.getElementById('filter_date_from').value = s.format('YYYY-MM-DD');
                document.getElementById('filter_date_to').value   = e.format('YYYY-MM-DD');
            });
            p.on('clear:selection', function () {
                document.getElementById('filter_date_from').value = '';
                document.getElementById('filter_date_to').value   = '';
            });
        },
    });

    @if ($f['date_from'] && $f['date_to'])
        picker.setDateRange('{{ $f['date_from'] }}', '{{ $f['date_to'] }}');
    @endif

    ['org-filter', 'course-filter'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) { new TomSelect(el, { allowEmptyOption: true, maxOptions: null }); }
    });

    // Saat organisasi berubah, langsung submit agar kursus dropdown ikut update
    var orgEl = document.getElementById('org-filter');
    if (orgEl) {
        orgEl.tomselect.on('change', function () {
            document.getElementById('report-filter-form').submit();
        });
    }
}());
</script>
@endpush
