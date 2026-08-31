@extends('layouts.app')

@section('titre', __('app.alertes.titre'))

@section('content')
    <div class="page-head">
        <h1>{{ __('app.alertes.titre') }}</h1>
    </div>

    <div class="card">
        <x-filtres :action="route('alertes.index')" :paginateur="$alertes">
            <x-champ-recherche />
            <div class="field">
                <label>{{ __('app.commun.statut') }}</label>
                <select name="statut">
                    <option value="">{{ __('app.conteneurs.tous') }}</option>
                    @foreach (\App\Enums\AlerteStatut::cases() as $statut)
                        <option value="{{ $statut->value }}" {{ request('statut') === $statut->value ? 'selected' : '' }}>{{ __("app.alerte_statut.{$statut->value}") }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>{{ __('app.commun.type') }}</label>
                <select name="type">
                    <option value="">{{ __('app.conteneurs.tous') }}</option>
                    @foreach (\App\Enums\AlerteType::cases() as $type)
                        <option value="{{ $type->value }}" {{ request('type') === $type->value ? 'selected' : '' }}>{{ __("app.alerte_type.{$type->value}") }}</option>
                    @endforeach
                </select>
            </div>
        </x-filtres>

        <div class="table-wrap">
            <table>
                <thead><tr><th>{{ __('app.commun.type') }}</th><th>{{ __('app.alertes.message') }}</th><th>{{ __('app.commun.dossier') }}</th><th>{{ __('app.commun.date') }}</th><th>{{ __('app.commun.statut') }}</th><th class="amount">{{ __('app.commun.actions') }}</th></tr></thead>
                <tbody>
                @forelse ($alertes as $alerte)
                    @php
                        $classeLigne = match ($alerte->statut->value) {
                            'nouvelle' => 'rstat-alerte',
                            'lue' => 'rstat-neutre',
                            'traitee' => 'rstat-succes',
                            default => '',
                        };
                    @endphp
                    <tr class="{{ $classeLigne }}">
                        <td><span class="badge {{ $alerte->statut === \App\Enums\AlerteStatut::Traitee ? 'success' : 'warning' }}">{{ __("app.alerte_type.{$alerte->type->value}") }}</span></td>
                        <td>{{ $alerte->message }}</td>
                        <td>@if ($alerte->dossier)<a class="mono" href="{{ route('dossiers.show', $alerte->dossier->numero) }}">{{ $alerte->dossier->numero }}</a>@else—@endif</td>
                        <td class="mono">{{ $alerte->created_at->format('d/m/Y H:i') }}</td>
                        <td><span class="badge {{ $alerte->statut === \App\Enums\AlerteStatut::Nouvelle ? 'danger' : ($alerte->statut === \App\Enums\AlerteStatut::Traitee ? 'success' : 'primary') }}">{{ __("app.alerte_statut.{$alerte->statut->value}") }}</span></td>
                        <td>
                            <div class="row-actions no-print">
                                @unless ($alerte->statut !== \App\Enums\AlerteStatut::Nouvelle)
                                    <form method="POST" action="{{ route('alertes.update', $alerte) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="statut" value="lue">
                                        <button type="submit" class="btn secondary small">{{ __('app.alertes.marquer_lue') }}</button>
                                    </form>
                                @endunless
                                @unless ($alerte->statut === \App\Enums\AlerteStatut::Traitee)
                                    <form method="POST" action="{{ route('alertes.update', $alerte) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="statut" value="traitee">
                                        <button type="submit" class="btn small">{{ __('app.alertes.marquer_traitee') }}</button>
                                    </form>
                                @endunless
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $alertes->links() }}
    </div>

    <div class="card">
        <h2>{{ __('app.notifications.titre') }}</h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th>{{ __('app.notifications.canal') }}</th><th>{{ __('app.notifications.destinataire') }}</th><th>{{ __('app.commun.client') }}</th><th>{{ __('app.notifications.sujet') }}</th><th>{{ __('app.commun.statut') }}</th><th>{{ __('app.notifications.envoyee_le') }}</th></tr></thead>
                <tbody>
                @forelse ($notifications as $notification)
                    <tr>
                        <td><span class="badge info">{{ __("app.notification_canal.{$notification->canal->value}") }}</span></td>
                        <td class="mono">{{ $notification->destinataire }}</td>
                        <td>{{ $notification->client?->raison_sociale ?? '—' }}</td>
                        <td>{{ $notification->sujet }}</td>
                        <td><span class="badge {{ $notification->statut === \App\Enums\NotificationStatut::Envoyee ? 'success' : ($notification->statut === \App\Enums\NotificationStatut::Echec ? 'danger' : 'warning') }}">{{ __("app.notification_statut.{$notification->statut->value}") }}</span></td>
                        <td class="mono">{{ $notification->envoyee_le?->format('d/m/Y H:i') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
