<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titre', __('app.nav.dashboard')) — {{ __('app.nom') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script>
        (function () {
            var t = localStorage.getItem('theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.dataset.theme = 'dark';
            }
        })();
    </script>
</head>
<body>
<div class="app">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <span class="logo">TR</span>
            <span>{{ __('app.nom') }}</span>
        </div>
        <nav>
            <a href="{{ route('dashboard.index') }}" class="{{ request()->routeIs('dashboard.index') ? 'active' : '' }}"><span class="icon"><x-icone name="dashboard" /></span>{{ __('app.nav.dashboard') }}</a>
            <a href="{{ route('clients.index') }}" class="{{ request()->routeIs('clients.*') ? 'active' : '' }}"><span class="icon"><x-icone name="clients" /></span>{{ __('app.nav.clients') }}</a>
            <a href="{{ route('dossiers.index') }}" class="{{ request()->routeIs('dossiers.*') ? 'active' : '' }}"><span class="icon"><x-icone name="dossiers" /></span>{{ __('app.nav.dossiers') }}</a>
            <a href="{{ route('conteneurs.index') }}" class="{{ request()->routeIs('conteneurs.*') ? 'active' : '' }}"><span class="icon"><x-icone name="conteneurs" /></span>{{ __('app.nav.conteneurs') }}</a>
            <a href="{{ route('documents-commerciaux.index') }}" class="{{ request()->routeIs('documents-commerciaux.*') ? 'active' : '' }}"><span class="icon"><x-icone name="factures" /></span>{{ __('app.nav.factures') }}</a>
            <a href="{{ route('paiements.index') }}" class="{{ request()->routeIs('paiements.*') ? 'active' : '' }}"><span class="icon"><x-icone name="paiements" /></span>{{ __('app.nav.paiements') }}</a>
            <a href="{{ route('fournisseurs.index') }}" class="{{ request()->routeIs('fournisseurs.*') ? 'active' : '' }}"><span class="icon"><x-icone name="fournisseurs" /></span>{{ __('app.nav.fournisseurs') }}</a>
            <a href="{{ route('camions.index') }}" class="{{ request()->routeIs('camions.*', 'chauffeurs.*', 'livraisons.*') ? 'active' : '' }}"><span class="icon"><x-icone name="transport" /></span>{{ __('app.nav.transport') }}</a>
            <a href="{{ route('alertes.index') }}" class="{{ request()->routeIs('alertes.*') ? 'active' : '' }}"><span class="icon"><x-icone name="alertes" /></span>{{ __('app.nav.alertes') }}</a>
            <a href="{{ route('rapports.index') }}" class="{{ request()->routeIs('rapports.*') ? 'active' : '' }}"><span class="icon"><x-icone name="rapports" /></span>{{ __('app.nav.rapports') }}</a>
            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}"><span class="icon"><x-icone name="users" /></span>{{ __('app.nav.users') }}</a>
            <a href="{{ route('audit.index') }}" class="{{ request()->routeIs('audit.*') ? 'active' : '' }}"><span class="icon"><x-icone name="audit" /></span>{{ __('app.nav.audit') }}</a>
        </nav>
        <div class="sidebar-foot">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn secondary small" style="width:100%;justify-content:center"><x-icone name="deconnexion" size="15" /> {{ __('app.nav.deconnexion') }}</button>
            </form>
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <button type="button" class="burger" id="burger" aria-label="Menu">☰</button>
            <div class="topbar-title">@yield('titre', __('app.nav.dashboard'))</div>
            <div class="topbar-actions">
                <a class="chip-btn" href="/locale/{{ app()->getLocale() === 'ar' ? 'fr' : 'ar' }}">{{ __('app.langue.basculer') }}</a>
                <button type="button" class="chip-btn" id="theme-btn" title="{{ __('app.theme.basculer') }}">🌓</button>
            </div>
        </header>

        <main class="content">
            @if (session('message'))
                <div class="flash">{{ session('message') }}</div>
            @endif

            @if ($errors->any())
                <div class="errors">
                    {{ __('validation.failed') !== 'validation.failed' ? __('validation.failed') : 'Erreurs de validation' }}
                    <ul>
                        @foreach ($errors->all() as $erreur)
                            <li>{{ $erreur }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script>
    document.getElementById('burger').addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('open');
    });

    document.getElementById('theme-btn').addEventListener('click', function () {
        var sombre = document.documentElement.dataset.theme === 'dark';
        if (sombre) { delete document.documentElement.dataset.theme; } else { document.documentElement.dataset.theme = 'dark'; }
        localStorage.setItem('theme', sombre ? 'light' : 'dark');
    });
</script>
</body>
</html>
