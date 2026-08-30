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
            if (localStorage.getItem('sidebar') === 'rail') {
                document.documentElement.dataset.rail = '1';
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
            <a href="{{ route('dashboard.index') }}" class="{{ request()->routeIs('dashboard.index') ? 'active' : '' }}" title="{{ __('app.nav.dashboard') }}"><span class="icon"><x-icone name="dashboard" /></span><span class="libelle">{{ __('app.nav.dashboard') }}</span></a>
            <a href="{{ route('clients.index') }}" class="{{ request()->routeIs('clients.*') ? 'active' : '' }}" title="{{ __('app.nav.clients') }}"><span class="icon"><x-icone name="clients" /></span><span class="libelle">{{ __('app.nav.clients') }}</span></a>
            <a href="{{ route('dossiers.index') }}" class="{{ request()->routeIs('dossiers.*') ? 'active' : '' }}" title="{{ __('app.nav.dossiers') }}"><span class="icon"><x-icone name="dossiers" /></span><span class="libelle">{{ __('app.nav.dossiers') }}</span></a>
            <a href="{{ route('conteneurs.index') }}" class="{{ request()->routeIs('conteneurs.*') ? 'active' : '' }}" title="{{ __('app.nav.conteneurs') }}"><span class="icon"><x-icone name="conteneurs" /></span><span class="libelle">{{ __('app.nav.conteneurs') }}</span></a>
            <a href="{{ route('documents-commerciaux.index') }}" class="{{ request()->routeIs('documents-commerciaux.*') ? 'active' : '' }}" title="{{ __('app.nav.factures') }}"><span class="icon"><x-icone name="factures" /></span><span class="libelle">{{ __('app.nav.factures') }}</span></a>
            <a href="{{ route('paiements.index') }}" class="{{ request()->routeIs('paiements.*') ? 'active' : '' }}" title="{{ __('app.nav.paiements') }}"><span class="icon"><x-icone name="paiements" /></span><span class="libelle">{{ __('app.nav.paiements') }}</span></a>
            <a href="{{ route('fournisseurs.index') }}" class="{{ request()->routeIs('fournisseurs.*') ? 'active' : '' }}" title="{{ __('app.nav.fournisseurs') }}"><span class="icon"><x-icone name="fournisseurs" /></span><span class="libelle">{{ __('app.nav.fournisseurs') }}</span></a>
            <a href="{{ route('camions.index') }}" class="{{ request()->routeIs('camions.*', 'chauffeurs.*', 'livraisons.*') ? 'active' : '' }}" title="{{ __('app.nav.transport') }}"><span class="icon"><x-icone name="transport" /></span><span class="libelle">{{ __('app.nav.transport') }}</span></a>
            <a href="{{ route('alertes.index') }}" class="{{ request()->routeIs('alertes.*') ? 'active' : '' }}" title="{{ __('app.nav.alertes') }}"><span class="icon"><x-icone name="alertes" /></span><span class="libelle">{{ __('app.nav.alertes') }}</span></a>
            <a href="{{ route('rapports.index') }}" class="{{ request()->routeIs('rapports.*') ? 'active' : '' }}" title="{{ __('app.nav.rapports') }}"><span class="icon"><x-icone name="rapports" /></span><span class="libelle">{{ __('app.nav.rapports') }}</span></a>
            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}" title="{{ __('app.nav.users') }}"><span class="icon"><x-icone name="users" /></span><span class="libelle">{{ __('app.nav.users') }}</span></a>
            <a href="{{ route('audit.index') }}" class="{{ request()->routeIs('audit.*') ? 'active' : '' }}" title="{{ __('app.nav.audit') }}"><span class="icon"><x-icone name="audit" /></span><span class="libelle">{{ __('app.nav.audit') }}</span></a>
        </nav>
        <div class="sidebar-foot">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn secondary small" style="width:100%;justify-content:center"><x-icone name="deconnexion" size="15" /> <span class="libelle">{{ __('app.nav.deconnexion') }}</span></button>
            </form>
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <button type="button" class="burger" id="burger" aria-label="{{ __('app.nav.basculer_menu') }}"
                title="{{ __('app.nav.basculer_menu') }}" aria-expanded="true" aria-controls="sidebar">☰</button>
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
        var bureau = window.matchMedia('(min-width: 1025px)').matches;

        if (bureau) {
            var replie = document.documentElement.dataset.rail === '1';
            if (replie) { delete document.documentElement.dataset.rail; } else { document.documentElement.dataset.rail = '1'; }
            localStorage.setItem('sidebar', replie ? 'complet' : 'rail');
            this.setAttribute('aria-expanded', replie ? 'true' : 'false');
            return;
        }

        var ouvert = document.getElementById('sidebar').classList.toggle('open');
        this.setAttribute('aria-expanded', ouvert ? 'true' : 'false');
    });

    document.getElementById('theme-btn').addEventListener('click', function () {
        var sombre = document.documentElement.dataset.theme === 'dark';
        if (sombre) { delete document.documentElement.dataset.theme; } else { document.documentElement.dataset.theme = 'dark'; }
        localStorage.setItem('theme', sombre ? 'light' : 'dark');
    });
</script>
</body>
</html>
