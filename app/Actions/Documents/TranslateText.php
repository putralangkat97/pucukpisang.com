<?php

namespace App\Actions\Documents;

use App\Models\Document;
use App\Enums\Document\Status;
use App\Services\AIFactory;
use App\Services\AIModeFactory;
use Closure;
use Illuminate\Support\Facades\Log;

class TranslateText
{
    public function handle(Document $document, Closure $next)
    {
        if (!isset($document->options['translate'])) {
            return $next($document);
        }

        try {
            $document->update(['status' => Status::PROCESSING_TRANSLATE]);

            $language = $document->options['translate']['language'] ?? 'en';
            $mode = AIModeFactory::create();
            $response = $mode->translate(
                $document->text_extraction,
                $language,
            );

            $document->update(['translations' => $response['text']]);
        } catch (\Exception $e) {
            return $this->handleError($document, 'Translation failed: ' . $e->getMessage());
        }

        return $next($document);
    }

    private function handleError(Document $document, string $message): void
    {
        Log::error($message, ['doc_id' => $document->id]);
        $document->update(['status' => Status::ERRORED, 'error' => $message]);
    }
}
