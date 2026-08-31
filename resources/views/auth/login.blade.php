<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.auth.titre') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=Noto+Kufi+Arabic:wght@400;600;700&family=Tajawal:wght@400;500;700&display=swap">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
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
<div class="login-page">
    <div class="login-card">
        <div class="login-brand">
            <span class="logo" style="width:38px;height:38px;border-radius:10px;background:linear-gradient(150deg,var(--primary),var(--accent));display:grid;place-items:center;color:#fff;font-weight:700">TR</span>
            <div>
                <strong style="font-size:1.05rem">{{ __('app.nom') }}</strong>
                <div class="muted" style="font-size:0.8rem">{{ __('app.auth.titre') }}</div>
            </div>
        </div>

        @if ($errors->any())
            <div class="errors">
                <ul style="margin:0;padding-inline-start:18px">
                    @foreach ($errors->all() as $erreur)
                        <li>{{ $erreur }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}" style="display:flex;flex-direction:column;gap:14px">
            @csrf
            <div class="field">
                <label for="email">{{ __('app.auth.email') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            </div>
            <div class="field">
                <label for="password">{{ __('app.auth.mot_de_passe') }}</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn" style="justify-content:center">{{ __('app.auth.connexion') }}</button>
        </form>

        @if (! app()->isProduction())
            @php
                $comptesDemo = [
                    ['profil' => \App\Enums\UserProfile::Administrateur, 'email' => 'admin@transitaire.dz'],
                    ['profil' => \App\Enums\UserProfile::Directeur, 'email' => 'directeur@transitaire.dz'],
                    ['profil' => \App\Enums\UserProfile::AgentTransit, 'email' => 'transit@transitaire.dz'],
                    ['profil' => \App\Enums\UserProfile::AgentCommercial, 'email' => 'commercial@transitaire.dz'],
                    ['profil' => \App\Enums\UserProfile::Comptable, 'email' => 'comptable@transitaire.dz'],
                    ['profil' => \App\Enums\UserProfile::ResponsableTransport, 'email' => 'transport@transitaire.dz'],
                    ['profil' => \App\Enums\UserProfile::Consultation, 'email' => 'consultation@transitaire.dz'],
                ];
            @endphp

            <div class="demo-box">
                <div class="demo-titre">{{ __('app.auth.demo_titre') }}</div>
                <p class="demo-aide">
                    {{ __('app.auth.demo_aide') }} <code>password</code>
                </p>
                <div class="demo-liste">
                    @foreach ($comptesDemo as $compte)
                        <button type="button" class="demo-compte" data-email="{{ $compte['email'] }}">
                            <span class="demo-profil">{{ __("app.profil.{$compte['profil']->value}") }}</span>
                            <span class="demo-email mono">{{ $compte['email'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <script>
                document.querySelectorAll('.demo-compte').forEach(function (bouton) {
                    bouton.addEventListener('click', function () {
                        document.getElementById('email').value = bouton.dataset.email;
                        document.getElementById('password').value = 'password';
                        document.querySelectorAll('.demo-compte').forEach(function (autre) {
                            autre.classList.toggle('actif', autre === bouton);
                        });
                    });
                });
            </script>
        @endif
    </div>
</div>
</body>
</html>
