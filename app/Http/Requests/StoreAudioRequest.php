<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAudioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'source_type' => 'required|string|in:upload,youtube',
            'audio_file' => 'required_if:source_type,upload|file|mimes:mp3,mp4,mpeg,wav,webm|max:25600', // Maks. 25MB
            'youtube_url' => 'required_if:source_type,youtube|url',
            'ai_model' => 'nullable|string',
            'operations' => 'required|array',
            'operations.summarize.length' => 'required_with:operations.summarize|string',
            'operations.translate.language' => 'required_with:operations.translate|string',
        ];
    }
}
