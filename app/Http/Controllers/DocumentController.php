<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Inertia\Inertia;
use Inertia\Response;

class DocumentController extends Controller
{
    public function index(): Response
    {
        $ai_models = [
            ['value' => 'gemini', 'text' => '🤖 Google (Gemini)'],
            ['value' => 'openai', 'text' => '🧠 OpenAI (GPT)'],
            ['value' => 'deepseek', 'text' => '🔍 DeepSeek'],
        ];

        $summary_length = [
            ['value' => 'short', 'text' => 'Short (a few sentences)'],
            ['value' => 'medium', 'text' => 'Medium (a paragraph)'],
            ['value' => 'long', 'text' => 'Long (multiple paragraphs)'],
        ];

        $languages = [
            ['value' => 'de', 'text' => '🇩🇪 German'],
            ['value' => 'es', 'text' => '🇪🇸 Spanish'],
            ['value' => 'zh', 'text' => '🇨🇳 Chinese'],
            ['value' => 'ja', 'text' => '🇯🇵 Japanese'],
            ['value' => 'id', 'text' => '🇮🇩 Indonesian'],
            ['value' => 'en', 'text' => '🇬🇧 English'],
        ];

        return Inertia::render('document/index', [
            'ai_models' => $ai_models,
            'summary_length' => $summary_length,
            'languages' => $languages,
        ]);
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
