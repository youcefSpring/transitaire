<?php

use App\Http\Controllers\AlerteController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ConteneurController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DedouanementController;
use App\Http\Controllers\DocumentCommercialController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DossierController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\FraisController;
use App\Http\Controllers\LivraisonController;
use App\Http\Controllers\MarchandiseController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'formulaire'])->name('login');
Route::post('/login', [LoginController::class, 'connecter'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'deconnecter'])->name('logout');

Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['ar', 'fr'], true)) {
        session(['locale' => $locale]);
    }

    return back();
})->name('locale.switch');

Route::get('/', fn () => view('landing'))->name('landing');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
    Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
    Route::post('/clients/{client}/documents', [DocumentController::class, 'storeClient'])->name('clients.documents');

    Route::get('/dossiers', [DossierController::class, 'index'])->name('dossiers.index');
    Route::get('/dossiers/create', [DossierController::class, 'create'])->name('dossiers.create');
    Route::post('/dossiers', [DossierController::class, 'store'])->name('dossiers.store');
    Route::get('/dossiers/{numero}', [DossierController::class, 'show'])->name('dossiers.show');
    Route::get('/dossiers/{numero}/edit', [DossierController::class, 'edit'])->name('dossiers.edit');
    Route::put('/dossiers/{numero}', [DossierController::class, 'update'])->name('dossiers.update');
    Route::patch('/dossiers/{numero}/statut', [DossierController::class, 'statut'])->name('dossiers.statut');
    Route::patch('/dossiers/{numero}/blocage', [DossierController::class, 'blocage'])->name('dossiers.blocage');
    Route::delete('/dossiers/{numero}', [DossierController::class, 'destroy'])->name('dossiers.destroy');

    Route::post('/dossiers/{numero}/marchandises', [MarchandiseController::class, 'store'])->name('dossiers.marchandises');
    Route::put('/dossiers/{numero}/marchandises/{marchandise}', [MarchandiseController::class, 'update'])->name('marchandises.update');
    Route::delete('/dossiers/{numero}/marchandises/{marchandise}', [MarchandiseController::class, 'destroy'])->name('marchandises.destroy');

    Route::post('/dossiers/{numero}/documents', [DocumentController::class, 'storeDossier'])->name('dossiers.documents');
    Route::post('/dossiers/{numero}/douane', [DedouanementController::class, 'store'])->name('dossiers.douane');
    Route::post('/dossiers/{numero}/frais', [FraisController::class, 'store'])->name('dossiers.frais');
    Route::delete('/frais/{frai}', [FraisController::class, 'destroy'])->name('frais.destroy');

    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    Route::get('/conteneurs', [ConteneurController::class, 'index'])->name('conteneurs.index');
    Route::post('/conteneurs', [ConteneurController::class, 'store'])->name('conteneurs.store');
    Route::put('/conteneurs/{conteneur}', [ConteneurController::class, 'update'])->name('conteneurs.update');
    Route::delete('/conteneurs/{conteneur}', [ConteneurController::class, 'destroy'])->name('conteneurs.destroy');

    Route::get('/documents-commerciaux', [DocumentCommercialController::class, 'index'])->name('documents-commerciaux.index');
    Route::get('/documents-commerciaux/create', [DocumentCommercialController::class, 'create'])->name('documents-commerciaux.create');
    Route::post('/documents-commerciaux', [DocumentCommercialController::class, 'store'])->name('documents-commerciaux.store');
    Route::get('/documents-commerciaux/{documentCommercial}', [DocumentCommercialController::class, 'show'])->name('documents-commerciaux.show');
    Route::patch('/documents-commerciaux/{documentCommercial}/statut', [DocumentCommercialController::class, 'statut'])->name('documents-commerciaux.statut');
    Route::delete('/documents-commerciaux/{documentCommercial}', [DocumentCommercialController::class, 'destroy'])->name('documents-commerciaux.destroy');

    Route::get('/paiements', [PaiementController::class, 'index'])->name('paiements.index');
    Route::post('/paiements', [PaiementController::class, 'store'])->name('paiements.store');

    Route::get('/fournisseurs', [FournisseurController::class, 'index'])->name('fournisseurs.index');
    Route::get('/fournisseurs/create', [FournisseurController::class, 'create'])->name('fournisseurs.create');
    Route::post('/fournisseurs', [FournisseurController::class, 'store'])->name('fournisseurs.store');
    Route::get('/fournisseurs/{fournisseur}', [FournisseurController::class, 'show'])->name('fournisseurs.show');
    Route::get('/fournisseurs/{fournisseur}/edit', [FournisseurController::class, 'edit'])->name('fournisseurs.edit');
    Route::put('/fournisseurs/{fournisseur}', [FournisseurController::class, 'update'])->name('fournisseurs.update');
    Route::delete('/fournisseurs/{fournisseur}', [FournisseurController::class, 'destroy'])->name('fournisseurs.destroy');

    Route::get('/transport/camions', [LivraisonController::class, 'camionsIndex'])->name('camions.index');
    Route::post('/transport/camions', [LivraisonController::class, 'camionsStore'])->name('camions.store');
    Route::delete('/transport/camions/{camion}', [LivraisonController::class, 'camionsDestroy'])->name('camions.destroy');
    Route::get('/transport/chauffeurs', [LivraisonController::class, 'chauffeursIndex'])->name('chauffeurs.index');
    Route::post('/transport/chauffeurs', [LivraisonController::class, 'chauffeursStore'])->name('chauffeurs.store');
    Route::delete('/transport/chauffeurs/{chauffeur}', [LivraisonController::class, 'chauffeursDestroy'])->name('chauffeurs.destroy');
    Route::get('/livraisons', [LivraisonController::class, 'index'])->name('livraisons.index');
    Route::post('/livraisons', [LivraisonController::class, 'store'])->name('livraisons.store');
    Route::patch('/livraisons/{livraison}/statut', [LivraisonController::class, 'statut'])->name('livraisons.statut');
    Route::delete('/livraisons/{livraison}', [LivraisonController::class, 'destroy'])->name('livraisons.destroy');

    Route::get('/alertes', [AlerteController::class, 'index'])->name('alertes.index');
    Route::patch('/alertes/{alerte}', [AlerteController::class, 'update'])->name('alertes.update');

    Route::get('/rapports', [RapportController::class, 'index'])->name('rapports.index');
    Route::get('/rapports/{type}', [RapportController::class, 'show'])->name('rapports.show');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
});
