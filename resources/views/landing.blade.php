<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ __('app.landing.accroche') }}">
    <title>{{ __('app.nom') }} — {{ __('app.landing.pied') }}</title>
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
<div class="landing">
    <nav class="landing-nav">
        <div class="brand">
            <span class="logo">TR</span>
            <span>{{ __('app.nom') }}</span>
        </div>
        <div class="actions">
            <a class="chip-btn" href="/locale/{{ app()->getLocale() === 'ar' ? 'fr' : 'ar' }}">{{ __('app.langue.basculer') }}</a>
            <button type="button" class="chip-btn" id="theme-btn" title="{{ __('app.theme.basculer') }}">🌓</button>
            <a class="btn small" href="{{ route('login') }}">{{ __('app.auth.connexion') }}</a>
        </div>
    </nav>

    <section class="hero">
        <span class="badge primary">{{ __('app.landing.pied') }}</span>
        <h1>{{ __('app.landing.accroche') }}</h1>
        <p class="lead">{{ __('app.landing.sous_titre') }}</p>
        <a class="btn" href="{{ route('login') }}">{{ __('app.auth.connexion') }} →</a>

        <div class="chaine-titre">{{ __('app.landing.chaine_titre') }}</div>
        <div class="chaine">
            @foreach (['client', 'dossier', 'marchandise', 'douane', 'frais', 'facturation', 'paiement', 'livraison', 'cloture'] as $etape)
                @if (! $loop->first)
                    <span class="sep">→</span>
                @endif
                <span class="etape">{{ __("app.landing.chaine.{$etape}") }}</span>
            @endforeach
        </div>
    </section>

    <section class="modules">
        <h2>{{ __('app.landing.modules_titre') }}</h2>
        <div class="modules-grid">
            @foreach ([
                ['clients', 'clients', 'nav.clients'],
                ['dossiers', 'dossiers', 'nav.dossiers'],
                ['douane', 'douane', 'dossiers.douane'],
                ['documents', 'documents', 'dossiers.documents'],
                ['frais', 'frais', 'dossiers.frais'],
                ['facturation', 'facturation', 'nav.factures'],
                ['paiements', 'paiements', 'nav.paiements'],
                ['transport', 'transport', 'nav.transport'],
                ['alertes', 'alertes', 'nav.alertes'],
                ['dashboard', 'dashboard', 'nav.dashboard'],
                ['rapports', 'rapports', 'nav.rapports'],
                ['traceabilite', 'traceabilite', 'nav.audit'],
            ] as [$cle, $icone, $titre])
                <div class="module-card">
                    <span class="icone"><x-icone :name="$icone" size="22" /></span>
                    <h3>{{ __("app.{$titre}") }}</h3>
                    <p>{{ __("app.landing.modules.{$cle}") }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <footer class="landing-footer">
        © {{ now()->year }} {{ __('app.nom') }} — {{ __('app.landing.pied') }}
    </footer>
</div>

<script>
    document.getElementById('theme-btn').addEventListener('click', function () {
        var sombre = document.documentElement.dataset.theme === 'dark';
        if (sombre) { delete document.documentElement.dataset.theme; } else { document.documentElement.dataset.theme = 'dark'; }
        localStorage.setItem('theme', sombre ? 'light' : 'dark');
    });
</script>
</body>
</html>
