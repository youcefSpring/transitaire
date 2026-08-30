<div class="form-grid">
    <div class="field span-2">
        <label>{{ __('app.clients.raison_sociale') }} *</label>
        <input name="raison_sociale" value="{{ old('raison_sociale', $client->raison_sociale ?? '') }}" required>
    </div>
    <div class="field"><label>{{ __('app.clients.nif') }} *</label><input name="nif" value="{{ old('nif', $client->nif ?? '') }}" required class="mono"></div>
    <div class="field"><label>{{ __('app.clients.nis') }} *</label><input name="nis" value="{{ old('nis', $client->nis ?? '') }}" required class="mono"></div>
    <div class="field"><label>{{ __('app.clients.rc') }} *</label><input name="rc" value="{{ old('rc', $client->rc ?? '') }}" required class="mono"></div>
    <div class="field"><label>{{ __('app.commun.telephone') }} *</label><input name="telephone" value="{{ old('telephone', $client->telephone ?? '') }}" placeholder="+213XXXXXXXXX" required class="mono"></div>
    <div class="field"><label>{{ __('app.commun.email') }} *</label><input type="email" name="email" value="{{ old('email', $client->email ?? '') }}" required></div>
    <div class="field"><label>{{ __('app.clients.conditions_paiement') }} *</label><input name="conditions_paiement" value="{{ old('conditions_paiement', $client->conditions_paiement ?? '') }}" required></div>
    <div class="field span-2"><label>{{ __('app.commun.adresse') }} *</label><input name="adresse" value="{{ old('adresse', $client->adresse ?? '') }}" required></div>
</div>

<h2 style="margin-block-start:18px">{{ __('app.clients.contacts') }}</h2>
<table id="contacts-table">
    <thead>
    <tr>
        <th>{{ __('app.commun.nom') }}</th>
        <th>{{ __('app.clients.fonction') }}</th>
        <th>{{ __('app.commun.telephone') }}</th>
        <th>{{ __('app.commun.email') }}</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    @foreach (old('contacts', $client->contacts ?? []) as $contact)
        <tr>
            <td><input name="contacts[{{ $loop->index }}][nom]" value="{{ $contact['nom'] ?? $contact->nom ?? '' }}"></td>
            <td><input name="contacts[{{ $loop->index }}][fonction]" value="{{ $contact['fonction'] ?? $contact->fonction ?? '' }}"></td>
            <td><input name="contacts[{{ $loop->index }}][telephone]" value="{{ $contact['telephone'] ?? $contact->telephone ?? '' }}"></td>
            <td><input name="contacts[{{ $loop->index }}][email]" value="{{ $contact['email'] ?? $contact->email ?? '' }}"></td>
            <td><button type="button" class="btn danger small" onclick="this.closest('tr').remove()">✕</button></td>
        </tr>
    @endforeach
    </tbody>
</table>
<button type="button" class="btn secondary small" style="margin-block-start:10px" id="ajouter-contact">＋ {{ __('app.clients.ajouter_contact') }}</button>

<script>
    document.getElementById('ajouter-contact').addEventListener('click', function () {
        var corps = document.querySelector('#contacts-table tbody');
        var ligne = corps.insertRow();
        var index = corps.rows.length - 1;
        ['nom', 'fonction', 'telephone', 'email'].forEach(function (champ) {
            ligne.insertCell().innerHTML = '<input name="contacts[' + index + '][' + champ + ']">';
        });
        ligne.insertCell().innerHTML = '<button type="button" class="btn danger small" onclick="this.closest(\'tr\').remove()">✕</button>';
    });
</script>
