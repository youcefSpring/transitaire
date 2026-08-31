@extends('layouts.app')

@section('titre', __('app.conteneurs.titre'))

@section('content')
    <div class="page-head">
        <h1>{{ __('app.conteneurs.titre') }}</h1>
        <div class="actions"><a class="btn secondary small" href="{{ route('dossiers.index') }}">{{ __('app.nav.dossiers') }}</a></div>
    </div>

    <div class="card">
        <x-filtres :action="route('conteneurs.index')" :paginateur="$conteneurs">
            <x-champ-recherche />
            <div class="field">
                <label>{{ __('app.commun.client') }}</label>
                <select name="client_id">
                    <option value="">{{ __('app.commun.tous') }}</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" {{ (string) request('client_id') === (string) $client->id ? 'selected' : '' }}>{{ $client->raison_sociale }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>{{ __('app.commun.statut') }}</label>
                <select name="statut">
                    <option value="">{{ __('app.conteneurs.tous') }}</option>
                    @foreach (\App\Enums\ConteneurStatut::cases() as $statut)
                        <option value="{{ $statut->value }}" {{ request('statut') === $statut->value ? 'selected' : '' }}>{{ __("app.conteneur_statut.{$statut->value}") }}</option>
                    @endforeach
                </select>
            </div>
        </x-filtres>

    <div class="table-wrap">
        <table>
            <thead><tr><th>{{ __('app.conteneurs.numero') }}</th><th>{{ __('app.conteneurs.bl') }}</th><th>{{ __('app.dossiers.navire') }}</th><th>{{ __('app.dossiers.eta') }}</th><th>{{ __('app.dossiers.ata') }}</th><th>{{ __('app.commun.dossier') }}</th><th>{{ __('app.commun.statut') }}</th><th>{{ __('app.dossiers.date_sortie') }}</th><th>{{ __('app.dossiers.date_retour') }}</th></tr></thead>
            <tbody>
            @forelse ($conteneurs as $conteneur)
                @php
                    $classeLigne = match ($conteneur->statut->value) {
                        'en_attente' => 'rstat-alerte',
                        'sorti' => 'rstat-info',
                        'livre' => 'rstat-succes',
                        'retourne' => 'rstat-neutre',
                        default => '',
                    };
                @endphp
                <tr class="{{ $classeLigne }}">
                        <td class="mono">{{ $conteneur->numero }}</td>
                        <td class="mono">{{ $conteneur->numero_bl }}</td>
                        <td>{{ $conteneur->navire }}</td>
                        <td class="mono">{{ $conteneur->date_eta->format('d/m/Y') }}</td>
                        <td class="mono">{{ $conteneur->date_ata?->format('d/m/Y') ?? '—' }}</td>
                        <td><a class="mono" href="{{ route('dossiers.show', $conteneur->dossier?->numero) }}">{{ $conteneur->dossier?->numero }}</a></td>
                        <td><span class="badge {{ $conteneur->statut === \App\Enums\ConteneurStatut::Retourne ? 'success' : 'warning' }}">{{ __("app.conteneur_statut.{$conteneur->statut->value}") }}</span></td>
                        <td class="mono">{{ $conteneur->date_sortie?->format('d/m/Y') ?? '—' }}</td>
                        <td class="mono">{{ $conteneur->date_retour?->format('d/m/Y') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $conteneurs->links() }}
    </div>

    @can('dossiers.gerer')
    <div class="card no-print">
        <h2>{{ __('app.conteneurs.nouveau') }}</h2>
        <p class="hint hint-bloc">{{ __('app.aide.conteneurs.intro') }}</p>
        <form method="POST" action="{{ route('conteneurs.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field"><label>{{ __('app.conteneurs.numero') }} * (ISO)</label><input name="numero" value="{{ old('numero') }}" placeholder="ABCU1234567" required class="mono"><x-aide cle="conteneurs.numero" /></div>
                <div class="field"><label>{{ __('app.commun.type') }} *</label><input name="type" value="{{ old('type') }}" placeholder="20', 40'…" required><x-aide cle="conteneurs.type" /></div>
                <div class="field"><label>{{ __('app.conteneurs.bl') }} *</label><input name="numero_bl" value="{{ old('numero_bl') }}" required class="mono"><x-aide cle="conteneurs.numero_bl" /></div>
                <div class="field"><label>{{ __('app.dossiers.navire') }}</label><input name="navire" value="{{ old('navire') }}"><x-aide cle="conteneurs.navire" /></div>
                <div class="field"><label>{{ __('app.dossiers.port_depart') }} *</label><input name="port_depart" value="{{ old('port_depart') }}" required><x-aide cle="conteneurs.port_depart" /></div>
                <div class="field"><label>{{ __('app.dossiers.port_arrivee') }} *</label><input name="port_arrivee" value="{{ old('port_arrivee') }}" required><x-aide cle="conteneurs.port_arrivee" /></div>
                <div class="field"><label>{{ __('app.dossiers.eta') }} *</label><input type="date" name="date_eta" value="{{ old('date_eta') }}" required class="mono"><x-aide cle="conteneurs.date_eta" /></div>
                <div class="field"><label>{{ __('app.dossiers.ata') }}</label><input type="date" name="date_ata" value="{{ old('date_ata') }}" class="mono"><x-aide cle="conteneurs.date_ata" /></div>
                <div class="field">
                    <label>{{ __('app.commun.client') }} *</label>
                    <select name="client_id" required>
                        <option value=""></option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->raison_sociale }}</option>
                        @endforeach
                    </select>
                    <x-aide cle="conteneurs.client" />
                </div>
                <div class="field">
                    <label>{{ __('app.commun.dossier') }} *</label>
                    <select name="dossier_id" required>
                        <option value=""></option>
                        @foreach ($dossiers as $dossier)
                            <option value="{{ $dossier->id }}">{{ $dossier->numero }}</option>
                        @endforeach
                    </select>
                    <x-aide cle="conteneurs.dossier" />
                </div>
            </div>
            <button type="submit" class="btn" style="margin-block-start:14px">{{ __('app.commun.enregistrer') }}</button>
        </form>
    </div>
    @endcan
@endsection
