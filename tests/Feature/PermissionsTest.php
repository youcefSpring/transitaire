<?php

namespace Tests\Feature;

use App\Enums\Devise;
use App\Enums\DocumentCommercialType;
use App\Enums\UserProfile;
use App\Models\Client;
use App\Models\DocumentCommercial;
use App\Models\Dossier;
use App\Models\User;
use App\Services\AlerteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->profile(UserProfile::Administrateur)->create();
    }

    public function test_un_compte_consultation_ne_peut_rien_modifier(): void
    {
        $consultation = User::factory()->profile(UserProfile::Consultation)->create();

        $this->actingAs($consultation);

        $this->get('/dashboard')->assertOk();
        $this->get('/dossiers')->assertOk();
        $this->get('/documents-commerciaux')->assertOk();

        $this->post('/transport/camions', ['immatriculation' => '16-AAA-111'])->assertForbidden();
        $this->get('/users')->assertForbidden();
        $this->get('/audit')->assertForbidden();
    }

    public function test_les_pages_utilisateurs_et_audit_sont_reservees(): void
    {
        $comptable = User::factory()->profile(UserProfile::Comptable)->create();
        $directeur = User::factory()->profile(UserProfile::Directeur)->create();

        $this->actingAs($comptable)->get('/users')->assertForbidden();
        $this->actingAs($comptable)->get('/audit')->assertForbidden();

        $this->actingAs($directeur)->get('/users')->assertForbidden();
        $this->actingAs($directeur)->get('/audit')->assertOk();

        $this->actingAs($this->admin)->get('/users')->assertOk();
        $this->actingAs($this->admin)->get('/audit')->assertOk();
    }

    public function test_chaque_profil_gere_son_domaine_exclusivement(): void
    {
        $transport = User::factory()->profile(UserProfile::ResponsableTransport)->create();
        $comptable = User::factory()->profile(UserProfile::Comptable)->create();
        $commercial = User::factory()->profile(UserProfile::AgentCommercial)->create();

        $this->actingAs($transport)->post('/transport/camions', ['immatriculation' => '16-TRT-001'])->assertRedirect();
        $this->actingAs($comptable)->post('/transport/camions', ['immatriculation' => '16-CPT-002'])->assertForbidden();

        $this->actingAs($commercial)->post('/clients', $this->donneesClient('SARL Com Test'))->assertRedirect();
        $this->actingAs($transport)->post('/clients', $this->donneesClient('SARL Trp Test'))->assertForbidden();
    }

    public function test_un_agent_de_transit_ne_supprime_pas_une_facture(): void
    {
        $agentTransit = User::factory()->profile(UserProfile::AgentTransit)->create();
        $document = $this->facture();

        $this->actingAs($agentTransit)
            ->delete("/documents-commerciaux/{$document->id}")
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete("/documents-commerciaux/{$document->id}")
            ->assertRedirect();
    }

    public function test_la_barre_de_navigation_masque_les_entrees_interdites(): void
    {
        $consultation = User::factory()->profile(UserProfile::Consultation)->create();

        $page = $this->actingAs($consultation)->get('/dashboard');

        $page->assertOk()
            ->assertDontSee(route('users.index'))
            ->assertDontSee(route('audit.index'));

        $this->actingAs($this->admin)->get('/dashboard')->assertSee(route('users.index'));
    }

    public function test_chaque_action_modificatrice_est_journalisee(): void
    {
        $transport = User::factory()->profile(UserProfile::ResponsableTransport)->create();

        $this->actingAs($transport)->post('/transport/camions', ['immatriculation' => '16-AUD-001']);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $transport->id,
            'action' => 'Enregistrement du camion 16-AUD-001',
            'entite_type' => 'camion',
        ]);
    }

    public function test_les_alertes_clients_mettent_une_notification_en_file(): void
    {
        $document = $this->facture();
        $document->update(['date_echeance' => now()->subWeek()]);

        $service = app(AlerteService::class);
        $service->factureImpayee();
        $service->factureImpayee();

        $this->assertDatabaseHas('notifications', [
            'client_id' => $document->client_id,
            'destinataire' => $document->client->email,
            'statut' => 'en_file',
        ]);

        $this->assertDatabaseCount('notifications', 1);
    }

    /**
     * @return array<string, mixed>
     */
    private function donneesClient(string $raisonSociale): array
    {
        return [
            'raison_sociale' => $raisonSociale,
            'nif' => '000225009999001',
            'nis' => '000225009999002',
            'rc' => '16/00-9999999B16',
            'adresse' => 'Alger',
            'telephone' => '+213 21 00 00 00',
            'email' => 'contact@test.dz',
            'conditions_paiement' => '30 jours',
        ];
    }

    private function facture(): DocumentCommercial
    {
        $client = Client::create([
            'raison_sociale' => 'SPA Notif Test',
            'nif' => '000225009999101',
            'nis' => '000225009999102',
            'rc' => '16/00-8888888B16',
            'adresse' => 'Oran',
            'telephone' => '+213 41 00 00 00',
            'email' => 'notif@test.dz',
            'conditions_paiement' => '30 jours',
            'created_by' => $this->admin->id,
        ]);

        $dossier = Dossier::create([
            'numero' => 'TR-2026-0999',
            'client_id' => $client->id,
            'type' => 'import',
            'mode_transport' => 'maritime',
            'port_aeroport' => 'Port d\'Alger',
            'fournisseur_destinataire' => 'Shipping Line SARL',
            'date_arrivee_prevue' => now()->addWeek()->toDateString(),
            'numero_bl_awb' => 'MSCU9990001',
            'nombre_colis' => 10,
            'poids' => 1000,
            'volume' => 2,
            'nature_marchandise' => 'Textiles',
            'valeur_declaree' => 50000,
            'devise' => 'DZD',
            'incoterm' => 'FOB',
            'created_by' => $this->admin->id,
        ]);

        return DocumentCommercial::create([
            'type' => DocumentCommercialType::Facture,
            'numero' => 'FA-2026-0999',
            'client_id' => $client->id,
            'dossier_id' => $dossier->id,
            'devise' => Devise::DZD,
            'total_prestations' => 50000,
            'montant_total' => 50000,
            'statut' => 'emis',
            'date_emission' => now()->subMonth()->toDateString(),
            'date_echeance' => now()->subWeek()->toDateString(),
            'created_by' => $this->admin->id,
        ]);
    }
}
