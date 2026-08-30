<div class="form-grid">
    <div class="field"><label>{{ __('app.commun.nom') }} *</label><input name="name" value="{{ old('name', $user->name ?? '') }}" required><x-aide cle="users.nom" /></div>
    <div class="field"><label>{{ __('app.commun.email') }} *</label><input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required><x-aide cle="users.email" /></div>
    <div class="field">
        <label>{{ __('app.users.profil') }} *</label>
        <select name="profile" required>
            @foreach (\App\Enums\UserProfile::cases() as $profil)
                <option value="{{ $profil->value }}" {{ old('profile', $user->profile->value ?? '') === $profil->value ? 'selected' : '' }}>{{ __("app.profil.{$profil->value}") }}</option>
            @endforeach
        </select>
        <x-aide cle="users.profil" />
    </div>
    <div class="field">
        <label>{{ __('app.users.mot_de_passe') }} @if (isset($user))*@else* @endif</label>
        <input type="password" name="password" @if (! isset($user)) required @endif autocomplete="new-password">
        <x-aide cle="users.mot_de_passe" />
    </div>
    <div class="field">
        <label>{{ __('app.users.actif') }}</label>
        <select name="is_active">
            <option value="1" {{ old('is_active', $user->is_active ?? true) ? 'selected' : '' }}>✓</option>
            <option value="0" {{ ! (old('is_active', $user->is_active ?? true)) ? 'selected' : '' }}>✕</option>
        </select>
        <x-aide cle="users.actif" />
    </div>
</div>
