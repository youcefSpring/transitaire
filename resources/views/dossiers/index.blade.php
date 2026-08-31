@extends('layouts.app')

@section('titre', __('app.dossiers.titre'))

@section('content')
    <div class="page-head">
        <h1>{{ __('app.dossiers.titre') }}</h1>
        <div class="actions">
            @can('dossiers.gerer')
                <a class="btn" href="{{ route('dossiers.create') }}">＋ {{ __('app.dossiers.nouveau') }}</a>
            @endcan
        </div>
    </div>

    <div class="card">
        <x-filtres :action="route('dossiers.index')" :paginateur="$dossiers">
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
                    @foreach (\App\Enums\DossierStatut::cases() as $statut)
                        <option value="{{ $statut->value }}" {{ request('statut') === $statut->value ? 'selected' : '' }}>{{ __("app.statut.{$statut->value}") }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>{{ __('app.dossiers.type') }}</label>
                <select name="type">
                    <option value="">{{ __('app.conteneurs.tous') }}</option>
                    @foreach (\App\Enums\TypeOperation::cases() as $type)
                        <option value="{{ $type->value }}" {{ request('type') === $type->value ? 'selected' : '' }}>{{ __("app.type_operation.{$type->value}") }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>{{ __('app.dossiers.bloque') }}</label>
                <select name="bloque">
                    <option value="">{{ __('app.conteneurs.tous') }}</option>
                    <option value="1" {{ request('bloque') ? 'selected' : '' }}>{{ __('app.dossiers.bloque') }}</option>
                </select>
            </div>
        </x-filtres>

        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>{{ __('app.commun.numero') }}</th>
                    <th>{{ __('app.commun.client') }}</th>
                    <th>{{ __('app.dossiers.type') }}</th>
                    <th>{{ __('app.dossiers.mode_transport') }}</th>
                    <th>{{ __('app.dossiers.arrivee_prevue') }}</th>
                    <th>{{ __('app.commun.statut') }}</th>
                    <th class="amount">{{ __('app.commun.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($dossiers as $dossier)
                    @php
                        $classeLigne = $dossier->bloque
                            ? 'rstat-danger'
                            : match ($dossier->statut->value) {
                                'nouveau', 'documents_recus' => 'rstat-neutre',
                                'en_cours', 'livraison' => 'rstat-info',
                                'dedouanement' => 'rstat-alerte',
                                'douane_terminee', 'cloture' => 'rstat-succes',
                                default => '',
                            };
                    @endphp
                    <tr class="{{ $classeLigne }}">
                        <td><a class="mono" href="{{ route('dossiers.show', $dossier->numero) }}">{{ $dossier->numero }}</a></td>
                        <td>{{ $dossier->client?->raison_sociale }}</td>
                        <td>{{ __("app.type_operation.{$dossier->type->value}") }}</td>
                        <td>{{ __("app.mode_transport.{$dossier->mode_transport->value}") }}</td>
                        <td>{{ $dossier->date_arrivee_prevue->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge {{ $dossier->bloque ? 'danger' : 'primary' }}">
                                {{ $dossier->bloque ? __('app.dossiers.bloque') : __("app.statut.{$dossier->statut->value}") }}
                            </span>
                        </td>
                        <td>
                            <div class="row-actions">
                                <a class="btn secondary small" href="{{ route('dossiers.show', $dossier->numero) }}">{{ __('app.commun.detail') }}</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $dossiers->links() }}
    </div>
@endsection
