@extends('layouts.app')

@section('titre', __('app.transport.livraisons'))

@section('content')
    <div class="page-head">
        <h1>{{ __('app.transport.livraisons') }} — {{ __('app.transport.titre') }}</h1>
        <div class="actions">
            <a class="btn secondary small" href="{{ route('camions.index') }}">{{ __('app.transport.camions') }}</a>
            <a class="btn secondary small" href="{{ route('chauffeurs.index') }}">{{ __('app.transport.chauffeurs') }}</a>
        </div>
    </div>

    <div class="card no-print">
        <h2>{{ __('app.transport.nouvelle_livraison') }}</h2>
        <form method="POST" action="{{ route('livraisons.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field">
                    <label>{{ __('app.commun.dossier') }} *</label>
                    <select name="dossier_id" required>
                        <option value=""></option>
                        @foreach ($dossiers as $dossier)
                            <option value="{{ $dossier->id }}">{{ $dossier->numero }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>{{ __('app.transport.camions') }}</label>
                    <select name="camion_id">
                        <option value=""></option>
                        @foreach ($camions as $camion)
                            <option value="{{ $camion->id }}">{{ $camion->immatriculation }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>{{ __('app.fournisseur_type.transporteur') }}</label>
                    <select name="transporteur_externe_id">
                        <option value=""></option>
                        @foreach ($transporteurs as $transporteur)
                            <option value="{{ $transporteur->id }}">{{ $transporteur->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>{{ __('app.transport.chauffeurs') }}</label>
                    <select name="chauffeur_id">
                        <option value=""></option>
                        @foreach ($chauffeurs as $chauffeur)
                            <option value="{{ $chauffeur->id }}">{{ $chauffeur->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field"><label>{{ __('app.transport.lieu_chargement') }} *</label><input name="lieu_chargement" required></div>
                <div class="field"><label>{{ __('app.transport.entrepot') }}</label><input name="entrepot"></div>
                <div class="field"><label>{{ __('app.transport.destination') }} *</label><input name="destination" required></div>
                <div class="field"><label>{{ __('app.transport.chargement') }} *</label><input type="datetime-local" name="date_heure_chargement" required class="mono"></div>
                <div class="field"><label>{{ __('app.transport.frais_transport') }} *</label><input type="number" step="0.01" min="0" name="frais_transport" required class="mono"></div>
                <div class="field"><label>{{ __('app.transport.bon_livraison') }}</label><input name="bon_livraison" class="mono"></div>
            </div>
            <button type="submit" class="btn" style="margin-block-start:14px">{{ __('app.commun.enregistrer') }}</button>
        </form>
    </div>

    <div class="card">
        <x-filtres :action="route('livraisons.index')" :paginateur="$livraisons">
            <div class="field">
                <label>{{ __('app.commun.statut') }}</label>
                <select name="statut">
                    <option value="">{{ __('app.commun.tous') }}</option>
                    @foreach (\App\Enums\LivraisonStatut::cases() as $statut)
                        <option value="{{ $statut->value }}" {{ request('statut') === $statut->value ? 'selected' : '' }}>{{ __("app.livraison_statut.{$statut->value}") }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>{{ __('app.commun.date') }}</label>
                <input type="date" name="date" value="{{ request('date') }}">
            </div>
        </x-filtres>
        <div class="table-wrap">
            <table>
                <thead><tr><th>{{ __('app.commun.dossier') }}</th><th>{{ __('app.transport.chargement') }}</th><th>{{ __('app.transport.destination') }}</th><th>{{ __('app.transport.camion_ou_transporteur') }}</th><th>{{ __('app.transport.chauffeurs') }}</th><th>{{ __('app.commun.statut') }}</th><th>{{ __('app.transport.frais_transport') }}</th><th class="amount">{{ __('app.commun.actions') }}</th></tr></thead>
                <tbody>
                @forelse ($livraisons as $livraison)
                    <tr>
                        <td><a class="mono" href="{{ route('dossiers.show', $livraison->dossier?->numero) }}">{{ $livraison->dossier?->numero }}</a></td>
                        <td class="mono">{{ $livraison->date_heure_chargement->format('d/m/Y H:i') }}</td>
                        <td>{{ $livraison->destination }}</td>
                        <td>{{ $livraison->camion?->immatriculation ?? $livraison->transporteurExterne?->nom }}</td>
                        <td>{{ $livraison->chauffeur?->nom }}</td>
                        <td><span class="badge {{ $livraison->statut === \App\Enums\LivraisonStatut::Livree ? 'success' : 'warning' }}">{{ __("app.livraison_statut.{$livraison->statut->value}") }}</span></td>
                        <td class="mono">{{ $livraison->frais_transport }}</td>
                        <td>
                            <div class="row-actions no-print">
                                @unless ($livraison->statut === \App\Enums\LivraisonStatut::Livree)
                                    <form method="POST" action="{{ route('livraisons.statut', $livraison) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="statut" value="{{ $livraison->statut === \App\Enums\LivraisonStatut::Planifiee ? 'en_cours' : 'livree' }}">
                                        <button type="submit" class="btn secondary small">↪</button>
                                    </form>
                                @endunless
                                <form method="POST" action="{{ route('livraisons.destroy', $livraison) }}" onsubmit="return confirm('{{ __('app.commun.confirmer') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn danger small">✕</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $livraisons->links() }}
    </div>
@endsection
