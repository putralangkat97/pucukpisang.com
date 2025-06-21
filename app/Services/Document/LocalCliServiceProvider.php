<?php
namespace App\Services\Document;

use App\Abstracts\Document\AbstractAiServiceProvider;
use Illuminate\Support\Facades\Process;

class LocalCliServiceProvider extends AbstractAiServiceProvider
{
    public function summarize(string $text, string $length): array {
        $prompt = "<|user|>\nYou are a professional editor. Summarize the following text clearly and concisely, preserving the main points and tone. Remove any redundancy or filler. Summarize the following text in a {$length} format. Text:\n\n{$text}<|end|>\n<|assistant|>\n";
        $result = $this->runLlama(env('LLAMA_SUMMARIZE_MODEL_PATH'), $prompt);

        return ['text' => $result, 'tokens' => 0];
    }

    public function translate(string $text, string $language): array {
        $system_prompt = "You are a helpful assistant that translates text accurately.";
        $user_prompt = "Translate the following text to {$language}:\n\n{$text}";
        $prompt = "<|im_start|>system\n{$system_prompt}<|im_end|>\n<|im_start|>user\n{$user_prompt}<|im_end|>\n<|im_start|>assistant\n";
        $result = $this->runLlama(env('LLAMA_TRANSLATE_MODEL_PATH'), $prompt);

        return ['text' => $result, 'tokens' => 0];
    }

    private function runLlama(string $modelPath, string $prompt): string {
        $result = Process::timeout(900)->run([env('LLAMA_CPP_PATH'), '-m', $modelPath, '-p', $prompt, '-n', 2048, '--temp', 0.5, '--ctx-size', 4096, '--no-display-prompt']);
        if (!$result->successful()) {
            throw new \Exception('Local CLI Provider Error: ' . $result->errorOutput());
        }

        return trim($result->output());
    }
}
