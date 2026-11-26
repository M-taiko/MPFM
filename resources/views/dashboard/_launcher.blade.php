    {{-- ═════ LAUNCHER ═════ --}}
    @php
        $T = [
            'upload'   => ['#22d3ee', 'upload', 'Upload log', 'New MPFM test', 'upload.form'],
            'mpfm'     => ['#22d3ee', 'activity', 'MPFM tests', 'Review & approve', 'mpfm_tests.index'],
            'archive'  => ['#34d399', 'folder', 'Test archive', 'Uploaded logs', 'well_tests.index'],
            'analytics' => ['#3b82f6', 'trending-up', 'Analytics', 'Trends & alerts', 'mpfm_tests.trending_dashboard'],
            'units'    => ['#fbbf24', 'anchor', 'Testing units', 'Fleet & status', 'test_vessels.index'],
            'runs'     => ['#f59e0b', 'list', 'Test runs', 'Results', 'tv_tests.index'],
            'newrun'   => ['#f59e0b', 'plus-square', 'New test run', 'Record a result', 'tv_tests.create'],
            'requests' => ['#a78bfa', 'clipboard', 'Test requests', 'Plan & track', 'tv_test_requests.index'],
            'compare'  => ['#f87171', 'repeat', 'Compare vs MPFM', 'Meter agreement', 'tv_tests.compare'],
            'locations' => ['#34d399', 'map-pin', 'Locations', 'Onshore & offshore sites', 'locations.index'],
            'fields'   => ['#38bdf8', 'map', 'Field setup', 'Fields, towers, wells', 'oil_fields.index'],
        ];
        $sets = [
            'admin'      => ['upload', 'mpfm', 'archive', 'analytics', 'units', 'runs', 'requests', 'compare', 'locations', 'fields'],
            'manager'    => ['mpfm', 'requests', 'runs', 'compare', 'analytics', 'locations', 'fields', 'archive'],
            'supervisor' => ['mpfm', 'compare', 'runs', 'analytics', 'archive', 'requests'],
            'production' => ['analytics', 'mpfm', 'requests', 'runs', 'locations', 'archive'],
            'reservoir'  => ['analytics', 'mpfm', 'locations', 'fields', 'archive'],
            'operator'   => ['upload', 'newrun', 'requests', 'mpfm', 'archive'],
        ][$persona];
    @endphp
    <div class="sech"><h2>Quick access</h2><span class="ln"></span></div>
    <div class="tiles">
        @foreach($sets as $k)@php [$tc, $ti, $tt, $ts, $tr] = $T[$k]; @endphp
        <a class="tile" style="--tc:{{ $tc }}" href="{{ route($tr) }}"><i class="fe fe-{{ $ti }}"></i><span><b>{{ $tt }}</b><small>{{ $ts }}</small></span></a>
        @endforeach
        @if($wl && in_array($persona, ['admin', 'manager', 'reservoir'], true))
        <a class="tile" style="--tc:#a78bfa" href="{{ route('wireline.dashboard') }}"><i class="fe fe-git-branch"></i><span><b>Wire Line</b><small>Surveys & interventions</small></span></a>
        <a class="tile" style="--tc:#22d3ee" href="{{ route('wireline.operations') }}"><i class="fe fe-list"></i><span><b>Wire Line jobs</b><small>All operations</small></span></a>
        @endif
        @if($integrity)
        <a class="tile" style="--tc:#2dd4bf" href="{{ route('integrity.dashboard') }}"><i class="fe fe-shield"></i><span><b>Well Integrity</b><small>Barriers, annuli, anomalies</small></span></a>
        @endif
    </div>
