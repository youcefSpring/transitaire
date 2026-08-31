<?php

namespace Tests\Unit;

use App\Services\MontantEnLettres;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MontantEnLettresTest extends TestCase
{
    private MontantEnLettres $montants;

    protected function setUp(): void
    {
        parent::setUp();

        $this->montants = new MontantEnLettres;
    }

    #[DataProvider('montantsFrancais')]
    public function test_en_francais(float $montant, string $attendu): void
    {
        $this->assertSame($attendu, $this->montants->enFrancais($montant));
    }

    public static function montantsFrancais(): array
    {
        return [
            'zéro' => [0.0, 'zéro dinar algérien et zéro centimes'],
            'un dinar' => [1.0, 'un dinar algérien et zéro centimes'],
            'quatre-vingt-dix' => [90.0, 'quatre-vingt-dix dinars algériens et zéro centimes'],
            'quatre-vingt-treize' => [93.0, 'quatre-vingt-treize dinars algériens et zéro centimes'],
            'avec centimes' => [1234.56, 'mille deux cent trente-quatre dinars algériens et cinquante-six centimes'],
            'cents invariables devant mille' => [500000.0, 'cinq cent mille dinars algériens et zéro centimes'],
            'million avec de' => [1000000.0, 'un million de dinars algériens et zéro centimes'],
            'montant type facture' => [250000.50, 'deux cent cinquante mille dinars algériens et cinquante centimes'],
        ];
    }

    public function test_en_arabe(): void
    {
        $this->assertSame(
            'مئتان وخمسون ألف دينارًا جزائريًا وخمسون سنتيمًا',
            $this->montants->enArabe(250000.50),
        );

        $this->assertSame(
            'ديناران جزائريان وسنتيمان',
            $this->montants->enArabe(2.02),
        );

        $this->assertSame(
            'صفر دينار جزائري وصفر سنتيمًا',
            $this->montants->enArabe(0),
        );
    }
}
