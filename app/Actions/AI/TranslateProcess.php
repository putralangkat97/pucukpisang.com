<?php

namespace App\Actions\AI;

use App\Enums\Status;
use App\Services\Document\AIModeFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class TranslateProcess
{
    public function handle(array $payload, \Closure $next)
    {
        /** @var Model|\App\Models\Document $model */
        $model = $payload['model'];

        if (!isset($model->options['translate'])) {
            return $next($payload);
        }

        try {
            $model->update(['status' => Status::PROCESSING_TRANSLATE]);

            $language = $model->options['translate']['language'] ?? 'en';
            $text_to_summarize = $model->text_extraction ?? $model->transcript;
            $mode = AIModeFactory::create();
            $response = $mode->translate(
                $text_to_summarize,
                $language,
            );

            $model->update(['translations' => $response['text']]);
        } catch (\Exception $e) {
            $this->handleError($payload['model'], 'Translation failed: ' . $e->getMessage());
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
