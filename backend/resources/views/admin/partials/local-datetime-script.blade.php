<script>
(function () {
    var locale = document.documentElement.getAttribute('data-intl-locale') || 'en-AU';
    function opts(el) {
        return {
            dateStyle: 'short',
            timeStyle: el.getAttribute('data-time-style') === 'medium' ? 'medium' : 'short',
        };
    }
    document.querySelectorAll('time.local-datetime').forEach(function (el) {
        var raw = el.getAttribute('datetime');
        if (!raw) return;
        var d = new Date(raw);
        if (isNaN(d.getTime())) return;
        try {
            el.textContent = d.toLocaleString(locale, opts(el));
        } catch (e) {
            /* keep server-rendered fallback */
        }
    });
})();
</script>
