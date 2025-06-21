<?php

namespace App\Abstracts\Audio;

abstract class AbstractAITranscribProvider
{
    abstract public function transcribe(string $local_audio_path): string;
}
