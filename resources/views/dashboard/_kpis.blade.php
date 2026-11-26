@php
    $beyond = (int) optional($attention->firstWhere('label', 'MPFM vs vessel pairs beyond ±10%'))['n'];
    $drafts3 = (int) optional($attention->firstWhere('label', 'MPFM drafts older than 3 days'))['n'];
    $awaiting = ($mpfmByStatus['submitted'] ?? 0) + ($tvByStatus['submitted'] ?? 0);
    $intBad = $integrity ? $integrity['stats']['red'] + $integrity['stats']['amber'] : null;
    $mine = $mine ?? null;
    $monthDelta = isset($monthMpfm) ? $monthMpfm - $prevMonthMpfm : 0;
    $oilK  = ['Oil production', 'droplet', '#22d3ee', $nf($snap['oil'], 0), 'B/D', 'latest test of ' . $snap['strings'] . ' well(s)'];
    $gasK  = ['Gas production', 'wind', '#fbbf24', $nf($snap['gas'], 2), 'MMscf/D', 'field GOR ' . $nf($snap['gor'])];
    $wcK   = ['Water cut', 'percent', '#38bdf8', $nf($snap['wc'], 1), '%', $nf($snap['water'], 0) . ' B/D water'];
    $covK  = ['Well test coverage', 'target', '#34d399', $covPct, '%', $counts['testedStrings'] . ' of ' . $counts['strings'] . ' wells tested in 30 days'];
    $mpfmK = ['MPFM tests', 'bar-chart-2', '#3b82f6', $mpfmTotal, '', $mpfmWeek . ' this week <span class="' . ($wow >= 0 ? 'up' : 'dn') . '">' . ($wow >= 0 ? '▲' : '▼') . ' ' . abs($wow) . '</span> · ' . $approvedRate . '% approved'];
    $tvK   = ['Well testing runs', 'anchor', '#fbbf24', $tvTotal, '', $tvWeek . ' this week · ' . $openRequests . ' open request(s)'];
    $K = match ($persona) {
        'manager' => [
            ['Tests this month', 'calendar', '#3b82f6', $monthMpfm ?? 0, '', 'MPFM · <span class="' . ($monthDelta >= 0 ? 'up' : 'dn') . '">' . ($monthDelta >= 0 ? '▲' : '▼') . ' ' . abs($monthDelta) . '</span> vs last month'],
            ['Approval rate', 'check-circle', '#34d399', $approvedRate, '%', 'of all MPFM tests'],
            ['Review turnaround', 'clock', '#a78bfa', $turnaround ? $nf($turnaround, 0) : '—', 'h', 'average, last 90 days'],
            ['Waiting for review', 'inbox', '#fbbf24', $awaiting, '', 'MPFM and well testing'],
            ['Open test requests', 'clipboard', '#22d3ee', $openRequests, '', 'planned and in progress'],
            ['Wire Line open', 'git-branch', '#a78bfa', $wl['stats']['open'] ?? 0, '', ($wl['stats']['month'] ?? 0) . ' done this month'],
            ['Integrity risk', 'shield', $intBad ? '#f87171' : '#34d399', $intBad ?? '—', '', $integrity ? $integrity['stats']['red'] . ' red · ' . $integrity['stats']['amber'] . ' amber wells' : 'no access'],
            ['Wells due a test', 'alert-circle', $dueCount ? '#fb923c' : '#34d399', $dueCount, '', 'no test in the last 30 days'],
        ],
        'supervisor' => [
            ['Waiting for review', 'inbox', '#fbbf24', $awaiting, '', 'MPFM ' . ($mpfmByStatus['submitted'] ?? 0) . ' · well testing ' . ($tvByStatus['submitted'] ?? 0)],
            ['Validation warnings', 'alert-triangle', $validationAlerts->count() ? '#fb923c' : '#34d399', $validationAlerts->count(), '', 'tests flagged by the rules'],
            ['Rejected (30 d)', 'x-circle', '#f87171', $rejected30, '', 'sent back to the operator'],
            ['Review turnaround', 'clock', '#a78bfa', $turnaround ? $nf($turnaround, 0) : '—', 'h', 'average, last 90 days'],
            ['Meter agreement', 'shuffle', '#22d3ee', $avgOilDeviation !== null ? '±' . $avgOilDeviation : '—', '%', $pairCount . ' paired test(s)'],
            ['Beyond ±10%', 'alert-octagon', $beyond ? '#f87171' : '#34d399', $beyond, '', 'MPFM vs well testing pairs'],
            ['Old drafts', 'edit-3', $drafts3 ? '#fb923c' : '#34d399', $drafts3, '', 'MPFM drafts over 3 days'],
            ['Approval rate', 'check-circle', '#34d399', $approvedRate, '%', 'of all MPFM tests'],
        ],
        'production' => [
            $oilK, $gasK, $wcK, ['GOR', 'activity', '#a78bfa', $nf($snap['gor']), 'scf/stb', 'field average'],
            $covK,
            ['Declining wells', 'trending-down', $declineCount ? '#f87171' : '#34d399', $declineCount, '', 'oil down more than 10%'],
            ['Water cut rising', 'droplet', $wcRiseCount ? '#fb923c' : '#34d399', $wcRiseCount, '', 'up more than 3 pts'],
            ['Wells due a test', 'alert-circle', $dueCount ? '#fb923c' : '#34d399', $dueCount, '', 'no test in 30 days'],
        ],
        'reservoir' => [
            ['Pressure surveys', 'activity', '#a78bfa', $reservoir['runs'], '', $reservoir['static'] . ' static · ' . $reservoir['recent90'] . ' in 90 d'],
            ['Average BHP', 'gauge', '#22d3ee', $nf($reservoir['avgBhp']), 'psi', 'latest static survey per well'],
            ['Average BHT', 'thermometer', '#fb923c', $nf($reservoir['avgBht'], 1), '°C', 'bottom-hole temperature'],
            ['Largest depletion', 'trending-down', ($reservoir['maxDepl'] ?? 0) > 5 ? '#f87171' : '#34d399', $reservoir['maxDepl'] !== null ? $nf($reservoir['maxDepl'], 1) : '—', '%', 'first vs latest survey'],
            ['Depleting wells', 'alert-triangle', $reservoir['deplCount'] ? '#fbbf24' : '#34d399', $reservoir['deplCount'], '', 'more than 5% down'],
            ['Surveys overdue', 'clock', ($wl['overdueCount'] ?? 0) ? '#f87171' : '#34d399', $wl['overdueCount'] ?? 0, '', 'active wells, 180 days'],
            $oilK, $wcK,
        ],
        'operator' => [
            ['My tests this month', 'bar-chart-2', '#3b82f6', $mine['month'], '', 'MPFM tests recorded by you'],
            ['Drafts to submit', 'edit-3', ($mine['byStatus']['draft'] ?? 0) ? '#fbbf24' : '#34d399', $mine['byStatus']['draft'] ?? 0, '', 'finish and send for review'],
            ['To redo', 'x-circle', ($mine['byStatus']['rejected'] ?? 0) ? '#f87171' : '#34d399', $mine['byStatus']['rejected'] ?? 0, '', 'rejected by the supervisor'],
            ['In review', 'clock', '#a78bfa', $mine['byStatus']['submitted'] ?? 0, '', 'waiting for approval'],
            ['Approved', 'check-circle', '#34d399', $mine['byStatus']['approved'] ?? 0, '', 'counted in reports'],
            ['Well testing runs', 'anchor', '#fbbf24', $mine['tv'], '', 'recorded by you'],
            ['Wells due a test', 'alert-circle', $dueCount ? '#fb923c' : '#34d399', $dueCount, '', 'no test in 30 days'],
            ['Open requests', 'clipboard', '#22d3ee', $myRequests->count(), '', 'scheduled test requests'],
        ],
        default => array_values(array_filter([
            $oilK, $gasK, $wcK, $covK, $mpfmK, $tvK,
            $wl ? ['Wire Line operations', 'git-branch', '#a78bfa', $wl['stats']['total'], '', $wl['stats']['month'] . ' this month · ' . $wl['stats']['open'] . ' open'] : ['Approval rate', 'check-circle', '#34d399', $approvedRate, '%', 'of all MPFM tests'],
            $wl ? ['Pressure surveys', 'activity', $wl['overdueCount'] ? '#f87171' : '#34d399', $wl['stats']['runs'], '', $wl['overdueCount'] . ' well(s) overdue (180 d)'] : ['Meter agreement', 'shuffle', '#f87171', $avgOilDeviation !== null ? '±' . $avgOilDeviation : '—', '%', $pairCount . ' paired test(s)'],
            $integrity ? ['Integrity', 'shield', $intBad ? '#f87171' : '#34d399', $intBad, '', $integrity['stats']['red'] . ' red · ' . $integrity['stats']['amber'] . ' amber · ' . $integrity['stats']['green'] . ' green'] : null,
            $integrity ? ['Open anomalies', 'alert-octagon', $integrity['stats']['critical'] ? '#f87171' : '#fbbf24', $integrity['stats']['anomalies'], '', $integrity['stats']['critical'] . ' critical · ' . $integrity['stats']['overdue'] . ' barrier tests overdue'] : null,
            ['Locations', 'map-pin', '#34d399', $locSummary['total'], '', $locSummary['fields'] . ' fields · ' . $locSummary['towers'] . ' towers · ' . $locSummary['wells'] . ' wells'],
            ['Testing units', 'anchor', '#38bdf8', $vessels->where('status', 'active')->count(), '', $vessels->count() . ' registered'],
        ])),
    };
@endphp
    <div class="kpis">
        @foreach($K as $k)
        <div class="kpi" style="--c:{{ $k[2] }}"><div class="l">{{ $k[0] }} <i class="fe fe-{{ $k[1] }}"></i></div>
            <div class="v">{{ $k[3] }}@if($k[4])<small>{{ $k[4] }}</small>@endif</div><div class="d">{!! $k[5] !!}</div></div>
        @endforeach
    </div>
