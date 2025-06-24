<?php

namespace App\Jobs;

use App\Actions\AI\SummaryProcess;
use App\Actions\AI\TranslateProcess;
use App\Actions\Documents\ExtractTextFromFile;
use App\Enums\Status;
use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Pipeline\Pipeline;
use Throwable;

class ProcessDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Document $document) {}

    public function handle(): void
    {
        $payload = [
            'model' => $this->document
        ];

        app(Pipeline::class)
            ->send($payload)->through([
                ExtractTextFromFile::class,
                TranslateProcess::class,
                SummaryProcess::class,
            ])->then(function (array $payload) {
                $document = $payload['model'];
                if ($document->status !== Status::ERRORED) {
                    $document->update(['status' => Status::COMPLETE]);
                }
            });
    }

    public function failed(Throwable $exception): void
    {
        $this->document->update(
            [
                'status' => Status::ERRORED,
                'error' => 'Job failed: ' . $exception->getMessage()
            ]
        );
    }
}
