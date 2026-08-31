@extends('pdf.layout', ['titreDocument' => __("app.dc_type.{$document->type->value}"), 'sousTitreDocument' => $document->numero])

@section('contenu')
    @php
        $libelleType = __("app.dc_type.{$document->type->value}");
        $estRecu = $document->type->value === 'recu';
        $montantDzd = $document->devise->value === 'DZD'
            ? (float) $document->montant_total
            : $contrevaleur;
        $mention = $texteur->arrete($libelleType, $montantDzd);

        $colonnes = [
            ['designation', __('app.factures.prestation'), false],
            ['categorie', __('app.commun.type'), false],
            ['quantite', __('app.dossiers.quantite'), true],
            ['prix_unitaire', __('app.factures.prix_unitaire'), true],
            ['montant', __('app.commun.montant'), true],
        ];
        $colonnes = $rtl ? array_reverse($colonnes) : $colonnes;
    @endphp

    <table class="bloc">
        <tr>
            @if ($rtl)
                <td width="50%" class="droite">
                    @include('pdf._bloc_dossier')
                </td>
                <td width="50%" class="droite">
                    @include('pdf._bloc_client')
                </td>
            @else
                <td width="50%">
                    @include('pdf._bloc_client')
                </td>
                <td width="50%">
                    @include('pdf._bloc_dossier')
                </td>
            @endif
        </tr>
    </table>

    <div class="bloc">
        <div class="bloc-titre">{{ $texteur->texte(__('app.factures.lignes')) }}</div>
        <table class="data">
            <thead>
                <tr>
                    @foreach ($colonnes as [$cle, $libelle, $nombre])
                        <th class="{{ $nombre ? 'droite' : '' }}">{{ $texteur->texte($libelle) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($document->lignes as $ligne)
                    <tr>
                        @foreach ($colonnes as [$cle, $libelle, $nombre])
                            @php
                                $valeur = match ($cle) {
                                    'categorie' => __("app.frais_categorie.{$ligne->categorie}"),
                                    'quantite' => $ligne->quantite,
                                    'prix_unitaire' => number_format((float) $ligne->prix_unitaire, 2, ',', ' '),
                                    'montant' => number_format((float) $ligne->montant, 2, ',', ' '),
                                    default => $ligne->designation,
                                };
                            @endphp
                            <td class="{{ $nombre ? 'droite mono' : '' }}">{{ $texteur->texte($valeur) }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="5" class="centre muted">{{ $texteur->texte(__('app.commun.aucune_donnee')) }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @php
        $totaux = [
            [__('app.factures.total_prestations'), number_format((float) $document->total_prestations, 2, ',', ' '), false],
            [__('app.factures.total_frais'), number_format((float) $document->total_frais, 2, ',', ' '), false],
            [__('app.factures.taxes'), number_format((float) $document->total_taxes, 2, ',', ' '), false],
            [__('app.factures.remise'), '− '.number_format((float) $document->remise, 2, ',', ' '), false],
            [__('app.factures.montant_total'), number_format((float) $document->montant_total, 2, ',', ' ').' '.__('app.devise.'.$document->devise->value), true],
        ];
        $avecContrevaleur = $contrevaleur !== null;
    @endphp
    <table class="data" style="margin-top: 4mm">
        @foreach ($totaux as [$libelle, $valeur, $estGrand])
            @if ($rtl)
                <tr class="totaux">
                    <td width="30%" class="mono {{ $estGrand ? 'grand' : '' }}">{{ $texteur->texte($valeur) }}</td>
                    <td width="70%" class="droite {{ $estGrand ? 'grand' : '' }}">{{ $texteur->texte($libelle) }}</td>
                </tr>
            @else
                <tr class="totaux">
                    <td width="40%"></td>
                    <td width="30%" class="droite {{ $estGrand ? 'grand' : '' }}">{{ $texteur->texte($libelle) }}</td>
                    <td width="30%" class="droite mono {{ $estGrand ? 'grand' : '' }}">{{ $texteur->texte($valeur) }}</td>
                </tr>
            @endif
        @endforeach
        @if ($avecContrevaleur)
            @if ($rtl)
                <tr class="totaux">
                    <td class="mono grand">{{ number_format($contrevaleur, 2, ',', ' ') }} DZD</td>
                    <td class="droite grand">{{ $texteur->texte(__('app.pdf.contrevaleur')) }}</td>
                </tr>
            @else
                <tr class="totaux">
                    <td width="40%"></td>
                    <td width="30%" class="droite grand">{{ $texteur->texte(__('app.pdf.contrevaleur')) }}</td>
                    <td width="30%" class="droite mono grand">{{ number_format($contrevaleur, 2, ',', ' ') }} DZD</td>
                </tr>
            @endif
        @endif
    </table>

    @if ($mention !== null)
        <div class="arrete">{{ $mention }}</div>
    @endif

    @if ($document->paiements->isNotEmpty())
        <div class="bloc">
            <div class="bloc-titre">{{ $texteur->texte(__('app.nav.paiements')) }}</div>
            <table class="data">
                <thead>
                    <tr>
                        @php
                            $entetesPaiements = [__('app.commun.date'), __('app.paiements.mode'), __('app.commun.montant'), __('app.paiements.reference')];
                        @endphp
                        @foreach (($rtl ? array_reverse($entetesPaiements) : $entetesPaiements) as $libelle)
                            <th>{{ $texteur->texte($libelle) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($document->paiements as $paiement)
                        @php
                            $clesPaiements = ['date', 'mode', 'montant', 'reference'];
                        @endphp
                        <tr>
                            @foreach ($clesPaiements as $cle)
                                @php
                                    $valeur = match ($cle) {
                                        'date' => $paiement->date_paiement->format('d/m/Y'),
                                        'mode' => __("app.paiement_mode.{$paiement->mode->value}"),
                                        'montant' => number_format((float) $paiement->montant, 2, ',', ' ').' '.__('app.devise.'.$paiement->devise->value),
                                        default => $paiement->reference ?? '—',
                                    };
                                @endphp
                                <td class="{{ in_array($cle, ['montant', 'date', 'reference']) ? 'mono droite' : '' }}">{{ $texteur->texte($valeur) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if ($solde !== null)
        <div class="bloc">
            <div class="bloc-titre">{{ $texteur->texte(__("app.dc_type.{$document->type->value}")) }} — {{ $texteur->texte($document->client?->raison_sociale) }}</div>
            <table class="fiche">
                @foreach ([__('app.clients.total_facture') => $solde['dzd']['total_facture'], __('app.clients.total_paye') => $solde['dzd']['total_paye'], __('app.clients.reste_a_payer') => $solde['dzd']['reste_a_payer']] as $libelle => $valeur)
                    <tr>
                        @if ($rtl)
                            <td class="mono droite">{{ number_format($valeur, 2, ',', ' ') }} DZD</td>
                            <td class="cle droite">{{ $texteur->texte($libelle) }}</td>
                        @else
                            <td class="cle">{{ $texteur->texte($libelle) }}</td>
                            <td class="droite mono">{{ number_format($valeur, 2, ',', ' ') }} DZD</td>
                        @endif
                    </tr>
                @endforeach
            </table>
        </div>
    @endif
@endsection
