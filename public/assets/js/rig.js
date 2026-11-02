(function () {
    if (!document.getElementById('pjBeam')) return;
    // pumpjack: one clock drives beam, polished rod, crank and pitman arm so the linkage stays consistent
    var pjB = document.getElementById("pjBeam"), pjR = document.getElementById("pjRod"), pjC = document.getElementById("pjCrank"), pjP = document.getElementById("pjPit");
    function pjTick(ms) {
        var th = ms / 4000 * 6.2832, s = Math.sin(th), a = -9 * s, ar = a * Math.PI / 180;
        pjB.setAttribute("transform", "rotate(" + a + " 70 420)");
        pjC.setAttribute("transform", "rotate(" + (-th * 57.2958) + " 107 474)");
        var hx = 70 + (-56) * Math.cos(ar) - 18 * Math.sin(ar), hy = 420 + (-56) * Math.sin(ar) + 18 * Math.cos(ar);
        pjR.setAttribute("x1", hx); pjR.setAttribute("y1", hy);
        pjP.setAttribute("x1", 70 + 52 * Math.cos(ar)); pjP.setAttribute("y1", 420 + 52 * Math.sin(ar));
        pjP.setAttribute("x2", 107 + 8 * Math.cos(th)); pjP.setAttribute("y2", 474 - 8 * s);
    }
    pjTick(0);
    if (!matchMedia("(prefers-reduced-motion: reduce)").matches) (function loop(t) { pjTick(t); requestAnimationFrame(loop); })(0);
})();
