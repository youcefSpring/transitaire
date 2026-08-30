<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\DocumentCategorie;
use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentUploadRequest;
use App\Models\Client;
use App\Models\Document;
use App\Models\Dossier;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function indexDossier(string $numero): JsonResponse
    {
        return response()->json($this->dossier($numero)->documents()->get());
    }

    public function storeDossier(DocumentUploadRequest $request, string $numero): JsonResponse
    {
        $dossier = $this->dossier($numero);

        return response()->json($this->televerser($request, $dossier), 201);
    }

    public function storeClient(DocumentUploadRequest $request, Client $client): JsonResponse
    {
        return response()->json($this->televerser($request, $client, "documents/clients/{$client->id}"), 201);
    }

    public function download(Document $document): StreamedResponse
    {
        return Storage::download($document->chemin, $document->nom_original);
    }

    public function destroy(Document $document): JsonResponse
    {
        $document->delete();

        return response()->json(status: 204);
    }

    private function televerser(DocumentUploadRequest $request, Dossier|Client $documentable, ?string $prefixe = null): Document
    {
        $fichier = $request->file('fichier');
        $prefixe ??= "documents/{$documentable->numero}";

        return Document::create([
            'documentable_type' => $documentable instanceof Dossier ? 'dossier' : 'client',
            'documentable_id' => $documentable->id,
            'categorie' => $request->enum('categorie', DocumentCategorie::class),
            'nom_original' => $fichier->getClientOriginalName(),
            'chemin' => $fichier->store($prefixe),
            'mime_type' => $fichier->getMimeType(),
            'taille' => $fichier->getSize(),
            'televerse_par' => $request->user()->id,
        ]);
    }

    private function dossier(string $numero): Dossier
    {
        return Dossier::where('numero', $numero)->firstOrFail();
    }
}
