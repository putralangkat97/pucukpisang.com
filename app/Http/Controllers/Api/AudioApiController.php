<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAudioRequest;
use App\Jobs\ProcessAudioJob;
use App\Models\Audio;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AudioApiController extends Controller
{
    public function store(StoreAudioRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $source_path = '';

        if ($validated['source_type'] === 'upload') {
            $source_path = $validated['audio_file']->store('audios');
        } else {
            $source_path = $validated['youtube_url'];
        }

        $audio = Audio::create([
            'id' => Str::uuid(),
            'status' => \App\Enums\Status::PENDING,
            'source_type' => $validated['source_type'],
            'source_path' => $source_path,
            'options' => $validated['operations'],
            'ai_model' => $validated['ai_model'] ?? 'default',
        ]);

        ProcessAudioJob::dispatch($audio);

        Log::info('ada-apa-di-model?', [
            'data' => $audio
        ]);

        return response()->json(['id' => $audio->id], 202);
    }

    public function status(Audio $audio): JsonResponse
    {
        $audio->message = $audio->status->message();
        return response()->json($audio);
    }
}
