<?php

namespace App\Actions\AI;

use App\Enums\Status;
use App\Models\Document;
use App\Services\Document\AIModeFactory;
use Illuminate\Support\Facades\Log;

class TranslateProcess
{
    public function handle(array $payload, \Closure $next)
    {
        /** @var Document $document */
        $document = $payload['model'];

        if (!isset($document->options['translate'])) {
            return $next($document);
        }

        try {
            $document->update(['status' => Status::PROCESSING_TRANSLATE]);

            $language = $document->options['translate']['language'] ?? 'en';
            $mode = AIModeFactory::create();
            $response = $mode->translate(
                $payload['text_for_ai'],
                $language,
            );
            $translated_text = $response['text'];
            $document->update(['translations' => $translated_text]);
            $payload['text_for_ai'] = $translated_text;

            return $next($payload);
        } catch (\Exception $e) {
            $this->handleError($document['model'], 'Translation failed: ' . $e->getMessage());
            return;
        }
    }

    private function handleError(Document $document, string $error_message): void
    {
        Log::error($error_message, ['doc_id' => $document->id]);
        $document->update(['status' => Status::ERRORED, 'error' => $error_message]);
    }
}
