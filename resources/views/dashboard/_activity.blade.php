    <div class="row r3">
        <div class="card"><header><div><h3>Test activity</h3><small>Tests per day · last 30 days</small></div></header><div class="cv s"><canvas id="cActivity"></canvas></div></div>
        <div class="card"><header><div><h3>MPFM review pipeline</h3><small>{{ $mpfmTotal }} tests by status</small></div></header>
            @if($mpfmTotal) <div class="cv s"><canvas id="cStatus"></canvas></div> @else <div class="empty">No tests yet.</div> @endif</div>
        <div class="card"><header><div><h3>MPFM vs well testing</h3><small>Oil rate agreement{{ $avgOilDeviation !== null ? ' · mean ±' . $avgOilDeviation . '%' : '' }}</small></div></header>
            @if(count($scatter)) <div class="cv s"><canvas id="cScatter"></canvas></div> @else <div class="empty">No paired tests yet.</div> @endif</div>
    </div>
