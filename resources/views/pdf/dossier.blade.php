@extends('pdf.layout', ['titreDocument' => __('app.pdf.synthese_dossier'), 'sousTitreDocument' => $dossier->numero])

@section('contenu')
    @php
        $identification = [
            [__('app.commun.client'), $dossier->client?->raison_sociale],
            [__('app.dossiers.type'), __("app.type_operation.{$dossier->type->value}")],
            [__('app.dossiers.mode_transport'), __("app.mode_transport.{$dossier->mode_transport->value}")],
            [__('app.dossiers.port_aeroport'), $dossier->port_aeroport],
            [__('app.dossiers.fournisseur_destinataire'), $dossier->fournisseur_destinataire],
            [__('app.dossiers.bl_awb'), $dossier->numero_bl_awb],
            [__('app.dossiers.arrivee_prevue'), $dossier->date_arrivee_prevue?->format('d/m/Y')],
            [__('app.dossiers.arrivee_reelle'), $dossier->date_arrivee_reelle?->format('d/m/Y') ?? '—'],
            [__('app.dossiers.nombre_colis'), $dossier->nombre_colis],
            [__('app.dossiers.poids'), $dossier->poids.' kg'],
            [__('app.dossiers.volume'), $dossier->volume.' m³'],
            [__('app.dossiers.nature_marchandise'), $dossier->nature_marchandise],
            [__('app.dossiers.valeur_declaree'), number_format((float) $dossier->valeur_declaree, 2, ',', ' ').' '.__('app.devise.'.$dossier->devise->value)],
            [__('app.dossiers.incoterm'), $dossier->incoterm],
            [__('app.commun.statut'), __("app.statut.{$dossier->statut->value}")],
        ];

        $colonnesMarchandises = [
            ['designation', __('app.dossiers.designation'), false],
            ['quantite', __('app.dossiers.quantite'), true],
            ['unite', __('app.dossiers.unite'), false],
            ['valeur', __('app.dossiers.valeur'), true],
            ['pays_origine', __('app.dossiers.pays_origine'), false],
            ['code_tarifaire', __('app.dossiers.code_tarifaire'), false],
        ];
        $colonnesMarchandises = $rtl ? array_reverse($colonnesMarchandises) : $colonnesMarchandises;

        $colonnesFrais = [
            ['date', __('app.commun.date'), true],
            ['categorie', __('app.commun.type'), false],
            ['libelle', __('app.dossiers.libelle'), false],
            ['sens', __('app.dossiers.sens'), false],
            ['fournisseur', __('app.commun.fournisseur'), false],
            ['montant', __('app.commun.montant'), true],
        ];
        $colonnesFrais = $rtl ? array_reverse($colonnesFrais) : $colonnesFrais;
    @endphp

    <div class="bloc">
        <table class="fiche">
            @foreach (array_chunk($identification, 2) as $paire)
                @php
                    $paires = $rtl ? array_reverse($paire) : $paire;
                @endphp
                <tr>
                    @foreach ($paires as [$libelle, $valeur])
                        <td class="cle">{{ $texteur->texte($libelle) }}</td>
                        <td>{{ $texteur->texte($valeur) }}</td>
                    @endforeach
                    @if (count($paire) === 1)
                        <td class="cle"></td><td></td>
                    @endif
                </tr>
            @endforeach
        </table>
    </div>

    <div class="bloc">
        <div class="bloc-titre">{{ $texteur->texte(__('app.dossiers.marchandises')) }}</div>
        <table class="data">
            <thead>
                <tr>
                    @foreach ($colonnesMarchandises as [$cle, $libelle, $nombre])
                        <th class="{{ $nombre ? 'droite' : '' }}">{{ $texteur->texte($libelle) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($dossier->marchandises as $marchandise)
                    <tr>
                        @foreach ($colonnesMarchandises as [$cle, $libelle, $nombre])
                            @php
                                $valeur = match ($cle) {
                                    'quantite' => $marchandise->quantite,
                                    'valeur' => number_format((float) $marchandise->valeur, 2, ',', ' '),
                                    default => $marchandise->{$cle} ?? '—',
                                };
                            @endphp
                            <td class="{{ $nombre ? 'droite mono' : '' }}">{{ $texteur->texte($valeur) }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="6" class="centre muted">{{ $texteur->texte(__('app.commun.aucune_donnee')) }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bloc">
        <div class="bloc-titre">{{ $texteur->texte(__('app.dossiers.douane')) }}</div>
        <table class="data">
            <thead>
                <tr>
                    @php
                        $entetesDouane = [__('app.dossiers.etape'), __('app.audit.utilisateur'), __('app.dossiers.executed_le')];
                    @endphp
                    @foreach (($rtl ? array_reverse($entetesDouane) : $entetesDouane) as $libelle)
                        <th>{{ $texteur->texte($libelle) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($dossier->douaneEtapes as $etape)
                    <tr>
                        @if ($rtl)
                            <td class="mono droite">{{ $etape->executed_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $texteur->texte($etape->executedBy?->name) }}</td>
                            <td>{{ $texteur->texte(__("app.douane_etape.{$etape->etape->value}")) }}</td>
                        @else
                            <td>{{ $texteur->texte(__("app.douane_etape.{$etape->etape->value}")) }}</td>
                            <td>{{ $texteur->texte($etape->executedBy?->name) }}</td>
                            <td class="mono droite">{{ $etape->executed_at->format('d/m/Y H:i') }}</td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="3" class="centre muted">{{ $texteur->texte(__('app.commun.aucune_donnee')) }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bloc">
        <div class="bloc-titre">{{ $texteur->texte(__('app.dossiers.frais')) }}</div>
        <table class="data">
            <thead>
                <tr>
                    @foreach ($colonnesFrais as [$cle, $libelle, $nombre])
                        <th class="{{ $nombre ? 'droite' : '' }}">{{ $texteur->texte($libelle) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($dossier->frais as $frai)
                    <tr>
                        @foreach ($colonnesFrais as [$cle, $libelle, $nombre])
                            @php
                                $valeur = match ($cle) {
                                    'date' => $frai->date_frais->format('d/m/Y'),
                                    'categorie' => __("app.frais_categorie.{$frai->categorie->value}"),
                                    'sens' => __("app.frais_sens.{$frai->sens->value}"),
                                    'fournisseur' => $frai->fournisseur?->nom ?? '—',
                                    'libelle' => $frai->libelle ?? '—',
                                    'montant' => number_format((float) $frai->montant, 2, ',', ' ').' '.__('app.devise.'.$frai->devise->value),
                                };
                            @endphp
                            <td class="{{ $nombre ? 'droite mono' : '' }}">{{ $texteur->texte($valeur) }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="6" class="centre muted">{{ $texteur->texte(__('app.commun.aucune_donnee')) }}</td></tr>
                @endforelse
            </tbody>
        </table>
        <table class="fiche" style="margin-top: 3mm">
            @foreach ([[__('app.dossiers.facture_client'), $marge['dzd']['facture_client']], [__('app.dossiers.supporte_transitaire'), $marge['dzd']['supporte_transitaire']], [__('app.dossiers.marge_reelle'), $marge['dzd']['marge_reelle']]] as [$libelle, $valeur])
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

    @if ($dossier->documentsCommerciaux->isNotEmpty())
        <div class="bloc">
            <div class="bloc-titre">{{ $texteur->texte(__('app.dossiers.facturation')) }}</div>
            <table class="data">
                <thead>
                <tr>
                    @php
                        $entetesFacturation = [__('app.commun.numero'), __('app.commun.type'), __('app.commun.statut'), __('app.commun.montant')];
                    @endphp
                    @foreach (($rtl ? array_reverse($entetesFacturation) : $entetesFacturation) as $libelle)
                        <th>{{ $texteur->texte($libelle) }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                    @foreach ($dossier->documentsCommerciaux as $document)
                        <tr>
                            @if ($rtl)
                                <td class="droite mono">{{ number_format((float) $document->montant_total, 2, ',', ' ') }} {{ $texteur->texte(__('app.devise.'.$document->devise->value)) }}</td>
                                <td>{{ $texteur->texte(__("app.dc_statut.{$document->statut->value}")) }}</td>
                                <td>{{ $texteur->texte(__("app.dc_type.{$document->type->value}")) }}</td>
                                <td class="mono">{{ $document->numero }}</td>
                            @else
                                <td class="mono">{{ $document->numero }}</td>
                                <td>{{ $texteur->texte(__("app.dc_type.{$document->type->value}")) }}</td>
                                <td>{{ $texteur->texte(__("app.dc_statut.{$document->statut->value}")) }}</td>
                                <td class="droite mono">{{ number_format((float) $document->montant_total, 2, ',', ' ') }} {{ $texteur->texte(__('app.devise.'.$document->devise->value)) }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
