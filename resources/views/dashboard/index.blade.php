@extends('layouts.app')

@section('titre', __('app.dashboard.titre'))

@section('content')
    <div class="grid-kpi">
        @foreach ([
            'dossiers_en_cours' => '',
            'conteneurs_en_attente' => '',
            'expeditions_aeriennes' => '',
            'livraisons_du_jour' => '',
            'chiffre_affaires' => 'mono',
            'paiements_recus' => 'good mono',
            'impayes' => 'bad mono',
            'benefice_marge' => 'good mono',
            'dossiers_bloques' => 'warn',
            'documents_manquants' => 'warn',
        ] as $cle => $classes)
            <div class="kpi {{ $classes }}">
                <span class="label">{{ __("app.dashboard.{$cle}") }}</span>
                <span class="value">{{ is_numeric($indicateurs[$cle]) ? $indicateurs[$cle] : number_format($indicateurs[$cle], 2, ',', ' ') }}</span>
            </div>
        @endforeach
    </div>

    <div class="card">
        <h2>{{ __('app.nav.dossiers') }} <a class="btn secondary small no-print" href="{{ route('dossiers.index') }}">{{ __('app.dossiers.nouveau') }}</a></h2>
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>{{ __('app.commun.numero') }}</th>
                    <th>{{ __('app.commun.client') }}</th>
                    <th>{{ __('app.dossiers.type') }}</th>
                    <th>{{ __('app.dossiers.mode_transport') }}</th>
                    <th>{{ __('app.commun.statut') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($indicateurs['derniers_dossiers'] ?? [] as $dossier)
                    <tr>
                        <td><a href="{{ route('dossiers.show', $dossier->numero) }}" class="mono">{{ $dossier->numero }}</a></td>
                        <td>{{ $dossier->client?->raison_sociale }}</td>
                        <td>{{ __("app.type_operation.{$dossier->type->value}") }}</td>
                        <td>{{ __("app.mode_transport.{$dossier->mode_transport->value}") }}</td>
                        <td><span class="badge primary">{{ __("app.statut.{$dossier->statut->value}") }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
