@extends('layouts.app')

@section('titre', __('app.factures.titre'))

@section('content')
    <div class="page-head">
        <h1>{{ __('app.factures.titre') }}</h1>
        <div class="actions"><a class="btn" href="{{ route('documents-commerciaux.create') }}">＋ {{ __('app.factures.nouveau') }}</a></div>
    </div>

    <div class="card">
        <form class="toolbar no-print" method="GET" action="{{ route('documents-commerciaux.index') }}">
            <div class="field">
                <label>{{ __('app.commun.type') }}</label>
                <select name="type" onchange="this.form.submit()">
                    <option value="">{{ __('app.conteneurs.tous') }}</option>
                    @foreach (\App\Enums\DocumentCommercialType::cases() as $type)
                        <option value="{{ $type->value }}" {{ request('type') === $type->value ? 'selected' : '' }}>{{ __("app.dc_type.{$type->value}") }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        <div class="table-wrap">
            <table>
                <thead><tr><th>{{ __('app.commun.numero') }}</th><th>{{ __('app.commun.type') }}</th><th>{{ __('app.commun.client') }}</th><th>{{ __('app.commun.statut') }}</th><th>{{ __('app.factures.montant_total') }}</th><th>{{ __('app.factures.emission') }}</th><th>{{ __('app.factures.echeance') }}</th></tr></thead>
                <tbody>
                @forelse ($documents as $document)
                    <tr>
                        <td><a class="mono" href="{{ route('documents-commerciaux.show', $document) }}">{{ $document->numero }}</a></td>
                        <td>{{ __("app.dc_type.{$document->type->value}") }}</td>
                        <td>{{ $document->client?->raison_sociale }}</td>
                        <td><span class="badge {{ $document->statut === \App\Enums\DocumentCommercialStatut::Paye ? 'success' : ($document->statut === \App\Enums\DocumentCommercialStatut::Annule ? 'danger' : 'primary') }}">{{ __("app.dc_statut.{$document->statut->value}") }}</span></td>
                        <td class="mono">{{ $document->montant_total }} {{ $document->devise->value }}</td>
                        <td class="mono">{{ $document->date_emission?->format('d/m/Y') ?? '—' }}</td>
                        <td class="mono">{{ $document->date_echeance?->format('d/m/Y') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $documents->links() }}
    </div>
@endsection
