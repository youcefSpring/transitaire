<div class="form-grid">
    <div class="field span-2"><label>{{ __('app.commun.nom') }} *</label><input name="nom" value="{{ old('nom', $fournisseur->nom ?? '') }}" required></div>
    <div class="field">
        <label>{{ __('app.commun.type') }} *</label>
        <select name="type" required>
            @foreach (\App\Enums\FournisseurType::cases() as $type)
                <option value="{{ $type->value }}" {{ old('type', $fournisseur->type->value ?? '') === $type->value ? 'selected' : '' }}>{{ __("app.fournisseur_type.{$type->value}") }}</option>
            @endforeach
        </select>
    </div>
    <div class="field"><label>{{ __('app.commun.telephone') }}</label><input name="telephone" value="{{ old('telephone', $fournisseur->telephone ?? '') }}" class="mono"></div>
    <div class="field"><label>{{ __('app.commun.email') }}</label><input type="email" name="email" value="{{ old('email', $fournisseur->email ?? '') }}"></div>
    <div class="field"><label>{{ __('app.commun.contact') }}</label><input name="contact" value="{{ old('contact', $fournisseur->contact ?? '') }}"></div>
    <div class="field span-2"><label>{{ __('app.commun.adresse') }}</label><input name="adresse" value="{{ old('adresse', $fournisseur->adresse ?? '') }}"></div>
</div>
