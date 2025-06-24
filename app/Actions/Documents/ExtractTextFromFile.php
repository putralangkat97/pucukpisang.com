<?php

namespace App\Actions\Documents;

use App\Enums\Status;
use App\Models\Document;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\PdfToText\Pdf;
use thiagoalessio\TesseractOCR\TesseractOCR;

class ExtractTextFromFile
{
    public function handle(array $payload, \Closure $next)
    {
        /** @var Document $document */
        $document = $payload['model'];
        $local_path_for_processing = null;
        $delete_temp_file = false;
        try {
            $document->update(['status' => Status::EXTRACTING_DOCUMENT]);

            $default_disk = config('filesystems.default');
            if ($default_disk === 'local') {
                $local_path_for_processing = Storage::disk('local')->path($document->file);
                $delete_temp_file = false;
            } else {
                $remote_file = Storage::disk($default_disk)->get($document->file);
                if ($remote_file === null) {
                    throw new \Exception("File '{$document->file}' not found in '{$default_disk}' bucket.");
                }

                $temp_path_on_local_disk = 'temp_processing/' . basename($document->file);
                Storage::disk('local')->put($temp_path_on_local_disk, $remote_file);

                $local_path_for_processing = Storage::disk('local')->path($temp_path_on_local_disk);
                $delete_temp_file = true;
            }

            if (!file_exists($local_path_for_processing)) {
                throw new \Exception("Local file for processing not found at: {$local_path_for_processing}");
            }

            $text = match ($document->type) {
                'pdf'   => (new Pdf(env('PDFTOTEXT_PATH')))->setPdf($local_path_for_processing)->text(),
                default => (new TesseractOCR($local_path_for_processing))->executable(env('TESSERACT_PATH', 'tesseract'))->run(),
            };

            if (empty(trim($text))) {
                throw new \Exception('Could not extract any text from the document.');
            }

            $payload['text_for_ai'] = $text;
            $document->update(['text_extraction' => $text]);

        } catch (\Exception $e) {
            $this->handleError($document, 'Text extraction failed: ' . $e->getMessage());
            return;
        } finally {
            if ($delete_temp_file && $local_path_for_processing && file_exists($local_path_for_processing)) {
                unlink($local_path_for_processing);
            }
        }

        return $next($payload);
    }

    private function handleError(Document $document, string $error_message): void
    {
        Log::error($error_message, ['document_id' => $document->id]);
        $document->update([
            'status' => Status::ERRORED,
            'error' => $error_message,
        ]);
    }
}
