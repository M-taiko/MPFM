@php $hmax = max(1, collect($heat)->max('n')); @endphp
<div class="hm" role="img" aria-label="Tests per day over the last 12 weeks">
    @foreach(collect($heat)->groupBy('w') as $week)
        <div>@foreach($week as $c)<i title="{{ $c['date'] }}: {{ $c['n'] }} test(s)" style="{{ $c['future'] ? 'opacity:.12' : 'opacity:' . ($c['n'] ? .28 + .72 * $c['n'] / $hmax : .1) }}"></i>@endforeach</div>
    @endforeach
</div>
<div style="display:flex;gap:14px;align-items:center;flex-wrap:wrap;margin-top:12px;font-size:11.5px;color:var(--sub);white-space:nowrap"><span><i style="display:inline-block;width:9px;height:9px;border-radius:2px;background:#22d3ee;opacity:.3;margin-right:6px"></i>few tests</span><span><i style="display:inline-block;width:9px;height:9px;border-radius:2px;background:#22d3ee;margin-right:6px"></i>many tests</span></div>
