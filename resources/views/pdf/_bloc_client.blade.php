<div class="bloc-titre">{{ $texteur->texte($estRecu ? __('app.pdf.recu_de') : __('app.commun.client')) }}</div>
<table class="fiche">
    <tr>
        <td class="cle">{{ $texteur->texte(__('app.clients.raison_sociale')) }}</td>
        <td><strong>{{ $texteur->texte($document->client?->raison_sociale) }}</strong></td>
    </tr>
    @if ($document->client?->adresse)
        <tr>
            <td class="cle">{{ $texteur->texte(__('app.commun.adresse')) }}</td>
            <td>{{ $texteur->texte($document->client->adresse) }}</td>
        </tr>
    @endif
    @if ($document->client?->nif)
        <tr>
            <td class="cle">{{ $texteur->texte(__('app.pdf.nif')) }}</td>
            <td class="mono">{{ $texteur->texte($document->client->nif) }}</td>
        </tr>
    @endif
    @if ($document->client?->nis)
        <tr>
            <td class="cle">{{ $texteur->texte(__('app.pdf.nis')) }}</td>
            <td class="mono">{{ $texteur->texte($document->client->nis) }}</td>
        </tr>
    @endif
    @if ($document->client?->rc)
        <tr>
            <td class="cle">{{ $texteur->texte(__('app.pdf.rc')) }}</td>
            <td class="mono">{{ $texteur->texte($document->client->rc) }}</td>
        </tr>
    @endif
</table>
