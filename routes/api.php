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

Route::prefix('v1')->group(function (): void {
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        Route::apiResource('clients', ClientController::class);
        Route::get('/clients/{client}/dossiers', [ClientController::class, 'dossiers']);
        Route::get('/clients/{client}/factures', [ClientController::class, 'factures']);
        Route::get('/clients/{client}/paiements', [ClientController::class, 'paiements']);
        Route::get('/clients/{client}/solde', [ClientController::class, 'solde']);

        Route::get('/dossiers', [DossierController::class, 'index']);
        Route::post('/dossiers', [DossierController::class, 'store']);
        Route::get('/dossiers/{numero}', [DossierController::class, 'show']);
        Route::put('/dossiers/{numero}', [DossierController::class, 'update']);
        Route::patch('/dossiers/{numero}/statut', [DossierController::class, 'statut']);
        Route::patch('/dossiers/{numero}/blocage', [DossierController::class, 'blocage']);
        Route::delete('/dossiers/{numero}', [DossierController::class, 'destroy']);
        Route::get('/dossiers/{numero}/marge', [DossierController::class, 'marge']);

        Route::get('/dossiers/{numero}/marchandises', [MarchandiseController::class, 'index']);
        Route::post('/dossiers/{numero}/marchandises', [MarchandiseController::class, 'store']);
        Route::put('/dossiers/{numero}/marchandises/{marchandise}', [MarchandiseController::class, 'update']);
        Route::delete('/dossiers/{numero}/marchandises/{marchandise}', [MarchandiseController::class, 'destroy']);

        Route::get('/dossiers/{numero}/documents', [DocumentController::class, 'indexDossier']);
        Route::post('/dossiers/{numero}/documents', [DocumentController::class, 'storeDossier']);
        Route::post('/clients/{client}/documents', [DocumentController::class, 'storeClient']);
        Route::get('/documents/{document}/download', [DocumentController::class, 'download']);
        Route::delete('/documents/{document}', [DocumentController::class, 'destroy']);

        Route::get('/dossiers/{numero}/douane', [DedouanementController::class, 'index']);
        Route::post('/dossiers/{numero}/douane', [DedouanementController::class, 'store']);

        Route::get('/dossiers/{numero}/frais', [FraisController::class, 'index']);
        Route::post('/dossiers/{numero}/frais', [FraisController::class, 'store']);
        Route::delete('/frais/{frai}', [FraisController::class, 'destroy']);

        Route::apiResource('conteneurs', ConteneurController::class);
        Route::patch('/conteneurs/{conteneur}/statut', [ConteneurController::class, 'statut']);

        Route::apiResource('documents-commerciaux', DocumentCommercialController::class)
            ->only(['index', 'show', 'store', 'destroy']);
        Route::patch('/documents-commerciaux/{documentCommercial}/statut', [DocumentCommercialController::class, 'statut']);

        Route::get('/paiements', [PaiementController::class, 'index']);
        Route::post('/paiements', [PaiementController::class, 'store']);

        Route::apiResource('fournisseurs', FournisseurController::class);
        Route::get('/fournisseurs/{fournisseur}/operations', [FournisseurController::class, 'operations']);
        Route::get('/fournisseurs/{fournisseur}/paiements', [FournisseurController::class, 'paiements']);

        Route::apiResource('camions', CamionController::class);
        Route::apiResource('chauffeurs', ChauffeurController::class);
        Route::apiResource('livraisons', LivraisonController::class)->only(['index', 'store', 'show', 'destroy']);
        Route::patch('/livraisons/{livraison}/statut', [LivraisonController::class, 'statut']);

        Route::get('/alertes', [AlerteController::class, 'index']);
        Route::patch('/alertes/{alerte}', [AlerteController::class, 'update']);

        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/rapports/{type}', [RapportController::class, 'show']);

        Route::apiResource('users', UserController::class);
        Route::get('/audit', [AuditController::class, 'index']);
    });
});
