<?php

namespace App\Jobs;

use App\Actions\AI\SummaryProcess;
use App\Actions\AI\TranslateProcess;
use App\Actions\Audio\GetAudioFile;
use App\Actions\Audio\TranscribeAudio;
use App\Enums\Status;
use App\Models\Audio;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Pipeline\Pipeline;
use Throwable;

class ProcessAudioJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public function __construct(public Audio $audio) {}

    public function handle(): void
    {
        $payload = ['model' => $this->audio];
        app(Pipeline::class)
            ->send($payload)->through([
                GetAudioFile::class,
                TranscribeAudio::class,
                SummaryProcess::class,
                TranslateProcess::class,
            ])->then(function (array $payload) {
                $model = $payload['model'];
                if ($model->status !== Status::ERRORED) {
                    $model->update(['status' => Status::COMPLETE]);
                }
            });
    }

    public function failed(Throwable $exception): void
    {
        $this->audio->update(['status' => Status::ERRORED, 'error' => 'Job failed: ' . $exception->getMessage()]);
    }
}
