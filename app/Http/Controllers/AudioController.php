<?php

namespace App\Http\Controllers;

use App\Services\AIFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

class AudioController extends Controller
{
    public function index()
    {
        return view('audio.create');
    }

    public function process(Request $request)
    {
        // 1. Validation
        $request->validate([
            'source_type' => 'required|in:upload,youtube',
            'audio_file' => 'required_if:source_type,upload|file|mimes:mp3,mp4,mpeg,mpga,m4a,wav,webm|max:25600', // 25MB max for Whisper
            'youtube_url' => 'required_if:source_type,youtube|url',
            'operations' => 'required|array|min:1',
            'ai_model' => 'required|in:openai,gemini,deepseek',
        ]);

        $audioPath = null;
        $cleanupPath = null;

        try {
            // 2. Get Audio File Path (from Upload or YouTube)
            if ($request->input('source_type') === 'upload') {
                $audioPath = $request->file('audio_file')->getPathname();
            } else {
                // Download from YouTube
                $yt = new YoutubeDl();
                // Create a temporary directory to store the downloaded audio
                $downloadDir = storage_path('app/public/temp_audio');
                File::makeDirectory($downloadDir, 0755, true, true);

                $yt->setBinPath(env('YT_DLP_PATH')); // Set path if needed, e.g., for Windows

                $options = Options::create()
                    ->downloadPath($downloadDir)
                    ->extractAudio(true)
                    ->ffmpegLocation(env('FFMPEG_PATH'))
                    ->audioFormat('mp3')
                    ->output('%(id)s.%(ext)s')
                    ->url($request->input('youtube_url'));

                $collection = $yt->download($options);

                foreach ($collection->getVideos() as $video) {
                    if ($video->getError() !== null) {
                        return back()->withErrors(['youtube_url' => "Error downloading video: " . $video->getError()]);
                    }
                    $audioPath = $video->getFile();
                    $cleanupPath = $audioPath; // Mark this file for deletion later
                    break; // Process first video only
                }
            }

            if (!$audioPath) {
                return back()->withErrors(['audio_file' => "Could not get an audio file to process."]);
            }

            // 3. Transcribe Audio using Whisper API
            $driver = env('TRANSCRIPTION_DRIVER', 'api');
            $transcript = '';

            if ($driver === 'local') {
                $transcript = $this->callWhisperLocally($audioPath);
            }

            if (Str::startsWith($transcript, 'Error:')) {
                return back()->withErrors(['audio_file' => $transcript]);
            }

            // 4. Perform subsequent operations (Summary, Translation)
            $results = ['transcript' => $transcript];
            $totalTokens = 0;
            $textForTranslation = $transcript; // By default, translate the full transcript
            $model = $request->input('ai_model');

            if (in_array('summarize', $request->input('operations'))) {
                $length = $request->input('summary_length', 'medium');
                $prompt = "Summarize the following transcript in a {$length} format:\n\n{$transcript}";
                $ai_model = AIFactory::create($model);
                $aiResponse = $ai_model->call($prompt);

                $results['summary'] = $aiResponse['text'];
                $results['summary_tokens'] = $aiResponse['tokens'];
                $totalTokens += $aiResponse['tokens'];
                $textForTranslation = $results['summary']; // Update text for translation to be the summary
            }

            if (in_array('translate', $request->input('operations'))) {
                $language = $request->input('target_language', 'Spanish');
                $prompt = "Translate the following text into {$language}:\n\n{$textForTranslation}";
                $ai_model = AIFactory::create($model);
                $aiResponse = $ai_model->call($prompt);

                $results['translation'] = $aiResponse['text'];
                $results['translation_tokens'] = $aiResponse['tokens'];
                $results['language'] = $language;
                $totalTokens += $aiResponse['tokens'];
            }

            $results['total_tokens'] = $totalTokens;

            return view('audio.create', ['results' => $results]);
        } catch (\Exception $e) {
            Log::error('Audio processing failed: ' . $e->getMessage());
            return back()->withErrors(['audio_file' => 'An unexpected error occurred during processing. ' . $e->getMessage()]);
        } finally {
            // Clean up downloaded YouTube file
            if ($cleanupPath && File::exists($cleanupPath)) {
                File::delete($cleanupPath);
            }
        }
    }

    private function callWhisperLocally(string $filePath)
    {
        $executablePath = env('WHISPER_CPP_PATH');
        $modelPath = env('WHISPER_MODEL_PATH');

        if (!$executablePath || !$modelPath) {
            return 'Error: WHISPER_CPP_PATH and WHISPER_MODEL_PATH must be set in your .env file.';
        }

        try {
            // We use Laravel's Process facade to run the command securely
            $result = Process::timeout(600) // 10 minute timeout
                ->run([
                    $executablePath,
                    '--model',
                    $modelPath,
                    '--file',
                    $filePath,
                ]);

            if ($result->successful()) {
                // The transcription is the standard output of the command
                return trim($result->output());
            } else {
                // If it fails, log the error and return a friendly message
                Log::error('Local Whisper Failed: ' . $result->errorOutput());
                return 'Error: Local transcription failed. Check server logs for details.';
            }
        } catch (\Exception $e) {
            Log::error('Local Whisper Exception: ' . $e->getMessage());
            return 'Error: Could not execute the local transcription process.';
        }
    }
}
