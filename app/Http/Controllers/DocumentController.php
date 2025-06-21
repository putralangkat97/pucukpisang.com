<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Inertia\Inertia;
use Inertia\Response;

class DocumentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('document/index');
    }

    public function show(Document $document): Response
    {
        return Inertia::render('document/show', [
            'document' => $document,
        ]);
    }

    public function download(Document $document, string $type)
    {
        $document_text = "";

        if ($type === 'summary') {
            $document_text = $document->summary;
        }
        if ($type === 'translation') {
            $document_text = $document->translations;
        }

        $file_name = $type . '-' . $document->id . '.txt';
        return response($document_text, 200, [
            'Content-Type' => 'text/txt',
            'Content-Disposition' => 'attachment; filename="' . $file_name . '"',
        ]);
    }
}
