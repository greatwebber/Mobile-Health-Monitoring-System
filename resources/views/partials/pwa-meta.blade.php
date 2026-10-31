<meta name="theme-color" content="#075f5b">
<meta name="application-name" content="EdeHealth Sentinel">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="EdeHealth">
<meta name="description" content="A clinician-supervised health monitoring and disease-risk support platform for the Federal Polytechnic Ede community.">
<meta name="pwa-service-worker" content="{{ asset('service-worker.js') }}">
<meta name="pwa-scope" content="{{ rtrim(asset('/'), '/').'/' }}">

<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
<link rel="icon" type="image/svg+xml" href="{{ asset('icons/sentinel-mark.svg') }}">
<link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
<meta name="turbo-cache-control" content="no-cache">
<meta name="turbo-prefetch" content="false">
