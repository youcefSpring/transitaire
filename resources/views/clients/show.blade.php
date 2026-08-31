@extends('layouts.app')

@section('titre', $client->raison_sociale)

@section('content')
    <div class="page-head">
        <h1>{{ $client->raison_sociale }}</h1>
        <div class="actions">
            @can('clients.gerer')
                <a class="btn secondary small" href="{{ route('clients.edit', $client) }}">{{ __('app.commun.modifier') }}</a>
            @endcan
            <a class="btn secondary small" href="{{ route('clients.index') }}"><span class="fl">←</span> {{ __('app.commun.retour') }}</a>
        </div>
    </div>

    <div class="grid-kpi">
        <div class="kpi"><span class="label">{{ __('app.clients.total_facture') }} (DZD)</span><span class="value mono">{{ number_format($solde['dzd']['total_facture'], 2, ',', ' ') }}</span></div>
        <div class="kpi good"><span class="label">{{ __('app.clients.total_paye') }} (DZD)</span><span class="value mono">{{ number_format($solde['dzd']['total_paye'], 2, ',', ' ') }}</span></div>
        <div class="kpi {{ $solde['dzd']['reste_a_payer'] > 0 ? 'bad' : 'good' }}"><span class="label">{{ __('app.clients.reste_a_payer') }} (DZD)</span><span class="value mono">{{ number_format($solde['dzd']['reste_a_payer'], 2, ',', ' ') }}</span></div>
    </div>

    <div class="section-list">
        <div class="card">
            <h2>{{ __('app.clients.titre') }}</h2>
            <dl class="dl">
                <div><dt>{{ __('app.clients.nif') }}</dt><dd class="mono">{{ $client->nif }}</dd></div>
                <div><dt>{{ __('app.clients.nis') }}</dt><dd class="mono">{{ $client->nis }}</dd></div>
                <div><dt>{{ __('app.clients.rc') }}</dt><dd class="mono">{{ $client->rc }}</dd></div>
                <div><dt>{{ __('app.commun.telephone') }}</dt><dd class="mono">{{ $client->telephone }}</dd></div>
                <div><dt>{{ __('app.commun.email') }}</dt><dd>{{ $client->email }}</dd></div>
                <div><dt>{{ __('app.commun.adresse') }}</dt><dd>{{ $client->adresse }}</dd></div>
                <div><dt>{{ __('app.clients.conditions_paiement') }}</dt><dd>{{ $client->conditions_paiement }}</dd></div>
            </dl>
        </div>

        <div class="card">
            <h2>{{ __('app.clients.contacts') }}</h2>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>{{ __('app.commun.nom') }}</th><th>{{ __('app.clients.fonction') }}</th><th>{{ __('app.commun.telephone') }}</th><th>{{ __('app.commun.email') }}</th></tr></thead>
                    <tbody>
                    @forelse ($client->contacts as $contact)
                        <tr><td>{{ $contact->nom }}</td><td>{{ $contact->fonction }}</td><td class="mono">{{ $contact->telephone }}</td><td>{{ $contact->email }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h2>{{ __('app.clients.documents_administratifs') }}</h2>
            @can('clients.gerer')
                <form class="inline-form" method="POST" action="{{ route('clients.documents', $client) }}" enctype="multipart/form-data" style="margin-block-end:12px">
                    @csrf
                    <div class="field"><label>{{ __('app.commun.type') }}</label>
                        <select name="categorie">
                            @foreach (\App\Enums\DocumentCategorie::cases() as $categorie)
                                <option value="{{ $categorie->value }}">{{ __("app.document_categorie.{$categorie->value}") }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field"><label>{{ __('app.dossiers.televerser') }}</label><input type="file" name="fichier" required></div>
                    <button class="btn small" type="submit">⬆ {{ __('app.dossiers.televerser') }}</button>
                </form>
            @endcan
            <div class="table-wrap">
                <table>
                    <thead><tr><th>{{ __('app.commun.nom') }}</th><th>{{ __('app.commun.type') }}</th><th>{{ __('app.commun.date') }}</th><th class="amount">{{ __('app.commun.actions') }}</th></tr></thead>
                    <tbody>
                    @forelse ($client->documents as $document)
                        <tr>
                            <td>{{ $document->nom_original }}</td>
                            <td><span class="badge info">{{ __("app.document_categorie.{$document->categorie->value}") }}</span></td>
                            <td>{{ $document->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="row-actions">
                                    <a class="btn secondary small" href="{{ route('documents.download', $document) }}">⬇</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h2>{{ __('app.clients.dossiers_client') }}</h2>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>{{ __('app.commun.numero') }}</th><th>{{ __('app.dossiers.type') }}</th><th>{{ __('app.commun.statut') }}</th><th>{{ __('app.dossiers.arrivee_prevue') }}</th></tr></thead>
                    <tbody>
                    @forelse ($client->dossiers as $dossier)
                        <tr>
                            <td><a class="mono" href="{{ route('dossiers.show', $dossier->numero) }}">{{ $dossier->numero }}</a></td>
                            <td>{{ __("app.type_operation.{$dossier->type->value}") }}</td>
                            <td><span class="badge primary">{{ __("app.statut.{$dossier->statut->value}") }}</span></td>
                            <td>{{ $dossier->date_arrivee_prevue->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
