    {{-- ═════ PRODUCTION ═════ --}}
    <div class="sech"><h2>Production &amp; priorities</h2><span class="ln"></span><a href="{{ route('mpfm_tests.trending_dashboard') }}">Analytics →</a></div>
    <div class="row r21">
        <div class="card"><header><div><h3>Production trend</h3><small>Latest MPFM tests · oil &amp; gas rate with water cut</small></div></header>
            @if(count($trend)) <div class="cv t"><canvas id="cTrend"></canvas></div> @else <div class="empty">No MPFM data yet.</div> @endif</div>
        <div class="card"><header><div><h3>Needs attention</h3><small>Across tests, requests, vessels @if($wl)and surveys @endif</small></div></header>
            @php
                $items = $attention->map(fn ($a) => ['n' => $a['n'], 'c' => $a['color'], 'tx' => $a['label'], 'url' => $a['url']]);
                if ($wl) {
                    if ($wl['overdueCount']) $items->push(['n' => $wl['overdueCount'], 'c' => '#f87171', 'tx' => 'active wells without a pressure survey in 180 days', 'url' => route('wireline.dashboard')]);
                    if ($wl['stats']['open']) $items->push(['n' => $wl['stats']['open'], 'c' => '#a78bfa', 'tx' => 'open Wire Line operations', 'url' => route('wireline.operations', ['status' => 'planned'])]);
                }
            @endphp
            @if($items->isEmpty())
                <div class="ok"><i class="fe fe-check-circle"></i> Nothing needs attention right now.</div>
            @else
                <ul class="list">@foreach($items as $a)<li><span class="n" style="--c:{{ $a['c'] }}">{{ $a['n'] }}</span><span class="tx">{{ $a['tx'] }}</span><a class="go" href="{{ $a['url'] }}">Open →</a></li>@endforeach</ul>
            @endif
        </div>
    </div>
