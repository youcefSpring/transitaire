<?php

namespace Tests\Feature;

use App\Enums\UserProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TraductionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_premiere_visite_affiche_l_arabe_sans_cles_brutes(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertSee('lang="ar"', false)
            ->assertSee('كل نشاط الترانزيت في منصة واحدة', false);

        $this->assertDoesNotMatchRegularExpression(
            '/app\.[a-z_]+\.[a-z_]+/',
            strip_tags($response->getContent()),
            'Des clés de traduction brutes apparaissent dans la page.',
        );
    }

    public function test_les_erreurs_de_validation_sont_traduites_en_arabe_des_la_premiere_requete(): void
    {
        $response = $this->from('/login')->post('/login', []);

        $page = $this->followRedirects($response);

        $page->assertSee('حقل البريد الإلكتروني مطلوب.', false)
            ->assertSee('حقل كلمة المرور مطلوب.', false);

        $this->assertStringNotContainsString(
            'validation.',
            strip_tags($page->getContent()),
            'Des messages de validation non traduits apparaissent.',
        );
    }

    public function test_message_d_identifiants_invalides_est_traduit(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'inconnu@transitaire.dz',
            'password' => 'mauvais',
        ]);

        $page = $this->followRedirects($response);

        $page->assertSee('بيانات الدخول غير صالحة أو الحساب معطّل.', false);
    }

    public function test_bascule_vers_le_francais_puis_retour_a_l_arabe(): void
    {
        $this->get('/locale/fr');
        $fr = $this->get('/');

        $fr->assertSee('centralisée dans une seule plateforme', false)
            ->assertSee('lang="fr"', false);

        $this->get('/locale/ar');
        $ar = $this->get('/');

        $ar->assertSee('كل نشاط الترانزيت في منصة واحدة', false)
            ->assertSee('lang="ar"', false);
    }

    public function test_les_messages_flash_suivent_la_langue_active(): void
    {
        $admin = User::factory()->profile(UserProfile::Administrateur)->create();

        $this->actingAs($admin);

        $arabe = $this->from('/transport/camions')->post('/transport/camions', ['immatriculation' => '16-111-222']);
        $this->followRedirects($arabe)->assertSee('تم تسجيل الشاحنة.', false);

        $this->get('/locale/fr');

        $francais = $this->from('/transport/camions')->post('/transport/camions', ['immatriculation' => '16-333-444']);
        $this->followRedirects($francais)->assertSee('Camion enregistré.', false);
    }
}
