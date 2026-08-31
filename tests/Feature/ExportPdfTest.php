<?php

namespace Tests\Feature;

use App\Enums\Devise;
use App\Enums\DocumentCommercialType;
use App\Enums\UserProfile;
use App\Models\Client;
use App\Models\DocumentCommercial;
use App\Models\Dossier;
use App\Models\ExchangeRate;
use App\Models\LignePrestation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class ExportPdfTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->profile(UserProfile::Administrateur)->create();
        $this->actingAs($this->admin);
    }

    public function test_pdf_document_commercial_en_francais(): void
    {
        $document = $this->documentCommercial();

        $this->get('/locale/fr');

        $response = $this->get("/documents-commerciaux/{$document->id}/pdf");

        $this->assertPdf($response, 'FA-2026-0001.pdf');
    }

    public function test_pdf_document_commercial_en_arabe(): void
    {
        $document = $this->documentCommercial();

        $response = $this->get("/documents-commerciaux/{$document->id}/pdf");

        $this->assertPdf($response, 'FA-2026-0001.pdf');
    }

    public function test_pdf_document_commercial_en_euros_avec_contrevaleur(): void
    {
        $document = $this->documentCommercial(Devise::EUR);

        ExchangeRate::create([
            'devise' => Devise::EUR,
            'taux_dzd' => 152.65,
            'date_taux' => now()->yesterday()->toDateString(),
            'created_by' => $this->admin->id,
        ]);

        $response = $this->get("/documents-commerciaux/{$document->id}/pdf");

        $this->assertPdf($response, 'FA-2026-0001.pdf');
    }

    public function test_pdf_rapport(): void
    {
        $response = $this->get('/rapports/impayes/pdf');

        $this->assertPdf($response);
    }

    public function test_pdf_rapport_inconnu_renvoie_404(): void
    {
        $this->get('/rapports/inconnu/pdf')->assertNotFound();
    }

    public function test_pdf_synthese_dossier(): void
    {
        $dossier = $this->dossier();

        $response = $this->get("/dossiers/{$dossier->numero}/pdf");

        $this->assertPdf($response, 'TR-2026-0001.pdf');
    }

    public function test_les_exports_pdf_exigent_une_connexion(): void
    {
        auth()->logout();

        $this->get('/rapports/impayes/pdf')->assertRedirect(route('login'));
    }

    private function dossier(): Dossier
    {
        $client = Client::create([
            'raison_sociale' => 'SPA Test Import Algérie',
            'nif' => '000225003456789',
            'nis' => '000225003456790',
            'rc' => '16/00-1234567B16',
            'adresse' => 'Zone industrielle, Alger',
            'telephone' => '+213 21 00 00 00',
            'email' => 'contact@test-import.dz',
            'conditions_paiement' => '30 jours fin de mois',
            'created_by' => $this->admin->id,
        ]);

        return Dossier::create([
            'numero' => 'TR-2026-0001',
            'client_id' => $client->id,
            'type' => 'import',
            'mode_transport' => 'maritime',
            'port_aeroport' => 'Port d\'Alger',
            'fournisseur_destinataire' => 'Shipping Line SARL',
            'date_arrivee_prevue' => now()->addWeek()->toDateString(),
            'numero_bl_awb' => 'MSCU1234567',
            'nombre_colis' => 120,
            'poids' => 18500.5,
            'volume' => 32.4,
            'nature_marchandise' => 'Pièces détachées',
            'valeur_declaree' => 250000,
            'devise' => 'DZD',
            'incoterm' => 'FOB',
            'created_by' => $this->admin->id,
        ]);
    }

    private function documentCommercial(Devise $devise = Devise::DZD): DocumentCommercial
    {
        $dossier = $this->dossier();

        $document = DocumentCommercial::create([
            'type' => DocumentCommercialType::Facture,
            'numero' => 'FA-2026-0001',
            'client_id' => $dossier->client_id,
            'dossier_id' => $dossier->id,
            'devise' => $devise,
            'total_prestations' => 192000,
            'total_frais' => 0,
            'total_taxes' => 0,
            'remise' => 0,
            'montant_total' => 192000,
            'statut' => 'emis',
            'date_emission' => now()->toDateString(),
            'created_by' => $this->admin->id,
        ]);

        LignePrestation::create([
            'document_id' => $document->id,
            'designation' => 'Transit et dédouanement',
            'categorie' => 'transit',
            'quantite' => 1,
            'prix_unitaire' => 192000,
            'montant' => 192000,
        ]);

        return $document;
    }

    /**
     * @param  TestResponse  $response
     */
    private function assertPdf($response, ?string $nomFichier = null): void
    {
        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');

        if ($nomFichier !== null) {
            $response->assertHeader('Content-Disposition', "attachment; filename={$nomFichier}");
        }

        $this->assertStringStartsWith(
            '%PDF',
            $response->getContent(),
            'La réponse n\'est pas un document PDF valide.',
        );
    }
}
