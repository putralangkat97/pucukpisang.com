<?php

namespace App\Services\Audio;

use App\Abstracts\Audio\AbstractAITranscribProvider;
use Illuminate\Support\Facades\Process;

class LocalCliTranscriptionProvider extends AbstractAITranscribProvider
{
    public function transcribe(string $local_audio_path): string
    {
        $executable_path = env('WHISPER_CPP_PATH');
        $model_path = env('WHISPER_MODEL_PATH');

        if (!$executable_path || !$model_path) {
            throw new \Exception('WHISPER_CPP_PATH and WHISPER_MODEL_PATH must be set for local_cli transcription.');
        }

        $result = Process::timeout(1800)
            ->run([
                $executable_path,
                '--model',
                $model_path,
                '--file',
                $local_audio_path,
                '--output-txt',
            ]);

        if (!$result->successful()) {
            throw new \Exception('Local whisper-cli process failed: ' . $result->errorOutput());
        }

        return trim($result->output());
    }
}
