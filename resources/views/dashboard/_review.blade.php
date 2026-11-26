    {{-- ═════ REVIEW DESK ═════ --}}
    <div class="sech"><h2>Review desk</h2><span class="ln"></span><a href="{{ route('mpfm_tests.index') }}">All tests →</a></div>
    <div class="row r21">
        <div class="card"><header><div><h3>Waiting for your review</h3><small>Oldest first · MPFM and well testing</small></div><span class="tag">{{ $reviewQueue->count() }}</span></header>
            @if($reviewQueue->isEmpty())
                <div class="ok"><i class="fe fe-check-circle"></i> The review queue is empty.</div>
            @else
            <div class="scroll"><table><thead><tr><th>Type</th><th>Well</th><th>Test date</th><th>Operator</th><th>Oil (B/D)</th><th>Quality</th><th></th></tr></thead><tbody>
                @foreach($reviewQueue as $q)
                <tr><td><span class="pill" style="background:{{ $q['color'] }}22;color:{{ $q['color'] }}">{{ $q['type'] }}</span></td>
                    <td><b style="color:#fff">{{ $q['well'] }}</b></td><td>{{ optional($q['date'])->format('d M') }} <small style="color:var(--mute)">({{ optional($q['date'])->diffForHumans(null, true) }})</small></td><td>{{ $q['who'] }}</td>
                    <td>{{ $nf($q['oil'], 1) }}</td>
                    <td>@if($q['score'] !== null)<span class="pill {{ $q['vs'] === 'pass' ? 'approved' : ($q['vs'] === 'fail' ? 'rejected' : 'maintenance') }}">{{ $q['score'] }}</span>@else — @endif</td>
                    <td><a href="{{ $q['url'] }}">Review →</a></td></tr>
                @endforeach
            </tbody></table></div>
            @endif
        </div>
        <div class="card"><header><div><h3>Validation alerts</h3><small>Tests the rules flagged</small></div></header>
            @if($validationAlerts->isEmpty())
                <div class="ok"><i class="fe fe-check-circle"></i> No validation warnings.</div>
            @else
            <ul class="list">@foreach($validationAlerts as $t)
                <li><span class="n" style="--c:{{ $t->validation_status === 'fail' ? '#f87171' : '#fb923c' }}">{{ $t->validation_score }}</span>
                    <span class="tx"><a href="{{ route('mpfm_tests.show', $t->id) }}" style="color:#fff;font-weight:600">{{ $t->oilString->name ?? '—' }}</a> · {{ optional($t->test_date)->format('d M') }}<br><small style="color:var(--mute)">{{ \Illuminate\Support\Str::limit($t->validation_summary, 70) }}</small></span>
                    <span class="pill {{ $t->status }}">{{ $t->status }}</span></li>
            @endforeach</ul>
            @endif
        </div>
    </div>
