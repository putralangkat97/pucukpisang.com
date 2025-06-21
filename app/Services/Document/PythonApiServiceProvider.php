<?php

namespace App\Services\Document;

use App\Abstracts\Document\AbstractAiServiceProvider;
use Illuminate\Support\Facades\Http;

class PythonApiServiceProvider extends AbstractAiServiceProvider
{
    protected string $base_url;

    public function __construct()
    {
        $this->base_url = rtrim(env('PYTHON_API_URL'), '/');
    }

    public function summarize(string $text, string $length): array
    {
        return Http::timeout(300)
            ->post(
                "{$this->base_url}/summarize",
                ['text' => $text, 'options' => ['length' => $length]]
            )
            ->throw()
            ->json();
    }

    public function translate(string $text, string $language): array
    {
        return Http::timeout(300)
            ->post(
                "{$this->base_url}/translate",
                ['text' => $text, 'options' => ['language' => $language]]
            )
            ->throw()
            ->json();
    }
}
