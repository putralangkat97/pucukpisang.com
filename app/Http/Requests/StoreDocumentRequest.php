<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'ai_model' => 'required|string|in:openai,gemini,deepseek',
            'operations' => 'required|array|min:1',
            'operations.summarize' => 'sometimes|required|array',
            'operations.summarize.length' => 'required_with:operations.summarize|string|in:short,medium,long',
            'operations.translate' => 'sometimes|required|array',
            'operations.translate.language' => 'required_with:operations.translate|string|in:de,es,zh,ja,id,en',
        ];
    }
}
