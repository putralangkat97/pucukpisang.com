<?php

namespace App\Http\Controllers;

use App\Models\Audio;
use App\Services\AIFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Inertia\Inertia;
use Inertia\Response;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

class AudioController extends Controller
{
    public function index(): Response
    {
        $ai_models = [
            ['value' => 'gemini', 'text' => '🤖 Google (Gemini)'],
            ['value' => 'openai', 'text' => '🧠 OpenAI (GPT)'],
            ['value' => 'deepseek', 'text' => '🔍 DeepSeek'],
        ];

        $summary_length = [
            ['value' => 'short', 'text' => 'Short (a few sentences)'],
            ['value' => 'medium', 'text' => 'Medium (a paragraph)'],
            ['value' => 'long', 'text' => 'Long (multiple paragraphs)'],
        ];

        $languages = [
            ['value' => 'de', 'text' => '🇩🇪 German'],
            ['value' => 'es', 'text' => '🇪🇸 Spanish'],
            ['value' => 'zh', 'text' => '🇨🇳 Chinese'],
            ['value' => 'ja', 'text' => '🇯🇵 Japanese'],
            ['value' => 'id', 'text' => '🇮🇩 Indonesian'],
            ['value' => 'en', 'text' => '🇬🇧 English'],
        ];

        return Inertia::render('audio/index', [
            'ai_models' => $ai_models,
            'summary_length' => $summary_length,
            'languages' => $languages,
        ]);
    }

    public function show(Audio $audio): Response
    {
        return Inertia::render('audio/show', [
            'audio' => $audio,
        ]);
    }

    public function download(Audio $audio, string $type)
    {
        $audio_text = "";

        if ($type === 'transcript') {
            $audio_text = $audio->transcript;
        }
        if ($type === 'summary') {
            $audio_text = $audio->summary;
        }
        if ($type === 'translation') {
            $audio_text = $audio->translations;
        }

        $file_name = $type . '-' . $audio->id . '.txt';
        return response($audio_text, 200, [
            'Content-Type' => 'text/txt',
            'Content-Disposition' => 'attachment; filename="' . $file_name . '"',
        ]);
    }

    // public function process(Request $request)
    // {
    //     // 1. Validation
    //     $request->validate([
    //         'audio_file' => 'nullable|file|mimes:mp3,mp4,wav|max:10240',
    //         'youtube_url' => 'nullable|string|url',
    //         'ai_model' => 'required|string|in:openai,gemini,deepseek',
    //         'transcribe' => 'required|boolean',
    //         'operations' => 'required|array|min:1',
    //         'operations.summarize' => 'sometimes|required|array',
    //         'operations.summarize.length' => 'required_with:operations.summarize|string|in:short,medium,long',
    //         'operations.translate' => 'sometimes|required|array',
    //         'operations.translate.language' => 'required_with:operations.translate|string|in:de,es,zh,ja,id,en',
    //     ]);

    //     $audio_path = null;
    //     $cleanup_path = null;

    //     try {
    //         // 2. get audio file path from upload/YouTube
    //         if ($request->input('source_type') === 'upload') {
    //             $audio_path = $request->file('audio_file')->getPathname();
    //         } else {
    //             // download from YouTube
    //             $yt = new YoutubeDl();
    //             // create a temporary dir to store the downloaded audio
    //             $download_dir = storage_path('app/public/temp_audio');
    //             File::makeDirectory($download_dir, 0755, true, true);

    //             $options = Options::create()
    //                 ->downloadPath($download_dir)
    //                 ->extractAudio(true)
    //                 ->ffmpegLocation(env('FFMPEG_PATH')) // set ffmpeg path /opt/homebrew/bin/ffmpeg
    //                 ->audioFormat('mp3')
    //                 ->output('%(id)s.%(ext)s')
    //                 ->url($request->input('youtube_url'));

    //             $collection = $yt->download($options);

