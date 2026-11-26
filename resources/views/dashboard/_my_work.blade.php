    {{-- ═════ MY WORK (operator) ═════ --}}
    <div class="sech"><h2>My work</h2><span class="ln"></span><a href="{{ route('mpfm_tests.index') }}">All MPFM tests →</a></div>
    <div class="row r21">
        <div class="card"><header><div><h3>Needs your action</h3><small>Drafts to submit and tests sent back to you</small></div><span class="tag">{{ $mine['todo']->count() }}</span></header>
            @if($mine['todo']->isEmpty())
                <div class="ok"><i class="fe fe-check-circle"></i> Nothing is waiting for you. Nice work.</div>
            @else
            <div class="scroll"><table><thead><tr><th>Well</th><th>Test date</th><th>Status</th><th>Note</th><th></th></tr></thead><tbody>
                @foreach($mine['todo'] as $t)
                <tr><td><b style="color:#fff">{{ $t->oilString->name ?? '—' }}</b></td><td>{{ optional($t->test_date)->format('d M Y') }}</td>
                    <td><span class="pill {{ $t->status }}">{{ $t->status }}</span></td>
                    <td style="color:var(--mute);font-size:12px">{{ $t->status === 'rejected' ? ($t->rejected_reason ?: 'See supervisor remarks') : 'Review the results, then submit' }}</td>
                    <td><a href="{{ route('mpfm_tests.show', $t->id) }}">{{ $t->status === 'rejected' ? 'Fix' : 'Open' }} →</a></td></tr>
                @endforeach
            </tbody></table></div>
            @endif
        </div>
        <div class="card"><header><div><h3>Wells due a test</h3><small>No MPFM test in the last 30 days</small></div><span class="tag">{{ $dueCount }}</span></header>
            @if($dueWells->isEmpty())
                <div class="ok"><i class="fe fe-check-circle"></i> Every active well was tested recently.</div>
            @else
            <ul class="list">@foreach($dueWells->take(6) as $w)
                <li><span class="tx"><a href="{{ route('mpfm_tests.string_analysis', $w->id) }}" style="color:#fff;font-weight:600">{{ $w->name }}</a><br><small style="color:var(--mute)">{{ $w->tower->name ?? '' }} · {{ $w->last_test ? (int) \Carbon\Carbon::parse($w->last_test)->diffInDays(now()) . ' days ago' : 'never tested' }}</small></span>
                    <a class="go" href="{{ route('upload.form') }}">Test →</a></li>
            @endforeach</ul>
            @endif
        </div>
    </div>
    <div class="row r3">
        <div class="card"><header><div><h3>My activity</h3><small>Tests per day · last 14 days</small></div></header><div class="cv s"><canvas id="cMyDaily"></canvas></div></div>
        <div class="card"><header><div><h3>My tests by status</h3><small>{{ array_sum($mine['byStatus']) }} MPFM tests</small></div></header>
            @if(array_sum($mine['byStatus'])) <div class="cv s"><canvas id="cMyStatus"></canvas></div> @else <div class="empty">No tests recorded by you yet.</div> @endif</div>
        <div class="card"><header><div><h3>Scheduled test requests</h3><small>Planned and in progress</small></div></header>
            @if($myRequests->isEmpty()) <div class="empty">No open requests.</div>
            @else <ul class="list">@foreach($myRequests as $r)
                <li><span class="n" style="--c:#22d3ee;font-size:14px">{{ $r->test_date->format('d M') }}</span><span class="tx">{{ $r->testVessel->name ?? 'Testing unit' }}<br><small style="color:var(--mute)">{{ $r->tower->name ?? '' }} · {{ $r->shift }}</small></span><span class="pill {{ $r->status }}">{{ str_replace('_', ' ', $r->status) }}</span></li>
            @endforeach</ul> @endif
        </div>
    </div>
    <div class="card" style="margin-bottom:16px"><header><div><h3>My recent tests</h3><small>Latest MPFM tests you recorded</small></div></header>
        @if($mine['recent']->isEmpty()) <div class="empty">Upload your first MPFM log to get started.</div>
        @else <div class="scroll"><table><thead><tr><th>Date</th><th>Well</th><th>Oil (B/D)</th><th>Water cut</th><th>Quality</th><th>Status</th><th></th></tr></thead><tbody>
            @foreach($mine['recent'] as $t)
            <tr><td>{{ optional($t->test_date)->format('d M Y') }}</td><td>{{ $t->oilString->name ?? '—' }}</td><td>{{ $nf($t->oil_rate, 1) }}</td><td>{{ $nf($t->water_cut, 1) }}%</td>
                <td>{{ $t->validation_score !== null ? $t->validation_score . ' / 100' : '—' }}</td><td><span class="pill {{ $t->status }}">{{ $t->status }}</span></td><td><a href="{{ route('mpfm_tests.show', $t->id) }}">Open</a></td></tr>
            @endforeach</tbody></table></div> @endif
    </div>
