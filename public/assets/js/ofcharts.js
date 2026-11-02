/* Tiny dependency-free SVG line chart used by the dark detail pages.
   OFCharts.line(el, { labels:[], series:[{ name, color, data:[], axis:'l'|'r', dec, unit }], dec, unit, height, area, smooth, xFmt })
   Null values leave a gap. Click a legend chip to toggle a series. */
(function () {
    var COLORS = ['#fbbf24', '#38bdf8', '#22d3ee', '#a78bfa', '#34d399', '#f87171'];

    function fmt(v, d) {
        if (v === null || v === undefined || isNaN(v)) return '—';
        var a = Math.abs(v);
        if (d === undefined) d = a >= 1000 ? 0 : a >= 100 ? 1 : 2;
        return Number(v).toLocaleString('en-US', { minimumFractionDigits: d, maximumFractionDigits: d });
    }
    function esc(s) { return String(s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); }

    function extent(series, axis, zero) {
        var lo = Infinity, hi = -Infinity;
        series.forEach(function (s) {
            if (s.off || (s.axis || 'l') !== axis) return;
            s.data.forEach(function (v) { if (v !== null && !isNaN(v)) { if (v < lo) lo = v; if (v > hi) hi = v; } });
        });
        if (lo === Infinity) return null;
        if (zero && lo > 0) lo = 0;
        if (hi === lo) { hi += Math.abs(hi) * .1 || 1; lo -= Math.abs(lo) * .1 || 0; }
        var pad = (hi - lo) * .12; hi += pad; if (!zero || lo < 0) lo -= pad;
        return [lo, hi];
    }

    function line(el, o) {
        var labels = o.labels || [], n = labels.length;
        var series = (o.series || []).map(function (s, i) {
            return { name: s.name, color: s.color || COLORS[i % COLORS.length], data: s.data || [], axis: s.axis || 'l', dec: s.dec, unit: s.unit || o.unit || '', off: false };
        });
        if (!n || !series.length) { el.innerHTML = '<div class="empty">No data to chart yet</div>'; return; }

        var W = 720, H = o.height || 260, pl = 56, pt = 14, pb = 34;
        var hasR = series.some(function (s) { return s.axis === 'r'; }), pr = hasR ? 56 : 18;
        var dense = n > 60, smooth = o.smooth !== undefined ? o.smooth : !dense;
        var uid = 'c' + Math.random().toString(36).slice(2, 8);

        function X(i) { return n === 1 ? (pl + W - pr) / 2 : pl + i * (W - pl - pr) / (n - 1); }
        function render() {
            var ext = { l: extent(series, 'l', o.zero !== false), r: extent(series, 'r', false) };
            function Y(v, ax) { var e = ext[ax] || ext.l || [0, 1]; return pt + (1 - (v - e[0]) / (e[1] - e[0])) * (H - pt - pb); }
            var s = '<svg viewBox="0 0 ' + W + ' ' + H + '"><defs>';
            series.forEach(function (x, i) { s += '<linearGradient id="' + uid + 'g' + i + '" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="' + x.color + '" stop-opacity=".3"/><stop offset="1" stop-color="' + x.color + '" stop-opacity="0"/></linearGradient>'; });
            s += '</defs>';
            var e = ext.l || ext.r;
            for (var g = 0; g <= 4 && e; g++) {
                var gy = pt + g * (H - pt - pb) / 4;
                s += '<line x1="' + pl + '" x2="' + (W - pr) + '" y1="' + gy + '" y2="' + gy + '" stroke="rgba(255,255,255,.07)"/>';
                if (ext.l) s += '<text x="' + (pl - 8) + '" y="' + (gy + 4) + '" text-anchor="end" font-size="10.5" fill="#64748b">' + fmt(ext.l[1] - (ext.l[1] - ext.l[0]) * g / 4, o.dec) + '</text>';
                if (ext.r) s += '<text x="' + (W - pr + 8) + '" y="' + (gy + 4) + '" font-size="10.5" fill="#64748b">' + fmt(ext.r[1] - (ext.r[1] - ext.r[0]) * g / 4, o.decR) + '</text>';
            }
            var every = Math.max(1, Math.ceil(n / 7)), xf = o.xFmt || function (l) { var t = String(l); return dense && t.indexOf(' ') > -1 ? t.split(' ').pop().slice(0, 5) : t.length > 11 ? t.slice(0, 11) : t; };
            for (var i = 0; i < n; i++) if (i % every === 0 || i === n - 1) s += '<text x="' + X(i) + '" y="' + (H - 10) + '" text-anchor="middle" font-size="10.5" fill="#64748b">' + esc(xf(labels[i])) + '</text>';

            series.forEach(function (x, si) {
                if (x.off) return;
                var d = '', seg = false, first = true, lastX = 0, lastY = 0, firstX = null, areaD = '';
                x.data.forEach(function (v, i) {
                    if (v === null || v === undefined || isNaN(v)) { seg = false; return; }
                    var px = X(i), py = Y(v, x.axis);
                    if (!seg) { d += (d ? ' ' : '') + 'M' + px + ',' + py; seg = true; }
                    else if (smooth) { var mx = (lastX + px) / 2; d += ' C' + mx + ',' + lastY + ' ' + mx + ',' + py + ' ' + px + ',' + py; }
                    else d += ' L' + px + ',' + py;
                    lastX = px; lastY = py;
                    if (firstX === null) firstX = px;
                });
                if (!d) return;
                if (o.area && si === 0 && x.data.every(function (v) { return v !== null; })) s += '<path d="' + d + ' L' + lastX + ',' + (H - pb) + ' L' + firstX + ',' + (H - pb) + ' Z" fill="url(#' + uid + 'g' + si + ')"/>';
                s += '<path class="ln" d="' + d + '" stroke="' + x.color + '" style="--len:3000"/>';
                if (!dense) x.data.forEach(function (v, i) { if (v !== null && !isNaN(v)) s += '<circle cx="' + X(i) + '" cy="' + Y(v, x.axis) + '" r="3.4" fill="#0b1426" stroke="' + x.color + '" stroke-width="2"/>'; });
            });
            s += '<line class="cr" y1="' + pt + '" y2="' + (H - pb) + '" stroke="rgba(255,255,255,.35)" stroke-dasharray="3 4" style="display:none"/></svg>';
            return { svg: s, Y: Y };
        }

        function draw() {
            var r = render();
            el.innerHTML = '<div class="lgn">' + series.map(function (x, i) { return '<span data-i="' + i + '" class="' + (x.off ? 'off' : '') + '"><b style="background:' + x.color + '"></b>' + esc(x.name) + '</span>'; }).join('') + '</div>' + r.svg + '<div class="tip"></div>';
            el.classList.add('ofc');
            [].forEach.call(el.querySelectorAll('.ln'), function (p) { try { p.style.setProperty('--len', Math.ceil(p.getTotalLength()) + 2); } catch (e) {} });
            if (el.dataset.shown) el.classList.add('in');
            [].forEach.call(el.querySelectorAll('.lgn span'), function (c) {
                c.addEventListener('click', function () {
                    var s = series[+c.dataset.i]; s.off = !s.off;
                    if (series.every(function (x) { return x.off; })) s.off = false;
                    draw();
                });
            });
            var svg = el.querySelector('svg'), tip = el.querySelector('.tip'), cr = el.querySelector('.cr');
            svg.addEventListener('mousemove', function (ev) {
                var b = svg.getBoundingClientRect(), x = (ev.clientX - b.left) / b.width * W, best = 0, bd = 1e9;
                for (var i = 0; i < n; i++) { var dd = Math.abs(X(i) - x); if (dd < bd) { bd = dd; best = i; } }
                cr.style.display = ''; cr.setAttribute('x1', X(best)); cr.setAttribute('x2', X(best));
                var h = '<small>' + esc(labels[best]) + '</small>';
                series.forEach(function (s) { if (!s.off) h += '<div><span style="width:8px;height:8px;border-radius:2px;background:' + s.color + '"></span>' + esc(s.name) + '<b style="color:' + s.color + '">' + fmt(s.data[best], s.dec !== undefined ? s.dec : o.dec) + (s.unit ? ' ' + esc(s.unit) : '') + '</b></div>'; });
                tip.innerHTML = h; tip.style.opacity = 1;
                tip.style.left = (X(best) / W * b.width) + 'px';
                var firstVis = series.filter(function (s) { return !s.off && s.data[best] !== null && s.data[best] !== undefined; })[0];
                tip.style.top = (firstVis ? r.Y(firstVis.data[best], firstVis.axis) / H * b.height : b.height / 2) + 'px';
            });
            svg.addEventListener('mouseleave', function () { tip.style.opacity = 0; cr.style.display = 'none'; });
        }
        draw();
    }

    /* start the draw-on animation when the chart scrolls into view */
    function watch(el) {
        if (!('IntersectionObserver' in window)) { el.dataset.shown = 1; el.classList.add('in'); return; }
        var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { el.dataset.shown = 1; el.classList.add('in'); io.disconnect(); } }); }, { threshold: .15 });
        io.observe(el);
    }

    window.OFCharts = { line: function (el, o) { line(el, o); watch(el); }, fmt: fmt, colors: COLORS };
})();
