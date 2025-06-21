<?php

namespace App\Services;

use App\Abstracts\AI\AbstractAIProvider;
use App\Services\Gemini;
use App\Services\OpenAI;
use App\Services\DeepSeek;

class AIFactory
{
    public static function create(string $model): AbstractAIProvider
    {
        return match ($model) {
            'gemini' => new Gemini([
                'api_url' => env('GOOGLE_GEMINI_API_URL'),
                'api_key' => env('GOOGLE_GEMINI_API_KEY'),
            ]),
            'open_ai' => new OpenAI([
                'api_url' => env('OPENAI_API_URL'),
                'api_key' => env('OPENAI_API_KEY'),
            ]),
            'deepseek' => new DeepSeek([
                'api_url' => env('DEEPSEEK_API_URL'),
                'api_key' => env('DEEPSEEK_API_KEY'),
            ]),
        };
    }
}
