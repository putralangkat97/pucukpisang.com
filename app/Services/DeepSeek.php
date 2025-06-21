<?php

namespace App\Services;

use App\Abstracts\AbstractAIProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeepSeek extends AbstractAIProvider
{
    public function __construct(protected array $config) {}

    public function call(string $prompt)
    {
        try {
            $response = Http::withToken(env('DEEPSEEK_API_KEY'))
                ->timeout(120)->post(env('DEEPSEEK_API_URL'), [
                    'model' => 'deepseek-chat',
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'temperature' => 0.5,
                ]);

            if ($response->failed()) {
                Log::error('DeepSeek API Error: ' . $response->body());
                return ['text' => 'Error: ' . $response->json('error.message'), 'tokens' => 0];
            }

            // Like OpenAI, DeepSeek uses the 'usage' object
            return [
                'text' => trim($response->json('choices.0.message.content')),
                'tokens' => $response->json('usage.total_tokens') ?? 0
            ];
        } catch (\Exception $e) {
            Log::error('DeepSeek API call exception: ' . $e->getMessage());
            return ['text' => 'Error: Could not connect to the DeepSeek service.', 'tokens' => 0];
        }
    }
}
