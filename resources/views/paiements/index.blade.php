@extends('layouts.app')

@section('titre', __('app.paiements.titre'))

@section('content')
    <div class="page-head">
        <h1>{{ __('app.paiements.titre') }}</h1>
    </div>

    <div class="card no-print">
        <h2>{{ __('app.paiements.nouveau') }}</h2>
        <form method="POST" action="{{ route('paiements.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field">
                    <label>{{ __('app.commun.client') }} *</label>
                    <select name="client_id" required>
                        <option value=""></option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->raison_sociale }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>{{ __('app.commun.numero') }} ({{ __('app.dc_type.facture') }})</label>
                    <select name="document_id">
                        <option value=""></option>
                        @foreach ($factures as $facture)
                            <option value="{{ $facture->id }}">{{ $facture->numero }} — {{ $facture->montant_total }} {{ $facture->devise->value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>{{ __('app.paiements.mode') }} *</label>
                    <select name="mode" required>
                        @foreach (\App\Enums\PaiementMode::cases() as $mode)
                            <option value="{{ $mode->value }}">{{ __("app.paiement_mode.{$mode->value}") }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field"><label>{{ __('app.commun.montant') }} *</label><input type="number" step="0.01" min="0" name="montant" required class="mono"></div>
                <div class="field">
                    <label>{{ __('app.commun.devise') }} *</label>
                    <select name="devise" required>
                        @foreach (\App\Enums\Devise::cases() as $devise)
                            <option value="{{ $devise->value }}">{{ $devise->value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field"><label>{{ __('app.commun.date') }} *</label><input type="date" name="date_paiement" value="{{ now()->toDateString() }}" required class="mono"></div>
                <div class="field"><label>{{ __('app.paiements.reference') }}</label><input name="reference" class="mono"></div>
            </div>
            <button type="submit" class="btn" style="margin-block-start:14px">{{ __('app.commun.enregistrer') }}</button>
        </form>
    </div>

    <div class="card">
        <x-filtres :action="route('paiements.index')" :paginateur="$paiements">
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
                <label>{{ __('app.paiements.mode') }}</label>
                <select name="mode">
                    <option value="">{{ __('app.commun.tous') }}</option>
                    @foreach (\App\Enums\PaiementMode::cases() as $mode)
                        <option value="{{ $mode->value }}" {{ request('mode') === $mode->value ? 'selected' : '' }}>{{ __("app.paiement_mode.{$mode->value}") }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>{{ __('app.commun.du') }}</label>
                <input type="date" name="du" value="{{ request('du') }}">
            </div>
            <div class="field">
                <label>{{ __('app.commun.au') }}</label>
                <input type="date" name="au" value="{{ request('au') }}">
            </div>
        </x-filtres>
        <div class="table-wrap">
            <table>
                <thead><tr><th>{{ __('app.commun.date') }}</th><th>{{ __('app.commun.client') }}</th><th>{{ __('app.paiements.mode') }}</th><th>{{ __('app.commun.numero') }}</th><th>{{ __('app.paiements.reference') }}</th><th>{{ __('app.commun.montant') }}</th></tr></thead>
                <tbody>
                @forelse ($paiements as $paiement)
                    <tr>
                        <td class="mono">{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                        <td>{{ $paiement->client?->raison_sociale }}</td>
                        <td>{{ __("app.paiement_mode.{$paiement->mode->value}") }}</td>
                        <td class="mono">{{ $paiement->document?->numero ?? '—' }}</td>
                        <td class="mono">{{ $paiement->reference }}</td>
                        <td class="mono">{{ $paiement->montant }} {{ $paiement->devise->value }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">{{ __('app.commun.aucune_donnee') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $paiements->links() }}
    </div>
@endsection
