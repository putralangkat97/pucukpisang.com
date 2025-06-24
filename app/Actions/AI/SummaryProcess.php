<?php

namespace App\Actions\AI;

use App\Enums\Status;
use App\Models\Document;
use App\Services\Document\AIModeFactory;
use Illuminate\Support\Facades\Log;

class SummaryProcess
{
    public function handle(array $payload, \Closure $next)
    {
        /** @var Document $document */
        $document = $payload['model'];

        if (!isset($document->options['summarize'])) {
            return $next($document);
        }

        try {
            $document->update(['status' => Status::PROCESSING_SUMMARY]);

            $mode = AIModeFactory::create();
            $response = $mode->summarize(
                $payload['text_for_ai'],
                $document->options['summarize']['length']
            );

            $document->update(['summary' => $response['text']]);

            return $next($payload);
        } catch (\Exception $e) {
            $this->handleError($document, 'Summarization failed: ' . $e->getMessage());
            return;
        }
    }

    private function handleError(Document $document, string $error_message): void
    {
        Log::error($error_message, ['doc_id' => $document->id]);
        $document->update(['status' => Status::ERRORED, 'error' => $error_message]);
    }
}
