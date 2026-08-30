<div class="form-grid">
    <div class="field">
        <label>{{ __('app.commun.client') }} *</label>
        <select name="client_id" required>
            <option value=""></option>
            @foreach ($clients as $client)
                <option value="{{ $client->id }}" {{ (string) old('client_id', $dossier->client_id ?? '') === (string) $client->id ? 'selected' : '' }}>{{ $client->raison_sociale }}</option>
            @endforeach
        </select>
    </div>
    <div class="field">
        <label>{{ __('app.dossiers.type') }} *</label>
        <select name="type" required>
            @foreach (\App\Enums\TypeOperation::cases() as $type)
                <option value="{{ $type->value }}" {{ old('type', $dossier->type->value ?? '') === $type->value ? 'selected' : '' }}>{{ __("app.type_operation.{$type->value}") }}</option>
            @endforeach
        </select>
    </div>
    <div class="field">
        <label>{{ __('app.dossiers.mode_transport') }} *</label>
        <select name="mode_transport" required>
            @foreach (\App\Enums\ModeTransport::cases() as $mode)
                <option value="{{ $mode->value }}" {{ old('mode_transport', $dossier->mode_transport->value ?? '') === $mode->value ? 'selected' : '' }}>{{ __("app.mode_transport.{$mode->value}") }}</option>
            @endforeach
        </select>
    </div>
    <div class="field"><label>{{ __('app.dossiers.port_aeroport') }} *</label><input name="port_aeroport" value="{{ old('port_aeroport', $dossier->port_aeroport ?? '') }}" required></div>
    <div class="field"><label>{{ __('app.dossiers.fournisseur_destinataire') }} *</label><input name="fournisseur_destinataire" value="{{ old('fournisseur_destinataire', $dossier->fournisseur_destinataire ?? '') }}" required></div>
    <div class="field"><label>{{ __('app.dossiers.arrivee_prevue') }} *</label><input type="date" name="date_arrivee_prevue" value="{{ old('date_arrivee_prevue', isset($dossier) ? $dossier->date_arrivee_prevue?->toDateString() : '') }}" required></div>
    <div class="field"><label>{{ __('app.dossiers.arrivee_reelle') }}</label><input type="date" name="date_arrivee_reelle" value="{{ old('date_arrivee_reelle', isset($dossier) ? $dossier->date_arrivee_reelle?->toDateString() : '') }}"></div>
    <div class="field"><label>{{ __('app.dossiers.bl_awb') }} *</label><input name="numero_bl_awb" value="{{ old('numero_bl_awb', $dossier->numero_bl_awb ?? '') }}" required class="mono"></div>
    <div class="field"><label>{{ __('app.dossiers.nombre_colis') }} *</label><input type="number" min="0" name="nombre_colis" value="{{ old('nombre_colis', $dossier->nombre_colis ?? '') }}" required class="mono"></div>
    <div class="field"><label>{{ __('app.dossiers.poids') }} *</label><input type="number" step="0.001" min="0" name="poids" value="{{ old('poids', $dossier->poids ?? '') }}" required class="mono"></div>
    <div class="field"><label>{{ __('app.dossiers.volume') }} *</label><input type="number" step="0.001" min="0" name="volume" value="{{ old('volume', $dossier->volume ?? '') }}" required class="mono"></div>
    <div class="field span-2"><label>{{ __('app.dossiers.nature_marchandise') }} *</label><input name="nature_marchandise" value="{{ old('nature_marchandise', $dossier->nature_marchandise ?? '') }}" required></div>
    <div class="field"><label>{{ __('app.dossiers.valeur_declaree') }} *</label><input type="number" step="0.01" min="0" name="valeur_declaree" value="{{ old('valeur_declaree', $dossier->valeur_declaree ?? '') }}" required class="mono"></div>
    <div class="field">
        <label>{{ __('app.commun.devise') }} *</label>
        <select name="devise" required>
            @foreach (\App\Enums\Devise::cases() as $devise)
                <option value="{{ $devise->value }}" {{ old('devise', $dossier->devise->value ?? 'DZD') === $devise->value ? 'selected' : '' }}>{{ $devise->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="field"><label>{{ __('app.dossiers.incoterm') }} *</label><input name="incoterm" value="{{ old('incoterm', $dossier->incoterm ?? '') }}" placeholder="FOB, CIF, EXW…" required class="mono"></div>
</div>
