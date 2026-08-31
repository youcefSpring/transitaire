<?php

namespace App\Services;

/**
 * Conversion d'un montant en dinars/centimes en toutes lettres,
 * pour la mention légale des papiers officiels algériens
 * (« Arrêtée la présente facture à la somme de… » / «حُدد مبلغ هذه الفاتورة بمبلغ…»).
 */
class MontantEnLettres
{
    private const UNITES_FR = ['zéro', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf',
        'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf'];

    private const DIZAINES_FR = ['', '', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante', 'quatre-vingt'];

    private const UNITES_AR = ['صفر', 'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة',
        'عشرة', 'أحد عشر', 'اثنا عشر', 'ثلاثة عشر', 'أربعة عشر', 'خمسة عشر', 'ستة عشر', 'سبعة عشر', 'ثمانية عشر', 'تسعة عشر'];

    private const DIZAINES_AR = ['', '', 'عشرون', 'ثلاثون', 'أربعون', 'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون'];

    private const CENTAINES_AR = ['', 'مئة', 'مئتان', 'ثلاثمئة', 'أربعمئة', 'خمسمئة', 'ستمئة', 'سبعمئة', 'ثمانمئة', 'تسعمئة'];

    public function enLettres(float $montant, ?string $locale = null): string
    {
        return match ($locale ?? app()->getLocale()) {
            'ar' => $this->enArabe($montant),
            default => $this->enFrancais($montant),
        };
    }

    /**
     * « deux cent cinquante mille dinars algériens et cinquante centimes ».
     */
    public function enFrancais(float $montant): string
    {
        [$dinars, $centimes] = $this->separer($montant);

        $enLettres = $this->entierFr($dinars);

        $grandNombre = str_ends_with($enLettres, 'million')
            || str_ends_with($enLettres, 'millions')
            || str_ends_with($enLettres, 'milliard')
            || str_ends_with($enLettres, 'milliards');

        $dinarsEnLettres = match (true) {
            $dinars === 1 => 'un dinar algérien',
            $grandNombre => $enLettres.' de dinars algériens',
            default => $enLettres.' '.($dinars === 0 ? 'dinar algérien' : 'dinars algériens'),
        };

        return $dinarsEnLettres.' et '.$this->entierFr($centimes).' '.($centimes === 1 ? 'centime' : 'centimes');
    }

    /**
     * « مئتان وخمسون ألف دينار جزائري وخمسون سنتيمًا ».
     */
    public function enArabe(float $montant): string
    {
        [$dinars, $centimes] = $this->separer($montant);

        $dinarsEnLettres = match (true) {
            $dinars === 1 => 'دينار جزائري واحد',
            $dinars === 2 => 'ديناران جزائريان',
            $dinars >= 3 && $dinars <= 10 => $this->entierAr($dinars).' دنانير جزائرية',
            $dinars === 0 => 'صفر دينار جزائري',
            default => $this->entierAr($dinars).' دينارًا جزائريًا',
        };

        $centimesEnLettres = match (true) {
            $centimes === 1 => 'سنتيم واحد',
            $centimes === 2 => 'سنتيمان',
            $centimes >= 3 && $centimes <= 10 => $this->entierAr($centimes).' سنتيمات',
            default => $this->entierAr($centimes).' سنتيمًا',
        };

        return $dinarsEnLettres.' و'.$centimesEnLettres;
    }

    /**
     * @return array{0: int, 1: int} [dinars entiers, centimes]
     */
    private function separer(float $montant): array
    {
        $totalCentimes = (int) round(abs($montant) * 100);

        return [intdiv($totalCentimes, 100), $totalCentimes % 100];
    }

    private function entierFr(int $nombre): string
    {
        if ($nombre === 0) {
            return 'zéro';
        }

        $tranches = [
            1000000000 => 'milliard',
            1000000 => 'million',
            1000 => 'mille',
        ];

        $parts = [];

        foreach ($tranches as $diviseur => $libelle) {
            $combien = intdiv($nombre, $diviseur);

            if ($combien > 0) {
                if ($diviseur === 1000 && $combien === 1) {
                    $parts[] = 'mille';
                } else {
                    $pluriel = ($combien > 1 && $diviseur !== 1000) ? 's' : '';
                    $segment = $diviseur === 1000
                        ? $this->segmentFr($combien, centInvariable: true)
                        : $this->segmentFr($combien);
                    $parts[] = $segment.' '.$libelle.$pluriel;
                }

                $nombre %= $diviseur;
            }
        }

        if ($nombre > 0) {
            $parts[] = $this->segmentFr($nombre);
        }

        return implode(' ', $parts);
    }

    /**
     * Segments de 0 à 999 (traits d'union, « et », « cents », soixante-dix / quatre-vingt-dix).
     * « cent » reste invariable devant « mille » (cinq cent mille).
     */
    private function segmentFr(int $nombre, bool $centInvariable = false): string
    {
        if ($nombre < 20) {
            return self::UNITES_FR[$nombre];
        }

        if ($nombre < 100) {
            $dizaine = intdiv($nombre, 10);
            $unite = $nombre % 10;

            if ($nombre >= 70 && $nombre < 80) {
                return $unite === 11 ? 'soixante-et-onze' : 'soixante-'.$this->segmentFr(10 + $unite);
            }

            if ($nombre >= 80) {
                return $nombre === 80 ? 'quatre-vingts' : 'quatre-vingt-'.$this->segmentFr($nombre - 80);
            }

            $mot = self::DIZAINES_FR[$dizaine];

            return match ($unite) {
                0 => $mot,
                1 => $mot.' et un',
                default => $mot.'-'.$this->segmentFr($unite),
            };
        }

        $centaines = intdiv($nombre, 100);
        $reste = $nombre % 100;

        if ($centaines === 1) {
            return $reste === 0 ? 'cent' : 'cent '.$this->segmentFr($reste);
        }

        $mot = $this->segmentFr($centaines).' cent'.($reste === 0 && ! $centInvariable ? 's' : '');

        return $reste === 0 ? $mot : $mot.' '.$this->segmentFr($reste);
    }

    private function entierAr(int $nombre): string
    {
        if ($nombre === 0) {
            return 'صفر';
        }

        $parts = [];

        $millions = intdiv($nombre, 1000000);
        $nombre %= 1000000;

        if ($millions > 0) {
            $parts[] = $this->groupeAr($millions, 'مليون', 'مليونان', 'ملايين');
        }

        $milliers = intdiv($nombre, 1000);
        $nombre %= 1000;

        if ($milliers > 0) {
            $parts[] = $this->groupeAr($milliers, 'ألف', 'ألفان', 'آلاف');
        }

        if ($nombre > 0) {
            $parts[] = $this->segmentAr($nombre);
        }

        return implode(' و', $parts);
    }

    /**
     * Groupe (milliers / millions) : 1 → « ألف », 2 → « ألفان », 3-10 → « ثلاثة آلاف », 11+ → « عشرون ألف ».
     */
    private function groupeAr(int $valeur, string $singulier, string $duel, string $pluriel): string
    {
        return match (true) {
            $valeur === 1 => $singulier,
            $valeur === 2 => $duel,
            $valeur <= 10 => $this->segmentAr($valeur).' '.$pluriel,
            default => $this->segmentAr($valeur).' '.$singulier,
        };
    }

    /**
     * Segments de 0 à 999 (unité avant la dizaine : « خمسة وعشرون »).
     */
    private function segmentAr(int $nombre): string
    {
        if ($nombre < 20) {
            return self::UNITES_AR[$nombre];
        }

        $centaine = intdiv($nombre, 100);
        $reste = $nombre % 100;

        $parts = [];

        if ($centaine > 0) {
            $parts[] = self::CENTAINES_AR[$centaine];
        }

        if ($reste > 0) {
            if ($reste < 20) {
                $parts[] = self::UNITES_AR[$reste];
            } else {
                $unite = $reste % 10;
                $dizaine = intdiv($reste, 10);

                $parts[] = $unite === 0
                    ? self::DIZAINES_AR[$dizaine]
                    : self::UNITES_AR[$unite].' و'.self::DIZAINES_AR[$dizaine];
            }
        }

        return implode(' و', $parts);
    }
}
