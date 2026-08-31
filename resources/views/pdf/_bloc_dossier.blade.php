@php
    $lignes = [
        [__('app.commun.statut'), __("app.dc_statut.{$document->statut->value}")],
        [__('app.commun.devise'), __('app.devise.'.$document->devise->value)],
        [__('app.factures.emission'), $document->date_emission?->format('d/m/Y') ?? '—'],
        [__('app.factures.echeance'), $document->date_echeance?->format('d/m/Y') ?? '—'],
    ];

    if ($document->dossier !== null) {
        $lignes = array_merge([
            [__('app.commun.dossier'), $document->dossier->numero],
            [__('app.dossiers.bl_awb'), $document->dossier->numero_bl_awb],
        ], $lignes);
    }
@endphp
<div class="bloc-titre">{{ $texteur->texte(__('app.commun.detail')) }}</div>
<table class="fiche">
    @foreach ($lignes as [$libelle, $valeur])
        <tr>
            <td class="cle">{{ $texteur->texte($libelle) }}</td>
            <td class="mono">{{ $texteur->texte($valeur) }}</td>
        </tr>
    @endforeach
</table>
