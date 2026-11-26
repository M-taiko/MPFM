    {{-- ═════ PRODUCTION BY LOCATION ═════ --}}
    <div class="sech"><h2>Production by location</h2><span class="ln"></span><a href="{{ route('locations.index') }}">Locations →</a></div>
    <div class="row r2">
        <div class="card"><header><div><h3>Oil by location</h3><small>B/D · latest test per well</small></div></header>
            @if($byLocation->isEmpty()) <div class="empty">No data.</div> @else <div class="cv s"><canvas id="cLocOil"></canvas></div> @endif</div>
        <div class="card"><header><div><h3>Locations ranked</h3><small>Share of total oil</small></div></header>
            @php $totOil = max(1, $byLocation->sum('oil')); @endphp
            <ul class="list">@foreach($byLocation as $l)
                <li><span class="tx"><b style="color:#fff;font-weight:600">{{ $l['name'] }}</b><small style="color:var(--mute);display:block">{{ $l['wells'] }} wells</small><div class="bar" style="margin-top:6px"><i style="--w:{{ $l['oil'] / $totOil * 100 }}%"></i></div></span>
                    <span class="n" style="--c:#22d3ee;font-size:14px">{{ $nf($l['oil']) }}<small style="font-size:10px;color:var(--mute)"> B/D</small></span></li>
            @endforeach</ul>
        </div>
    </div>
