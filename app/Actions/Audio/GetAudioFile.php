<?php

namespace App\Actions\Audio;

use App\Enums\Status;
use App\Models\Audio;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

class GetAudioFile
{
    public function handle(array $payload, \Closure $next)
    {
        /** @var Audio $audio */
        $audio = $payload['model'];

        try {
            if ($audio->source_type === 'upload') {
                // if the file is already existed, get the full path.
                $local_path = Storage::disk('r2')
                    ->put(
                        $audio->source_path,
                        file_get_contents(
                            public_path($audio->source_path)
                        )
                    );

                if (!file_exists($local_path)) {
                    throw new \Exception("Uploaded file does not exist at path: {$local_path}");
                    return;
                }

                $payload['local_audio_path'] = $local_path;
            } elseif ($audio->source_type === 'youtube') {
                $download_dir = storage_path('app/temp_audio');
                File::makeDirectory($download_dir, 0755, true, true);

                $yt = new YoutubeDl();
                $options = Options::create()
                    ->ffmpegLocation(env('FFMPEG_PATH'))
                    ->downloadPath($download_dir)
                    ->extractAudio(true)
                    ->audioFormat('mp3')
                    ->output('%(id)s.%(ext)s')
                    ->url($audio->source_path);

                $result = $yt->download($options)->getVideos()[0];

                if ($result->getError() !== null) {
                    throw new \Exception("YouTube download failed: " . $result->getError());
                    return;
                }

                $payload['local_audio_path'] = $result->getFile()->getRealPath();
            } else {
                throw new \Exception("Unsupported source type: " . $audio->source_type);
                return;
            }

            $final_result = $next($payload);

            // clean up file after download
            if ($audio->source_type === 'youtube' && isset($payload['local_audio_path']) && File::exists($payload['local_audio_path'])) {
                Log::info("Cleaning up temporary audio file: " . $payload['local_audio_path']);
                File::delete($payload['local_audio_path']);
            }

            return $final_result;
        } catch (\Exception $e) {
            Log::error("GetAudioFile failed for ID: {$audio->id}. Error: " . $e->getMessage());
            $audio->update([
                'status' => Status::ERRORED,
                'error' => 'Failed to prepare audio file: ' . $e->getMessage(),
            ]);
            return;
        }
    }
}
