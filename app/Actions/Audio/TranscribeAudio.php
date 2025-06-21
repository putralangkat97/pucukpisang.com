<?php

namespace App\Actions\Audio;

use App\Enums\Status;
use App\Models\Audio;
use App\Services\Audio\AIModeFactory;
use Illuminate\Support\Facades\Log;

class TranscribeAudio
{
    public function handle(array $payload, \Closure $next)
    {
        /** @var \App\Models\Audio $audio */
        $audio = $payload['model'];
        $local_audio_path = $payload['local_audio_path'];

        try {
            // update status to poll endpoint
            $audio->update(['status' => Status::PROCESSING_TRANSCRIBE]);

            if (empty($local_audio_path) || !file_exists($local_audio_path)) {
                throw new \Exception('Local audio file path was not found or is invalid.');
            }

            $transcription_provider = AIModeFactory::create();
            $transcript = $transcription_provider->transcribe($local_audio_path);

            $audio->update([
                'transcript' => $transcript,
                'status' => Status::PROCESSING_SUMMARY,
            ]);
        } catch (\Exception $e) {
            $this->handleError($audio, $e->getMessage());
            return;
        }

        return $next($payload);
    }

    private function handleError(Audio $audio, string $message): void
    {
        Log::error("Audio Transcription Failed for ID: {$audio->id}. Error: " . $message);
        $audio->update(['status' => Status::ERRORED, 'error' => $message]);
    }
}
