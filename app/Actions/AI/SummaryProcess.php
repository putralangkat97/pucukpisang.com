<?php

namespace App\Actions\AI;

use App\Enums\Status;
use App\Services\Document\AIModeFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class SummaryProcess
{
    public function handle(array $payload, \Closure $next)
    {
        /** @var Model|\App\Models\Document $model */
        $model = $payload['model'];

        if (!isset($model->options['summarize'])) {
            return $next($payload);
        }

        try {
            $model->update(['status' => Status::PROCESSING_SUMMARY]);

            $text_to_summarize = $model->text_extraction ?? $model->transcript;
            $mode = AIModeFactory::create();
            $response = $mode->summarize($text_to_summarize, $model->options['summarize']['length']);

            $model->update(
                ['summary' => $response['text']]
            );
        } catch (\Exception $e) {
            $this->handleError($model, 'Summarization failed: ' . $e->getMessage());
            return;
        }

        return $next($payload);
    }

    private function handleError(Model $model, string $message): void
    {
        Log::error($message, ['doc_id' => $model->id]);
        $model->update(['status' => Status::ERRORED, 'error' => $message]);
    }
}
