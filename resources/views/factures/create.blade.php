@extends('layouts.app')

@section('titre', __('app.factures.nouveau'))

@section('content')
    <div class="page-head">
        <h1>{{ __('app.factures.nouveau') }}</h1>
        <div class="actions"><a class="btn secondary small" href="{{ route('documents-commerciaux.index') }}">← {{ __('app.commun.retour') }}</a></div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('documents-commerciaux.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field">
                    <label>{{ __('app.commun.type') }} *</label>
                    <select name="type" required>
                        @foreach (\App\Enums\DocumentCommercialType::cases() as $type)
                            <option value="{{ $type->value }}">{{ __("app.dc_type.{$type->value}") }}</option>
                        @endforeach
                    </select>
                </div>
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
                    <label>{{ __('app.commun.dossier') }}</label>
                    <select name="dossier_id">
                        <option value=""></option>
                        @foreach ($dossiers as $dossier)
                            <option value="{{ $dossier->id }}">{{ $dossier->numero }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>{{ __('app.commun.devise') }} *</label>
                    <select name="devise" required>
                        @foreach (\App\Enums\Devise::cases() as $devise)
                            <option value="{{ $devise->value }}">{{ $devise->value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field"><label>{{ __('app.factures.taxes') }} *</label><input type="number" step="0.01" min="0" name="total_taxes" value="0" required class="mono"></div>
                <div class="field"><label>{{ __('app.factures.remise') }} *</label><input type="number" step="0.01" min="0" name="remise" value="0" required class="mono"></div>
                <div class="field"><label>{{ __('app.factures.emission') }}</label><input type="date" name="date_emission" class="mono"></div>
                <div class="field"><label>{{ __('app.factures.echeance') }}</label><input type="date" name="date_echeance" class="mono"></div>
            </div>

            <h2 style="margin-block-start:18px">{{ __('app.factures.lignes') }}</h2>
            <div class="table-wrap">
                <table id="lignes-table">
                    <thead><tr><th>{{ __('app.factures.prestation') }} *</th><th>{{ __('app.commun.type') }} *</th><th>{{ __('app.dossiers.quantite') }} *</th><th>{{ __('app.factures.prix_unitaire') }} *</th></tr></thead>
                    <tbody>
                    @foreach (old('lignes', [['designation' => '', 'categorie' => 'transit', 'quantite' => 1, 'prix_unitaire' => '']]) as $ligne)
                        <tr>
                            <td><input name="lignes[{{ $loop->index }}][designation]" value="{{ $ligne['designation'] ?? '' }}" required></td>
                            <td>
                                <select name="lignes[{{ $loop->index }}][categorie]">
                                    @foreach (['transit', 'dedouanement', 'manutention', 'transport', 'stockage', 'frais_portuaires', 'frais_administratifs', 'autres_prestations'] as $categorie)
                                        <option value="{{ $categorie }}" {{ ($ligne['categorie'] ?? '') === $categorie ? 'selected' : '' }}>{{ __("app.frais_categorie.{$categorie}") }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" step="0.001" min="0" name="lignes[{{ $loop->index }}][quantite]" value="{{ $ligne['quantite'] ?? 1 }}" required class="mono" style="width:110px"></td>
                            <td><input type="number" step="0.01" min="0" name="lignes[{{ $loop->index }}][prix_unitaire]" value="{{ $ligne['prix_unitaire'] ?? '' }}" required class="mono" style="width:130px"></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn secondary small" style="margin-block-start:10px" id="ajouter-ligne">＋ {{ __('app.factures.ajouter_ligne') }}</button>

            <div style="display:flex;gap:8px;margin-block-start:18px">
                <button type="submit" class="btn">{{ __('app.commun.enregistrer') }}</button>
                <a class="btn secondary" href="{{ route('documents-commerciaux.index') }}">{{ __('app.commun.annuler') }}</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('ajouter-ligne').addEventListener('click', function () {
            var corps = document.querySelector('#lignes-table tbody');
            var ligne = corps.insertRow();
            var index = corps.rows.length - 1;

            ligne.insertCell().innerHTML = '<input name="lignes[' + index + '][designation]" required>';

            var options = @json(collect(['transit', 'dedouanement', 'manutention', 'transport', 'stockage', 'frais_portuaires', 'frais_administratifs', 'autres_prestations'])
                ->mapWithKeys(fn ($c) => [$c => __("app.frais_categorie.{$c}")])->all());
            var select = '<select name="lignes[' + index + '][categorie]">';
            for (var valeur in options) { select += '<option value="' + valeur + '">' + options[valeur] + '</option>'; }
            select += '</select>';
            ligne.insertCell().innerHTML = select;
            ligne.insertCell().innerHTML = '<input type="number" step="0.001" min="0" name="lignes[' + index + '][quantite]" value="1" required class="mono" style="width:110px">';
            ligne.insertCell().innerHTML = '<input type="number" step="0.01" min="0" name="lignes[' + index + '][prix_unitaire]" required class="mono" style="width:130px">';
        });
    </script>
@endsection
