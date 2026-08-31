<?php

namespace App\Services;

use ArPHP\I18N\Arabic;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

/**
 * Génération des papiers officiels (PDF) — style documents algériens.
 *
 * Injecté dans les vues PDF sous le nom « texteur » : les textes passent
 * par texte() pour le façonnage des glyphes arabes (ar-php + dompdf),
 * et montant() pour la mention légale du montant en toutes lettres.
 */
class PdfExportService
{
    private ?Arabic $glyphe = null;

    public function __construct(
        private readonly MontantEnLettres $montants,
    ) {}

    /**
     * @param  array<string, mixed>  $donnees
     */
    public function telecharger(string $vue, array $donnees, string $nomFichier): Response
    {
        $donnees['texteur'] = $this;
        $donnees['rtl'] = app()->getLocale() === 'ar';

        return Pdf::loadView($vue, $donnees)
            ->setPaper('a4')
            ->download($nomFichier);
    }

    /**
     * Texte prêt pour dompdf : façonné (présentation + ordre visuel) en arabe,
     * inchangé en français. Les chiffres restent occidentaux (norme algérienne).
     */
    public function texte(?string $texte): string
    {
        if ($texte === null || $texte === '' || app()->getLocale() !== 'ar') {
            return $texte ?? '';
        }

        $this->glyphe ??= new Arabic('Glyphs');

        return $this->glyphe->utf8Glyphs($texte, 200, false);
    }

    /**
     * Mention légale du montant en toutes lettres (« dinars algériens / سنتيم »).
     */
    public function montant(float $montant): string
    {
        return $this->texte($this->montants->enLettres($montant));
    }

    /**
     * Mention légale complète (« Arrêtée la présente facture à la somme de… »),
     * null lorsque aucun montant en dinars n'est disponible.
     */
    public function arrete(string $libelleType, ?float $montantDzd): ?string
    {
        if ($montantDzd === null) {
            return null;
        }

        return $this->texte(__('app.pdf.arrete', [
            'type' => $libelleType,
            'montant' => $this->montants->enLettres($montantDzd),
        ]));
    }
}
