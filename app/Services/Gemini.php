<?php

namespace App\Services;

use App\Abstracts\AbstractAIProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Gemini extends AbstractAIProvider
{
    public function __construct(protected array $config) {}

    public function call(string $prompt)
    {
        try {
            $url = $this->config['api_url'] . '?key=' . $this->config['api_key'];

            $response = Http::timeout(120)->post($url, [
                'contents' => [
                    [
                        'parts' =>
                        [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->failed()) {
                Log::error('Gemini API Error: ' . $response->body());
                return ['text' => 'Error: ' . $response->json('error.message'), 'tokens' => 0];
            }

            $text = $response->json('candidates.0.content.parts.0.text');
            if (is_null($text)) {
                return ['text' => 'Error: The AI model returned an empty response.', 'tokens' => 0];
            }

            // Gemini puts token usage in 'usageMetadata'
            return [
                'text' => $text,
                'tokens' => $response->json('usageMetadata.totalTokenCount') ?? 0
            ];
        } catch (\Exception $e) {
            Log::error('Gemini API call exception: ' . $e->getMessage());
            return ['text' => 'Error: Could not connect to the Gemini service.', 'tokens' => 0];
        }
    }
}
