<?php

namespace App\Services\Document;

use App\Abstracts\Document\AbstractAiServiceProvider;
use App\Services\DeepSeek;
use App\Services\Gemini;
use App\Services\OpenAI;

class AIServiceProvider extends AbstractAiServiceProvider
{
    protected $ai_client;

    public function __construct(string $model = 'gemini')
    {
        $this->ai_client = match ($model) {
            'gemini' => new Gemini(['api_key' => env('GOOGLE_GEMINI_API_KEY'), 'api_url' => env('GOOGLE_GEMINI_API_URL')]),
            'openai' => new OpenAI(['api_key' => env('OPENAI_API_KEY'), 'api_url' => env('OPENAI_API_URL')]),
            'deepseek' => new DeepSeek(['api_key' => env('DEEPSEEK_API_KEY'), 'api_url' => env('DEEPSEEK_API_URL')]),
        };
    }

    public function summarize(string $text, string $length): array
    {
        $prompt = "You are a professional editor. Summarize the following text clearly and concisely,
            preserving the main points and tone. Remove any redundancy or filler. Summarize the following
            text in a {$length} format. Text:\n\n{$text}";
        $response = $this->ai_client->call($prompt);
        $response['text'] = $this->cleanResponseText($response['text']);

        return $response;
    }

    public function translate(string $text, string $language): array
    {
        $prompt = "You're a native speaker and creative translator. Translate the following text into
            {$language}:\n\n{$text}.\n\n\nTranslates like a human expert, not literal machine translation.";
        $response = $this->ai_client->call($prompt);
        $response['text'] = $this->cleanResponseText($response['text']);

        return $response;
    }

    // 🔥 sanitize the response. thanks ChatGPT!
    private function cleanResponseText(string $raw_text): string
    {
        $lines = explode("\n", trim($raw_text));
        if (count($lines) <= 1) {
            return $raw_text;
        }
        $first_line_lower = strtolower(trim($lines[0]));

        $preambles = [
            'okay, here',
            'here is',
            'here\'s',
            'sure, here',
            'certainly,',
            'tentu, berikut',
            'baiklah, ini',
        ];
        foreach ($preambles as $preamble) {
            if (str_starts_with($first_line_lower, $preamble)) {
                return trim(implode("\n", array_slice($lines, 1)));
            }
        }

        return $raw_text;
    }
}
