    {{-- ═════ TEAM & ACTIVITY ═════ --}}
    <div class="sech"><h2>Team &amp; activity</h2><span class="ln"></span></div>
    <div class="row r3">
        <div class="card"><header><div><h3>Recent activity</h3><small>Tests, requests and log uploads</small></div></header>
            @if($feed->isEmpty()) <div class="empty">No activity yet.</div>
            @else <ul class="list feed">@foreach($feed->take(7) as $e)<li style="--c:{{ $feedColors[$e['type']] ?? '#64748b' }}"><span class="dot"></span><span><b><a href="{{ $e['url'] }}">{{ $e['title'] }}</a></b><small>{{ $e['sub'] }}{{ $e['who'] ? ' · ' . $e['who'] : '' }} · {{ $e['when']->diffForHumans() }}</small></span></li>@endforeach</ul> @endif</div>
        <div class="card"><header><div><h3>Tests by shift</h3><small>MPFM and vessel tests</small></div></header><div class="cv s"><canvas id="cShift"></canvas></div></div>
        <div class="card"><header><div><h3>Most active operators</h3><small>Tests recorded</small></div></header>
            @if($operators->isEmpty()) <div class="empty">No data.</div>
            @else <ul class="list">@foreach($operators as $o)<li><span class="tx" style="color:#fff;font-weight:600">{{ $o['name'] }}<div class="bar" style="margin-top:6px"><i style="--w:{{ $o['total'] / $maxOp * 100 }}%;--bc:#3b82f6"></i></div></span><span class="n" style="--c:#fff;font-size:15px">{{ $o['total'] }}</span></li>@endforeach</ul> @endif</div>
    </div>
