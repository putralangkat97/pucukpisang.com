<?php

namespace App\Actions\Documents;

use App\Models\Document;
use App\Enums\Document\Status;
use App\Services\AIModeFactory;
use Closure;
use Illuminate\Support\Facades\Log;

class SummarizeText
{
    public function handle(Document $document, Closure $next)
    {
        if (!isset($document->options['summarize'])) {
            return $next($document);
        }

        try {
            $document->update(['status' => Status::PROCESSING_SUMMARY]);

            $mode = AIModeFactory::create();
            $response = $mode->summarize($document->text_extraction, $document->options['summarize']['length']);

            $document->update(
                ['summary' => $response['text']]
            );
        } catch (\Exception $e) {
            $this->handleError($document, 'Summarization failed: ' . $e->getMessage());
            return;
        }
        return $next($document);
    }

    private function handleError(Document $document, string $msg): void
    {
        Log::error($msg, ['doc_id' => $document->id]);
        $document->update(['status' => Status::ERRORED, 'error' => $msg]);
    }
}
