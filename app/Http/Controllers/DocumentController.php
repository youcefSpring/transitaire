<?php

namespace App\Http\Controllers;

use App\Enums\DocumentCategorie;
use App\Http\Requests\DocumentUploadRequest;
use App\Models\Client;
use App\Models\Document;
use App\Models\Dossier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function storeDossier(DocumentUploadRequest $request, string $numero): RedirectResponse
    {
        $dossier = $this->dossier($numero);

        $this->televerser($request, 'dossier', $dossier->id, "documents/{$dossier->numero}");

        return redirect()->route('dossiers.show', $numero)->with('message', 'Document téléversé et rattaché au dossier.');
    }

    public function storeClient(DocumentUploadRequest $request, Client $client): RedirectResponse
    {
        $this->televerser($request, 'client', $client->id, "documents/clients/{$client->id}");

        return redirect()->route('clients.show', $client)->with('message', 'Document administratif ajouté.');
    }

    public function download(Document $document): StreamedResponse
    {
        return Storage::download($document->chemin, $document->nom_original);
    }

    public function destroy(Document $document): RedirectResponse
    {
        $document->delete();

        return back()->with('message', 'Document supprimé.');
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
