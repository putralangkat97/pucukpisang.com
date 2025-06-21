<?php

namespace App\Abstracts\Document;

abstract class AbstractAiServiceProvider
{
    abstract public function summarize(string $text, string $length): array;
    abstract public function translate(string $text, string $language): array;
}
