<?php

namespace App\Services\Document;

use App\Abstracts\Document\AbstractAiServiceProvider;
use App\Services\Document\AIServiceProvider;
use App\Services\Document\LocalCliServiceProvider;
use InvalidArgumentException;

class AIModeFactory
{
    public static function create(): AbstractAiServiceProvider
    {
        return match (env('AI_PROVIDER_STRATEGY', 'python_api')) {
            'commercial_api' => new AIServiceProvider(),
            'local_cli' => new LocalCliServiceProvider(),
            default => throw new InvalidArgumentException("Unsupported AI Provider Strategy."),
        };
    }
}
