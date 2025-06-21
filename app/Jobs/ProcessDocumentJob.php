<?php

namespace App\Jobs;

use App\Actions\Documents\ExtractTextFromFile;
use App\Actions\Documents\SummarizeText;
use App\Actions\Documents\TranslateText;
use App\Enums\Document\Status;
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
        app(Pipeline::class)
            ->send($this->document)->through([
                ExtractTextFromFile::class,
                SummarizeText::class,
                TranslateText::class,
            ])->then(function (Document $document) {
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
