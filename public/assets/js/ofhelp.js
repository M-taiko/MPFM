/* Floating explanations for every ".hq" (?) marker. Works for elements added later too. */
(function () {
    var tip = document.createElement('div'); tip.className = 'ofhq-tip'; tip.setAttribute('role', 'tooltip'); document.body.appendChild(tip);
    var cur = null, pinned = false;
    function esc(s) { return String(s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); }
    function show(el) {
        cur = el;
        tip.innerHTML = '<b>' + esc(el.dataset.h || '') + '</b>' + esc(el.dataset.t || '') + (el.dataset.u ? '<br><em>' + esc(el.dataset.u) + '</em>' : '');
        tip.classList.add('on'); el.classList.add('on');
        var r = el.getBoundingClientRect(), w = tip.offsetWidth, h = tip.offsetHeight;
        var x = Math.max(10, Math.min(window.innerWidth - w - 10, r.left + r.width / 2 - w / 2));
        var y = r.top - h - 10; if (y < 10) y = r.bottom + 10;
        tip.style.left = x + 'px'; tip.style.top = y + 'px';
    }
    function hide() { tip.classList.remove('on'); if (cur) cur.classList.remove('on'); cur = null; pinned = false; }
    function target(e) { return e.target.closest ? e.target.closest('.hq') : null; }
    document.addEventListener('mouseover', function (e) { var el = target(e); if (el && !pinned) show(el); });
    document.addEventListener('mouseout', function (e) { var el = target(e); if (el && !pinned) hide(); });
    document.addEventListener('focusin', function (e) { var el = target(e); if (el) show(el); });
    document.addEventListener('focusout', function (e) { if (target(e) && !pinned) hide(); });
    document.addEventListener('click', function (e) {
        var el = target(e);
        if (el) { e.preventDefault(); e.stopPropagation(); if (pinned && cur === el) hide(); else { show(el); pinned = true; } }
        else if (pinned) hide();
    });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') hide(); else if ((e.key === 'Enter' || e.key === ' ') && target(e)) { e.preventDefault(); var el = target(e); pinned = true; show(el); } });
    window.addEventListener('scroll', function () { if (cur) hide(); }, true);
})();
