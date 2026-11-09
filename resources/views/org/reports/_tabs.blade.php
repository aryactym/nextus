{{-- Tab navigation shared across report pages --}}
@php
    $tabs = [
        ['route' => 'admin.org.reports.index',     'label' => 'Ringkasan'],
        ['route' => 'admin.org.reports.by-topic',  'label' => 'Per Topik'],
        ['route' => 'admin.org.reports.by-course', 'label' => 'Per Kursus'],
        ['route' => 'admin.org.reports.by-learner','label' => 'Per Peserta'],
    ];
@endphp
<div class="mb-3">
    <ul class="nav nav-tabs">
        @foreach ($tabs as $tab)
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs($tab['route']) ? 'active' : '' }}"
                   href="{{ route($tab['route']) }}">
                    {{ $tab['label'] }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
