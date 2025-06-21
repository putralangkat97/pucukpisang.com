<?php

namespace App\Services\Audio;

use App\Abstracts\Audio\AbstractAITranscribProvider;
use App\Services\AIProviders\PythonApiTranscriptionProvider;
use InvalidArgumentException;

class AIModeFactory
{
    public static function create(): AbstractAITranscribProvider
    {
        return match (env('TRANSCRIBE_PROVIDER_STRATEGY', 'python_api')) {
            // 'commercial_api' => new AIServiceProvider(),
            'local_cli' => new LocalCliTranscriptionProvider(),
            'python_api' => new PythonApiTranscriptionProvider(),
            default => throw new InvalidArgumentException("Unsupported AI Provider Strategy."),
        };
    }
}
