<?php

namespace App\Actions\Documents;

use App\Models\Document;
use App\Enums\Document\Status;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\PdfToText\Pdf;
use thiagoalessio\TesseractOCR\TesseractOCR;

class ExtractTextFromFile
{
    public function handle(Document $document, Closure $next)
    {
        try {
            $document->update(['status' => Status::EXTRACTING_DOCUMENT]);
            $full_path = Storage::disk('local')->path($document->file);

            if (!file_exists($full_path)) {
                throw new \Exception("File does not exist at path: {$full_path}");
            }

            $text = match ($document->type) {
                'pdf' => (new Pdf(env('PDFTOTEXT_PATH')))->setPdf($full_path)->text(),
                'jpeg', 'jpg', 'png' => (new TesseractOCR($full_path))->executable(env('TESSERACT_PATH', 'tesseract'))->run(),
                default => (new TesseractOCR($full_path))->executable(env('TESSERACT_PATH', 'tesseract'))->run(),
            };

            if (empty(trim($text))) {
                throw new \Exception('Could not extract any text from the document.');
            }

            $document->update(['text_extraction' => $text]);
        } catch (\Exception $e) {
            return $this->handleError($document, 'Text extraction failed: ' . $e->getMessage());
        }

        return $next($document);
    }

    private function handleError(Document $document, string $errorMessage): void
    {
        Log::error($errorMessage, ['document_id' => $document->id]);
        $document->update([
            'status' => Status::ERRORED,
            'error' => $errorMessage,
        ]);
    }
}
