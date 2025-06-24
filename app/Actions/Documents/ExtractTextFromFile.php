<?php

namespace App\Actions\Documents;

use App\Enums\Status;
use App\Models\Document;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\PdfToText\Pdf;
use thiagoalessio\TesseractOCR\TesseractOCR;

class ExtractTextFromFile
{
    public function handle(array $payload, Closure $next)
    {
        /** @var Document $document */
        $document = $payload['model'];

        try {
            $document->update(['status' => Status::EXTRACTING_DOCUMENT]);

            $r2_document_file = Storage::disk('r2')->get($document->file);
            if ($r2_document_file === null) {
                throw new \Exception("File '{$document->file}' not found in R2 bucket.");
            }

            $temp_path_local_disk = 'temp_processing/' . uniqid('doc_', true) . '.' . $document->type;
            Storage::disk('local')->put($temp_path_local_disk, $r2_document_file);
            $temp_local_path = Storage::disk('local')->path($temp_path_local_disk);

            $text = match ($document->type) {
                'pdf'   => (new Pdf(env('PDFTOTEXT_PATH')))->setPdf($temp_local_path)->text(),
                default => (new TesseractOCR($temp_local_path))->executable(env('TESSERACT_PATH', 'tesseract'))->run(),
            };

            if (empty(trim($text))) {
                throw new \Exception('Could not extract any text from the document.');
            }

            $document->update(['text_extraction' => $text]);

            Log::info("Deleting original file from R2: " . $document->file);
            Storage::disk('r2')->delete($document->file);
        } catch (\Exception $e) {
            return $this->handleError($document, 'Text extraction failed: ' . $e->getMessage());
            return;
        } finally {
            if ($temp_path_local_disk && Storage::disk('local')->exists($temp_path_local_disk)) {
                Storage::disk('local')->delete($temp_path_local_disk);
            }
        }

        $final_result = $next($payload);
        return $final_result;
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
