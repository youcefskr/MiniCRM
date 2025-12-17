<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ $title ?? config('app.name') }}</title>

<!-- Alpine.js - Déplacer en haut -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

<!-- Arabic font for RTL support -->
@if(App\Http\Middleware\SetLocale::isRtl())
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
@endif

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

<!-- RTL Styles -->
@if(App\Http\Middleware\SetLocale::isRtl())
<link rel="stylesheet" href="{{ asset('css/rtl.css') }}">
@endif

<!-- Ajouter après les autres styles -->
<style>
    [x-cloak] { display: none !important; }
</style>

