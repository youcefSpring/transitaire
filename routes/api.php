<?php

use App\Http\Controllers\Api\V1\AlerteController;
use App\Http\Controllers\Api\V1\AuditController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CamionController;
use App\Http\Controllers\Api\V1\ChauffeurController;
use App\Http\Controllers\Api\V1\ClientController;
use App\Http\Controllers\Api\V1\ConteneurController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\DedouanementController;
use App\Http\Controllers\Api\V1\DocumentCommercialController;
use App\Http\Controllers\Api\V1\DocumentController;
use App\Http\Controllers\Api\V1\DossierController;
use App\Http\Controllers\Api\V1\FournisseurController;
use App\Http\Controllers\Api\V1\FraisController;
use App\Http\Controllers\Api\V1\LivraisonController;
use App\Http\Controllers\Api\V1\MarchandiseController;
use App\Http\Controllers\Api\V1\PaiementController;
use App\Http\Controllers\Api\V1\RapportController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function (): void {
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        Route::apiResource('clients', ClientController::class)->only(['index', 'show']);
        Route::get('/clients/{client}/dossiers', [ClientController::class, 'dossiers']);
        Route::get('/clients/{client}/factures', [ClientController::class, 'factures']);
        Route::get('/clients/{client}/paiements', [ClientController::class, 'paiements']);
        Route::get('/clients/{client}/solde', [ClientController::class, 'solde']);
        Route::apiResource('clients', ClientController::class)->only(['store', 'update'])->middleware('can:clients.gerer');
        Route::apiResource('clients', ClientController::class)->only(['destroy'])->middleware('can:clients.supprimer');

        Route::get('/dossiers', [DossierController::class, 'index']);
        Route::post('/dossiers', [DossierController::class, 'store'])->middleware('can:dossiers.gerer');
        Route::get('/dossiers/{numero}', [DossierController::class, 'show']);
        Route::put('/dossiers/{numero}', [DossierController::class, 'update'])->middleware('can:dossiers.gerer');
        Route::patch('/dossiers/{numero}/statut', [DossierController::class, 'statut'])->middleware('can:dossiers.gerer');
        Route::patch('/dossiers/{numero}/blocage', [DossierController::class, 'blocage'])->middleware('can:dossiers.gerer');
        Route::delete('/dossiers/{numero}', [DossierController::class, 'destroy'])->middleware('can:dossiers.supprimer');
        Route::get('/dossiers/{numero}/marge', [DossierController::class, 'marge']);

        Route::get('/dossiers/{numero}/marchandises', [MarchandiseController::class, 'index']);
        Route::post('/dossiers/{numero}/marchandises', [MarchandiseController::class, 'store'])->middleware('can:dossiers.gerer');
        Route::put('/dossiers/{numero}/marchandises/{marchandise}', [MarchandiseController::class, 'update'])->middleware('can:dossiers.gerer');
        Route::delete('/dossiers/{numero}/marchandises/{marchandise}', [MarchandiseController::class, 'destroy'])->middleware('can:dossiers.gerer');

        Route::get('/dossiers/{numero}/documents', [DocumentController::class, 'indexDossier']);
        Route::post('/dossiers/{numero}/documents', [DocumentController::class, 'storeDossier'])->middleware('can:dossiers.gerer');
        Route::post('/clients/{client}/documents', [DocumentController::class, 'storeClient'])->middleware('can:clients.gerer');
        Route::get('/documents/{document}/download', [DocumentController::class, 'download']);
        Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->middleware('can:dossiers.gerer');

        Route::get('/dossiers/{numero}/douane', [DedouanementController::class, 'index']);
        Route::post('/dossiers/{numero}/douane', [DedouanementController::class, 'store'])->middleware('can:dossiers.gerer');

        Route::get('/dossiers/{numero}/frais', [FraisController::class, 'index']);
        Route::post('/dossiers/{numero}/frais', [FraisController::class, 'store'])->middleware('can:frais.gerer');
        Route::delete('/frais/{frai}', [FraisController::class, 'destroy'])->middleware('can:frais.gerer');

        Route::apiResource('conteneurs', ConteneurController::class)->only(['index', 'show']);
        Route::apiResource('conteneurs', ConteneurController::class)->only(['store', 'update', 'destroy'])->middleware('can:dossiers.gerer');
        Route::patch('/conteneurs/{conteneur}/statut', [ConteneurController::class, 'statut'])->middleware('can:dossiers.gerer');

        Route::apiResource('documents-commerciaux', DocumentCommercialController::class)
            ->only(['index', 'show']);
        Route::apiResource('documents-commerciaux', DocumentCommercialController::class)
            ->only(['store'])->middleware('can:documents-commerciaux.gerer');
        Route::apiResource('documents-commerciaux', DocumentCommercialController::class)
            ->only(['destroy'])->middleware('can:documents-commerciaux.supprimer');
        Route::patch('/documents-commerciaux/{documentCommercial}/statut', [DocumentCommercialController::class, 'statut'])->middleware('can:documents-commerciaux.gerer');

        Route::get('/paiements', [PaiementController::class, 'index']);
        Route::post('/paiements', [PaiementController::class, 'store'])->middleware('can:paiements.gerer');

        Route::apiResource('fournisseurs', FournisseurController::class)->only(['index', 'show']);
        Route::get('/fournisseurs/{fournisseur}/operations', [FournisseurController::class, 'operations']);
        Route::get('/fournisseurs/{fournisseur}/paiements', [FournisseurController::class, 'paiements']);
        Route::apiResource('fournisseurs', FournisseurController::class)->only(['store', 'update', 'destroy'])->middleware('can:fournisseurs.gerer');

        Route::apiResource('camions', CamionController::class)->only(['index', 'show']);
        Route::apiResource('camions', CamionController::class)->only(['store', 'update', 'destroy'])->middleware('can:transport.gerer');
        Route::apiResource('chauffeurs', ChauffeurController::class)->only(['index', 'show']);
        Route::apiResource('chauffeurs', ChauffeurController::class)->only(['store', 'update', 'destroy'])->middleware('can:transport.gerer');
        Route::apiResource('livraisons', LivraisonController::class)->only(['index', 'show']);
        Route::apiResource('livraisons', LivraisonController::class)->only(['store', 'destroy'])->middleware('can:transport.gerer');
        Route::patch('/livraisons/{livraison}/statut', [LivraisonController::class, 'statut'])->middleware('can:transport.gerer');

        Route::get('/alertes', [AlerteController::class, 'index']);
        Route::patch('/alertes/{alerte}', [AlerteController::class, 'update']);

        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/rapports/{type}', [RapportController::class, 'show']);

        Route::apiResource('users', UserController::class)->middleware('can:users.gerer');
        Route::get('/audit', [AuditController::class, 'index'])->middleware('can:audit.consulter');
    });
});
