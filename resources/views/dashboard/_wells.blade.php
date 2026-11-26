    {{-- ═════ WELLS & FIELDS ═════ --}}
    <div class="sech"><h2>Wells &amp; fields</h2><span class="ln"></span><a href="{{ route('oil_fields.index') }}">Field setup →</a></div>
    <div class="row r3">
        <div class="card"><header><div><h3>Oil by field</h3><small>B/D · latest test per well</small></div></header>
            @if($byField->isEmpty()) <div class="empty">No data.</div> @else <div class="cv s"><canvas id="cField"></canvas></div> @endif</div>
        <div class="card"><header><div><h3>Top producing wells</h3><small>Latest oil rate</small></div></header>
            @if($topStrings->isEmpty()) <div class="empty">No data.</div>
            @else <div class="scroll"><table><thead><tr><th>Well</th><th>Oil (B/D)</th><th>WC %</th><th></th></tr></thead><tbody>
                @foreach($topStrings as $s)<tr><td>{{ $s['name'] }}</td><td>{{ $nf($s['oil'], 1) }}</td><td>{{ $nf($s['wc'], 1) }}</td><td style="width:80px"><div class="bar"><i style="--w:{{ $s['oil'] / $maxTop * 100 }}%"></i></div></td></tr>@endforeach
            </tbody></table></div> @endif</div>
        <div class="card"><header><div><h3>Test coverage</h3><small>Wells tested in the last 30 days</small></div></header>
            <div class="cv s"><canvas id="cCover"></canvas></div></div>
    </div>
