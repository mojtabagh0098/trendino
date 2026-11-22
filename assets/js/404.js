(function () {
    'use strict';
    const link = document.querySelector('[data-404-countdown]');
    if (!link) return;
    let remaining = 3;
    const format = new Intl.NumberFormat('fa-IR');
    function render() {
        link.textContent = 'بازگشت تا ' + format.format(remaining) + ' ثانیه دیگه به صفحه اصلی';
    }
    render();
    let timer;
    function cancelRedirect() {
        window.clearInterval(timer);
        link.textContent = 'بازگشت به صفحه اصلی';
        document.removeEventListener('pointerdown', cancelRedirect);
        document.removeEventListener('focusin', cancelRedirect);
    }
    // Interaction means the visitor may be using the page rather than waiting.
    document.addEventListener('pointerdown', cancelRedirect, { once: true });
    document.addEventListener('focusin', cancelRedirect, { once: true });
    timer = window.setInterval(function () {
        // Do not redirect while the visitor is away from the page.
        if (document.hidden) return;
        remaining -= 1;
        if (remaining <= 0) {
            window.clearInterval(timer);
            window.location.replace(link.href);
            return;
        }
        render();
    }, 1000);
})();
