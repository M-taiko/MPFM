    {{-- ═════ TEST VESSELS ═════ --}}
    <div class="sech"><h2>Well testing</h2><span class="ln"></span><a href="{{ route('test_vessels.index') }}">All testing units →</a></div>
    <div class="row r21">
        <div class="card"><header><div><h3>Testing unit utilisation</h3><small>Tests performed per unit</small></div></header>
            @if($vessels->isEmpty()) <div class="empty">No testing units registered.</div>
            @else <div class="scroll"><table><thead><tr><th>Unit</th><th>Status</th><th>Tests</th><th>Last test</th><th></th></tr></thead><tbody>
                @foreach($vessels as $v)<tr><td><a href="{{ route('test_vessels.show', $v) }}">{{ $v->name }}</a></td><td><span class="pill {{ $v->status }}">{{ $v->status }}</span></td><td>{{ $v->tests_count }}</td>
                    <td>{{ $v->last_test_at ? \Carbon\Carbon::parse($v->last_test_at)->format('d M Y') : '—' }}</td><td style="width:110px"><div class="bar"><i style="--w:{{ $v->tests_count / $maxVessel * 100 }}%;--bc:#fbbf24"></i></div></td></tr>@endforeach
            </tbody></table></div> @endif</div>
        <div class="card"><header><div><h3>Test requests</h3><small>{{ $openRequests }} open</small></div></header>
            @if(empty($requestsByStatus)) <div class="empty">No requests yet.</div> @else <div class="cv s"><canvas id="cReq"></canvas></div> @endif</div>
    </div>
