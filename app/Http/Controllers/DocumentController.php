<?php

namespace App\Http\Controllers;

use App\Enums\DocumentCategorie;
use App\Http\Requests\DocumentUploadRequest;
use App\Models\Client;
use App\Models\Document;
use App\Models\Dossier;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function __construct(
        private readonly AuditService $audit,
    ) {}

    public function storeDossier(DocumentUploadRequest $request, string $numero): RedirectResponse
    {
        $dossier = $this->dossier($numero);

        $document = $this->televerser($request, 'dossier', $dossier->id, "documents/{$dossier->numero}");

        $this->audit->journaliser(
            $request->user(),
            "Téléversement du document « {$document->nom_original} » ({$document->categorie->value}) sur le dossier #{$dossier->numero}",
            $dossier,
            'document',
            $document->id,
        );

        return redirect()->route('dossiers.show', $numero)->with('message', __('app.messages.document_televerse'));
    }

    public function storeClient(DocumentUploadRequest $request, Client $client): RedirectResponse
    {
        $document = $this->televerser($request, 'client', $client->id, "documents/clients/{$client->id}");

        $this->audit->journaliser(
            $request->user(),
            "Téléversement du document administratif « {$document->nom_original} » pour le client {$client->raison_sociale}",
            null,
            'document',
            $document->id,
        );

        return redirect()->route('clients.show', $client)->with('message', __('app.messages.document_client_ajoute'));
    }

    public function download(Document $document): StreamedResponse
    {
        return Storage::download($document->chemin, $document->nom_original);
    }

    public function destroy(Document $document): RedirectResponse
    {
        $dossier = $document->documentable instanceof Dossier ? $document->documentable : null;

        $document->delete();

        $this->audit->journaliser(auth()->user(), "Suppression (logique) du document « {$document->nom_original} »", $dossier, 'document', $document->id);

        return back()->with('message', __('app.messages.document_supprime'));
    }

    private function televerser(DocumentUploadRequest $request, string $type, int $id, string $prefixe): Document
    {
        $fichier = $request->file('fichier');

        return Document::create([
            'documentable_type' => $type,
            'documentable_id' => $id,
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
