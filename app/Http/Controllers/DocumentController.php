<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AIFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\PdfToText\Pdf;
use thiagoalessio\TesseractOCR\TesseractOCR;

class DocumentController extends Controller
{
    /**
     * Display the upload form.
     */
    public function index()
    {
        return view('documents.create');
    }

    /**
     * Process the uploaded document.
     */
    public function process(Request $request)
    {
        /**
         * 1. Validate the request
         */
        $validated = $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'operations' => 'required|array|min:1',
            'operations.*' => 'in:summarize,translate',
            'summary_length' => 'required_if:operations,summarize|in:short,medium,long',
            'target_language' => 'required_if:operations,translate|in:German,Spanish,Chinese,Japanese,Indonesia,English',
            'ai_model' => 'required|in:openai,gemini,deepseek',
        ]);

        if ($validated) {
            $file = $request->file('document');
            $text = '';

            try {
                /**
                 * 2. Extract the text from the PDF or Image
                 */
                $extension = strtolower($file->getClientOriginalExtension());
                if ($extension === 'pdf') {
                    $text = (new Pdf(env('PDFTOTEXT_PATH')))->setPdf($file->getPathname())->text();
                } else {
                    $ocr = new TesseractOCR($file->getPathname());

                    if (env('TESSERACT_PATH')) {
                        $ocr->executable(env('TESSERACT_PATH'));
                    }

                    $text = $ocr->run();
                }

                if (empty(trim($text))) {
                    return back()->withErrors(['document' => 'Could not extract any text from the document.']);
                }
            } catch (\Exception $e) {
                Log::error('Text extraction failed: ' . $e->getMessage());
                return back()->withErrors(['document' => 'Failed to process the document. Error: ' . $e->getMessage()]);
            }

            /**
             * 3. Perform AI operations using the selected model
             */
            $results = [];
            $total_tokens = 0;
            $operations = $request->input('operations');
            $model = $request->input('ai_model');

            $text_for_translation = $text;
            if (in_array('summarize', $operations)) {
                $length = $request->input('summary_length');
                // The summary prompt always uses the full original text.
                $prompt = "Summarize the following text in a {$length} format:\n\n{$text}";
                $ai_model = AIFactory::create($model);
                $ai_response = $ai_model->call($prompt);

                $results['summary'] = $ai_response['text'];
                $results['summary_tokens'] = $ai_response['tokens'];
                $total_tokens += $ai_response['tokens'];

                // Update the variable for the next step with the summary result.
                $text_for_translation = $results['summary'];
            }

            if (in_array('translate', $operations)) {
                $language = $request->input('target_language');

                // This prompt now uses $text_for_translation. It will contain either the
                // original text if no summary was done or summarize text.
                $prompt = "Translate the following text into {$language}:\n\n{$text_for_translation}";

                $ai_model = AIFactory::create($model);
                $ai_response = $ai_model->call($prompt);

                $results['translation'] = $ai_response['text'];
                $results['translation_tokens'] = $ai_response['tokens'];
                $results['language'] = $language;
                $total_tokens += $ai_response['tokens'];
            }

            $results['total_tokens'] = $total_tokens;

            /**
             * 4. Return the view with results
             */
            return view('documents.create', [
                'results' => $results,
                'originalText' => $text,
            ]);
        }
    }
}
