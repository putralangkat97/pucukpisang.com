<?php

namespace App\Services\AIProviders;

use App\Abstracts\Audio\AbstractAITranscribProvider;
use Illuminate\Support\Facades\Http;

class PythonApiTranscriptionProvider extends AbstractAITranscribProvider
{
    public function transcribe(string $local_audio_path): string
    {
        $response = Http::timeout(1800)
            ->attach(
                'file',
                file_get_contents($local_audio_path),
                basename($local_audio_path)
            )
            ->post(rtrim(env('PYTHON_API_URL'), '/') . '/transcribe');

        $response->throw();

        return $response->json('transcript');
    }
}
