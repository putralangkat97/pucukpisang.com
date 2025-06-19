<?php

namespace App\Services;

use App\Services\Gemini;

class AIFactory
{
    public static function create(string $model)
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
