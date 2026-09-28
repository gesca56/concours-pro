{{-- Application installable (PWA) : manifeste, icônes et service worker. --}}
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#1E3A8A">
<link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192.png">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="Concours-Pro">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js'));
    }
</script>
