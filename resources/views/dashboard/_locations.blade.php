    {{-- ═════ LOCATIONS ═════ --}}
    @php $ls = $locSummary; $locTypes = \App\Models\Location::TYPES; @endphp
    <div class="sech"><h2>Locations</h2><span class="ln"></span><a href="{{ route('locations.index') }}">Manage locations →</a></div>
    <div class="row r21">
        <div class="card"><header><div><h3>Location › Field › Tower › Well</h3><small>{{ $ls['total'] }} locations · {{ $ls['fields'] }} fields · {{ $ls['towers'] }} towers · {{ $ls['wells'] }} wells</small></div>
                @if(auth()->user()->canManageLocations())<a href="{{ route('locations.create') }}" style="margin-left:auto;font-size:12px;font-weight:700">+ Add location</a>@endif</header>
            @if($ls['total'] === 0)
                <div class="empty">No locations yet. @if(auth()->user()->canManageLocations())<a href="{{ route('locations.create') }}">Add the first one</a>@endif</div>
            @else
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(110px,1fr));gap:10px;margin-bottom:14px">
                @foreach($locTypes as $k => [$tl, $tc])
                <a href="{{ route('locations.index', ['environment' => $k]) }}" style="display:block;padding:12px 14px;border-radius:14px;border:1px solid {{ $tc }}55;background:{{ $tc }}12"><small style="color:{{ $tc }};font-weight:700;letter-spacing:.1em;text-transform:uppercase">{{ $tl }}</small><div style="font:700 26px 'Space Grotesk',sans-serif;color:#fff">{{ $ls['types'][$k] ?? 0 }}</div></a>
                @endforeach
            </div>
            <div class="scroll"><table><thead><tr><th>Location</th><th>Type</th><th>Fields</th><th>Towers</th><th>Wells</th><th>Status</th></tr></thead><tbody>
                @foreach($ls['list'] as $l)@php $st = $ls['stats'][$l->id] ?? ['fields' => 0, 'towers' => 0, 'wells' => 0]; @endphp<tr><td><a href="{{ route('locations.show', $l) }}">{{ $l->name }}</a></td>
                    <td><span class="pill" style="color:{{ $l->envColor() }};border-color:{{ $l->envColor() }}55">{{ $l->envLabel() }}</span></td>
                    <td>{{ $st['fields'] }}</td><td>{{ $st['towers'] }}</td><td>{{ $st['wells'] }}</td><td><span class="pill {{ $l->status }}">{{ $l->status }}</span></td></tr>@endforeach
            </tbody></table></div>
            @endif
        </div>
        <div class="card"><header><div><h3>Location types</h3><small>Onshore, offshore and more</small></div></header>
            @if($ls['total'] === 0) <div class="empty">No data.</div> @else <div class="cv s"><canvas id="cLocTypes"></canvas></div> @endif</div>
    </div>
    @if($ls['total'] > 0)
    <script>
    window.addEventListener('load', function () {
        var el = document.getElementById('cLocTypes'); if (!el || !window.Chart) return;
        var meta = @json(collect($locTypes)->map(fn ($t) => $t[0])), colors = @json(collect($locTypes)->map(fn ($t) => $t[1]));
        var raw = @json($ls['types']), keys = Object.keys(raw);
        new Chart(el, { type: 'doughnut', data: { labels: keys.map(function (k) { return meta[k] || k; }), datasets: [{ data: keys.map(function (k) { return raw[k]; }), backgroundColor: keys.map(function (k) { return colors[k] || '#64748b'; }), borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '62%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 }, color: '#9aaccb' } } } } });
    });
    </script>
    @endif
