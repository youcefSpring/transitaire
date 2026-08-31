@extends('layouts.app')

@section('titre', $document->numero)

@section('content')
    <div class="page-head">
        <h1><span class="mono">{{ $document->numero }}</span> <span class="badge primary">{{ __("app.dc_type.{$document->type->value}") }}</span></h1>
        <div class="actions no-print">
            @can('documents-commerciaux.gerer')
                <form method="POST" action="{{ route('documents-commerciaux.statut', $document) }}" style="display:inline-flex;gap:6px">
                    @csrf
                    @method('PATCH')
                    <select name="statut">
                        @foreach (\App\Enums\DocumentCommercialStatut::cases() as $statut)
                            <option value="{{ $statut->value }}" {{ $document->statut === $statut ? 'selected' : '' }}>{{ __("app.dc_statut.{$statut->value}") }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn small"><span class="fl">↪</span> {{ __('app.commun.statut') }}</button>
                </form>
            @endcan
            <a class="btn small" href="{{ route('documents-commerciaux.pdf', $document) }}">🖨 {{ __('app.commun.pdf') }}</a>
            <a class="btn secondary small" href="{{ route('documents-commerciaux.index') }}"><span class="fl">←</span> {{ __('app.commun.retour') }}</a>
        </div>
    </div>

    <div class="card">
        <dl class="dl">
            <div><dt>{{ __('app.commun.client') }}</dt><dd>{{ $document->client?->raison_sociale }}</dd></div>
            <div><dt>{{ __('app.commun.dossier') }}</dt><dd>@if ($document->dossier)<a class="mono" href="{{ route('dossiers.show', $document->dossier->numero) }}">{{ $document->dossier->numero }}</a>@else—@endif</dd></div>
            <div><dt>{{ __('app.commun.statut') }}</dt><dd><span class="badge primary">{{ __("app.dc_statut.{$document->statut->value}") }}</span></dd></div>
            <div><dt>{{ __('app.commun.devise') }}</dt><dd class="mono">{{ $document->devise->value }}</dd></div>
            <div><dt>{{ __('app.factures.emission') }}</dt><dd class="mono">{{ $document->date_emission?->format('d/m/Y') ?? '—' }}</dd></div>
            <div><dt>{{ __('app.factures.echeance') }}</dt><dd class="mono">{{ $document->date_echeance?->format('d/m/Y') ?? '—' }}</dd></div>
        </dl>
    </div>

    <div class="card">
        <h2>{{ __('app.factures.lignes') }}</h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th>{{ __('app.factures.prestation') }}</th><th>{{ __('app.commun.type') }}</th><th>{{ __('app.dossiers.quantite') }}</th><th>{{ __('app.factures.prix_unitaire') }}</th><th class="amount">{{ __('app.commun.montant') }}</th></tr></thead>
                <tbody>
                @forelse ($document->lignes as $ligne)
                    <tr>
                        <td>{{ $ligne->designation }}</td>
                        <td>{{ __("app.frais_categorie.{$ligne->categorie}") }}</td>
                        <td class="mono">{{ $ligne->quantite }}</td>
                        <td class="mono">{{ $ligne->prix_unitaire }}</td>
                        <td class="mono">{{ $ligne->montant }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <dl class="dl" style="margin-block-start:14px;justify-content:flex-end">
            <div><dt>{{ __('app.factures.total_prestations') }}</dt><dd class="mono">{{ $document->total_prestations }}</dd></div>
            <div><dt>{{ __('app.factures.total_frais') }}</dt><dd class="mono">{{ $document->total_frais }}</dd></div>
            <div><dt>{{ __('app.factures.taxes') }}</dt><dd class="mono">{{ $document->total_taxes }}</dd></div>
            <div><dt>{{ __('app.factures.remise') }}</dt><dd class="mono">− {{ $document->remise }}</dd></div>
            <div><dt>{{ __('app.factures.montant_total') }}</dt><dd class="mono" style="font-weight:700;font-size:1.05rem">{{ $document->montant_total }} {{ $document->devise->value }}</dd></div>
        </dl>
    </div>

    <div class="card">
        <h2>{{ __('app.nav.paiements') }}</h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th>{{ __('app.commun.date') }}</th><th>{{ __('app.paiements.mode') }}</th><th>{{ __('app.commun.montant') }}</th><th>{{ __('app.paiements.reference') }}</th></tr></thead>
                <tbody>
                @forelse ($document->paiements as $paiement)
                    <tr>
                        <td class="mono">{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                        <td>{{ __("app.paiement_mode.{$paiement->mode->value}") }}</td>
                        <td class="mono">{{ $paiement->montant }} {{ $paiement->devise->value }}</td>
                        <td class="mono">{{ $paiement->reference }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
