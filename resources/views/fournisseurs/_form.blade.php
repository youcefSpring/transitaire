<div class="form-grid">
    <div class="field span-2"><label>{{ __('app.commun.nom') }} *</label><input name="nom" value="{{ old('nom', $fournisseur->nom ?? '') }}" required><x-aide cle="fournisseurs.nom" /></div>
    <div class="field">
        <label>{{ __('app.commun.type') }} *</label>
        <select name="type" required>
            @foreach (\App\Enums\FournisseurType::cases() as $type)
                <option value="{{ $type->value }}" {{ old('type', $fournisseur->type->value ?? '') === $type->value ? 'selected' : '' }}>{{ __("app.fournisseur_type.{$type->value}") }}</option>
            @endforeach
        </select>
        <x-aide cle="fournisseurs.type" />
    </div>
    <div class="field"><label>{{ __('app.commun.telephone') }}</label><input name="telephone" value="{{ old('telephone', $fournisseur->telephone ?? '') }}" class="mono"><x-aide cle="fournisseurs.telephone" /></div>
    <div class="field"><label>{{ __('app.commun.email') }}</label><input type="email" name="email" value="{{ old('email', $fournisseur->email ?? '') }}"><x-aide cle="fournisseurs.email" /></div>
    <div class="field"><label>{{ __('app.commun.contact') }}</label><input name="contact" value="{{ old('contact', $fournisseur->contact ?? '') }}"><x-aide cle="fournisseurs.contact" /></div>
    <div class="field span-2"><label>{{ __('app.commun.adresse') }}</label><input name="adresse" value="{{ old('adresse', $fournisseur->adresse ?? '') }}"><x-aide cle="fournisseurs.adresse" /></div>
</div>
