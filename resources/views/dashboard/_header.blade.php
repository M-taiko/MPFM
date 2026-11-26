@php
    $meta = [
        'admin'      => ['Operations command center', 'Everything across fields, tests, surveys and well integrity'],
        'manager'    => ['Operations management', 'Throughput, approvals, planned work and risks'],
        'supervisor' => ['Review &amp; surveillance desk', 'What is waiting for you, and what looks wrong'],
        'production' => ['Production performance', 'Rates, decline, water cut and test coverage'],
        'reservoir'  => ['Reservoir &amp; pressure surveillance', 'Memory-gauge surveys, depletion and well behaviour'],
        'operator'   => ['My field work', 'Your tests, what needs fixing and which wells are due'],
    ][$persona];
    $acts = [
        'admin'      => [['upload.form', 'upload', 'Upload MPFM log', 'p'], ['tv_test_requests.create', 'clipboard', 'Test request'], ['wireline.create', 'git-branch', 'Wire Line operation', null, 'wl'], ['tv_tests.compare', 'repeat', 'Compare vs MPFM']],
        'manager'    => [['tv_test_requests.create', 'clipboard', 'Test request', 'p'], ['wireline.create', 'git-branch', 'Wire Line operation', null, 'wl'], ['tv_tests.compare', 'repeat', 'Compare vs MPFM'], ['locations.index', 'map-pin', 'Locations']],
        'supervisor' => [['mpfm_tests.index', 'check-square', 'Review tests', 'p'], ['tv_tests.compare', 'repeat', 'Compare vs MPFM'], ['mpfm_tests.trending_dashboard', 'trending-up', 'Analytics']],
        'production' => [['mpfm_tests.trending_dashboard', 'trending-up', 'Analytics', 'p'], ['tv_test_requests.create', 'clipboard', 'Test request'], ['locations.index', 'map-pin', 'Locations']],
        'reservoir'  => [['wireline.dashboard', 'git-branch', 'Wire Line surveys', 'p', 'wl'], ['mpfm_tests.trending_dashboard', 'trending-up', 'Analytics'], ['locations.index', 'map-pin', 'Locations']],
        'operator'   => [['upload.form', 'upload', 'Upload MPFM log', 'p'], ['tv_tests.create', 'plus-square', 'New test run'], ['tv_test_requests.index', 'clipboard', 'Test requests']],
    ][$persona];
    $views = ['admin' => 'Admin', 'manager' => 'Manager', 'supervisor' => 'Supervisor', 'production' => 'Production', 'reservoir' => 'Reservoir', 'operator' => 'Operator'];
@endphp
    {{-- ═════ HEADER ═════ --}}
    <div class="top hero">
        <div class="who">
            <span class="lg" role="img" aria-label="Company logo"></span>
            <div>
                <div class="eyebrow"><i></i> {!! $meta[0] !!}</div>
                <h1>{{ $greet }}, {{ $first }}</h1>
                <div class="meta">
                    <span>{{ now()->format('l, d F Y') }}</span>
                    <span class="tag">{{ $canSwitchView && request('view') ? 'previewing: ' . $persona : str_replace('_', ' ', $user->roles->pluck('name')->first() ?? 'User') }}</span>
                    <span>{{ $counts['activeStrings'] }} active wells · {{ $counts['fields'] }} field(s) · {{ $counts['towers'] }} tower(s)</span>
                </div>
                <div class="meta" style="margin-top:6px;color:var(--mute)">{!! $meta[1] !!}</div>
                @if($canSwitchView)
                <div class="vsw"><span>View as</span>@foreach($views as $k => $l)<a href="{{ route('dashboard', $k === 'admin' ? [] : ['view' => $k]) }}" class="{{ $persona === $k ? 'on' : '' }}">{{ $l }}</a>@endforeach</div>
                @endif
            </div>
        </div>
        <div style="display:flex;align-items:flex-end;gap:14px;flex-wrap:wrap">
            <div class="acts">
                @foreach($acts as $a)
                    @if(($a[4] ?? null) !== 'wl' || $wl)
                    <a href="{{ route($a[0]) }}" class="btn {{ $a[3] ?? '' }}"><i class="fe fe-{{ $a[1] }}"></i> {{ $a[2] }}</a>
                    @endif
                @endforeach
            </div>
            @include('partials.rig')
        </div>
    </div>
