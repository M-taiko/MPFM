    {{-- ═════ WATCH LISTS ═════ --}}
    <div class="sech"><h2>Well watch list</h2><span class="ln"></span><a href="{{ route('mpfm_tests.trending_dashboard') }}">Trending analytics →</a></div>
    <div class="row r3">
        <div class="card"><header><div><h3>Oil decline</h3><small>Last 6 tests of each well</small></div><span class="tag">{{ $declineWatch->count() }}</span></header>
            @if($declineWatch->isEmpty()) <div class="ok"><i class="fe fe-check-circle"></i> No wells are declining noticeably.</div>
            @else <ul class="list">@foreach($declineWatch as $w)
                <li><span class="n" style="--c:{{ $w['oil_pct'] < -10 ? '#f87171' : '#fb923c' }};font-size:15px">{{ $w['oil_pct'] }}%</span>
                    <span class="tx"><a href="{{ route('mpfm_tests.string_analysis', $w['id']) }}" style="color:#fff;font-weight:600">{{ $w['name'] }}</a><br><small style="color:var(--mute)">{{ $nf($w['oil_from']) }} → {{ $nf($w['oil_to']) }} B/D in {{ (int) $w['days'] }} days</small></span></li>
            @endforeach</ul> @endif
        </div>
        <div class="card"><header><div><h3>Water cut rising</h3><small>Percentage points gained</small></div><span class="tag">{{ $wcWatch->count() }}</span></header>
            @if($wcWatch->isEmpty()) <div class="ok"><i class="fe fe-check-circle"></i> Water cut is stable.</div>
            @else <ul class="list">@foreach($wcWatch as $w)
                <li><span class="n" style="--c:#38bdf8;font-size:15px">+{{ $w['wc_delta'] }}</span>
                    <span class="tx"><a href="{{ route('mpfm_tests.string_analysis', $w['id']) }}" style="color:#fff;font-weight:600">{{ $w['name'] }}</a><br><small style="color:var(--mute)">{{ $nf($w['wc_from'], 1) }}% → {{ $nf($w['wc_to'], 1) }}% over {{ (int) $w['days'] }} days</small></span></li>
            @endforeach</ul> @endif
        </div>
        <div class="card"><header><div><h3>Wells due a test</h3><small>No test in the last 30 days</small></div><span class="tag">{{ $dueCount }}</span></header>
            @if($dueWells->isEmpty()) <div class="ok"><i class="fe fe-check-circle"></i> Every active well was tested recently.</div>
            @else <ul class="list">@foreach($dueWells->take(6) as $w)
                <li><span class="tx"><a href="{{ route('mpfm_tests.string_analysis', $w->id) }}" style="color:#fff;font-weight:600">{{ $w->name }}</a><br><small style="color:var(--mute)">{{ $w->tower->name ?? '' }}</small></span>
                    <span class="pill maintenance">{{ $w->last_test ? (int) \Carbon\Carbon::parse($w->last_test)->diffInDays(now()) . ' d' : 'never' }}</span></li>
            @endforeach</ul> @endif
        </div>
    </div>
