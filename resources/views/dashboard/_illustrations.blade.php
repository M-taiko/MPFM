    {{-- ═════ FIELD & PROCESS ILLUSTRATIONS ═════ --}}
    <div class="sech"><h2>Field &amp; process overview</h2><span class="ln"></span></div>
    <div class="row r21">
        <div class="card pano ill" style="position:relative">
            <div class="ct"><span>Field overview · live cross-section</span></div>
            @include('partials.dash_platform')
            @include('partials.dash_strata')
            <span class="cap">Figures = latest test of every oil string</span>
        </div>
        <div class="card tankc ill">
            <div class="ct"><span>Fluid split</span><a href="{{ route('mpfm_tests.index') }}">Tests →</a></div>
            @include('partials.dash_tank', ['fill' => 78])
            <div class="sx">
                <div style="--c:#fbbf24"><small>Oil</small><b>{{ number_format($snap['oil'], 0) }}<i>B/D</i></b></div>
                <div style="--c:#fb923c"><small>Gas</small><b>{{ number_format($snap['gas'], 1) }}<i>MMscf/d</i></b></div>
                <div style="--c:#38bdf8"><small>Water</small><b>{{ number_format($snap['water'], 0) }}<i>B/D</i></b></div>
                <div style="--c:#a78bfa"><small>GOR</small><b>{{ $snap['gor'] !== null ? number_format($snap['gor']) : '—' }}<i>scf/stb</i></b></div>
            </div>
        </div>
    </div>
    <div class="card ill" style="margin-bottom:16px">
        <div class="ct"><span>Process flow · wells → MPFM → test separator → storage</span><a href="{{ route('tv_tests.compare') }}">Compare tests →</a></div>
        @include('partials.dash_flow')
    </div>