    //             foreach ($collection->getVideos() as $video) {
    //                 if ($video->getError() !== null) {
    //                     return back()->withErrors(['youtube_url' => "Error downloading video: " . $video->getError()]);
    //                 }
    //                 $audio_path = $video->getFile();
    //                 $cleanup_path = $audio_path; // mark this file for deletion later
    //                 break; // process the first video only
    //             }
    //         }

    //         if (!$audio_path) {
    //             return back()->withErrors(['audio_file' => "Could not get an audio file to process."]);
    //         }

    //         // 3. transcribe audio using local | whisper API muahal
    //         $driver = env('AI_PROVIDER_STRATEGY', 'commercial_api');
    //         $transcript = '';

    //         if ($driver === 'local_cli') {
    //             $transcript = $this->callWhisperLocally($audio_path);
    //         }

    //         if (Str::startsWith($transcript, 'Error:')) {
    //             return back()->withErrors(['audio_file' => $transcript]);
    //         }

    //         // 4. perform subsequent operations summary then translate
    //         $results = ['transcript' => $transcript];
    //         $total_tokens = 0;
    //         $text_for_translation = $transcript; // by default, translate the full transcript
    //         $model = $request->input('ai_model');

    //         if (in_array('summarize', $request->input('operations'))) {
    //             $length = $request->input('summary_length', 'medium');
    //             $prompt = "Summarize the following transcript in a {$length} format:\n\n{$transcript}";
    //             $ai_model = AIFactory::create($model);
    //             $ai_response = $ai_model->call($prompt);

    //             $results['summary'] = $ai_response['text'];
    //             $results['summary_tokens'] = $ai_response['tokens'];
    //             $total_tokens += $ai_response['tokens'];
    //             $text_for_translation = $results['summary'];
    //         }

    //         if (in_array('translate', $request->input('operations'))) {
    //             $language = $request->input('target_language', 'Spanish');
    //             $prompt = "Translate the following text into {$language}:\n\n{$text_for_translation}";
    //             $ai_model = AIFactory::create($model);
    //             $ai_response = $ai_model->call($prompt);

    //             $results['translation'] = $ai_response['text'];
    //             $results['translation_tokens'] = $ai_response['tokens'];
    //             $results['language'] = $language;
    //             $total_tokens += $ai_response['tokens'];
    //         }

    //         $results['total_tokens'] = $total_tokens;

    //         return view('audio.create', ['results' => $results]);
    //     } catch (\Exception $e) {
    //         Log::error('Audio processing failed: ' . $e->getMessage());
    //         return back()->withErrors(['audio_file' => 'An unexpected error occurred during processing. ' . $e->getMessage()]);
    //     } finally {
    //         // clean the downloaded youtube file
    //         if ($cleanup_path && File::exists($cleanup_path)) {
    //             File::delete($cleanup_path);
    //         }
    //     }
    // }

    // private function callWhisperLocally(string $filePath)
    // {
    //     $executablePath = env('WHISPER_CPP_PATH');
    //     $modelPath = env('WHISPER_MODEL_PATH');

    //     if (!$executablePath || !$modelPath) {
    //         return 'Error: WHISPER_CPP_PATH and WHISPER_MODEL_PATH must be set in your .env file.';
    //     }

    //     try {
    //         // 10 minute timeout
    //         $result = Process::timeout(600)
    //             ->run([
    //                 $executablePath,
    //                 '--model',
    //                 $modelPath,
    //                 '--file',
    //                 $filePath,
    //             ]);

    //         if ($result->successful()) {
    //             return trim($result->output());
    //         } else {
    //             Log::error('Local Whisper Failed: ' . $result->errorOutput());
    //             return 'Error: Local transcription failed. Check server logs for details.';
    //         }
    //     } catch (\Exception $e) {
    //         Log::error('Local Whisper Exception: ' . $e->getMessage());
    //         return 'Error: Could not execute the local transcription process.';
    //     }
    // }
}
