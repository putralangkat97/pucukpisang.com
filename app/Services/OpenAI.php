<?php

namespace App\Services;

use App\Abstracts\AbstractAIProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAI extends AbstractAIProvider
{
    public function __construct(protected array $config) {}

    public function call(string $prompt)
    {
        try {
            $response = Http::withToken(env('OPENAI_API_KEY'))
                ->timeout(120)->post(env('OPENAI_API_URL'), [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'temperature' => 0.5,
                ]);

            if ($response->failed()) {
                Log::error('OpenAI API Error: ' . $response->body());
                return ['text' => 'Error: ' . $response->json('error.message'), 'tokens' => 0];
            }

            // Return an array with text and tokens
            return [
                'text' => trim($response->json('choices.0.message.content')),
                'tokens' => $response->json('usage.total_tokens') ?? 0
            ];
        } catch (\Exception $e) {
            Log::error('API call exception: ' . $e->getMessage());
            return ['text' => 'Error: Could not connect to the AI service.', 'tokens' => 0];
        }
    }
}
