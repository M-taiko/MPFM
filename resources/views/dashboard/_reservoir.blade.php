    {{-- ═════ RESERVOIR ═════ --}}
    <div class="sech"><h2>Reservoir pressure</h2><span class="ln"></span><a href="{{ route('wireline.dashboard') }}">Wire Line →</a></div>
    <div class="row r21">
        <div class="card"><header><div><h3>Average static BHP</h3><small>Field average of BHCIP / static surveys, by month</small></div></header>
            @if(count($reservoir['trend']['labels']) > 1) <div class="cv t"><canvas id="cBhp"></canvas></div> @else <div class="empty">Needs surveys in at least two months.</div> @endif</div>
        <div class="card"><header><div><h3>Depletion leaderboard</h3><small>First vs latest static survey</small></div></header>
            @if($reservoir['depl']->isEmpty()) <div class="empty">Needs two static surveys on a well.</div>
            @else @php $maxD = max(1, (float) $reservoir['depl']->max('pct')); @endphp
            <ul class="list">@foreach($reservoir['depl'] as $d)
                <li><span class="tx"><a href="{{ route('mpfm_tests.string_analysis', $d['id']) }}" style="color:#fff;font-weight:600">{{ $d['name'] }}</a>
                    <small style="color:var(--mute);display:block">{{ $nf($d['from']) }} → {{ $nf($d['to']) }} psi · {{ $d['months'] }} mo</small>
                    <div class="bar" style="margin-top:6px"><i style="--w:{{ max(3, $d['pct'] / $maxD * 100) }}%;--bc:{{ $d['pct'] > 5 ? '#f87171' : ($d['pct'] > 2 ? '#fbbf24' : '#34d399') }}"></i></div></span>
                    <span class="n" style="--c:{{ $d['pct'] > 5 ? '#f87171' : ($d['pct'] > 2 ? '#fbbf24' : '#34d399') }};font-size:15px">{{ $d['pct'] }}%</span></li>
            @endforeach</ul> @endif
        </div>
    </div>
