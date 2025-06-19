<x-app-layout>
    <x-slot name="titlePage">Document Processor</x-slot>
    <x-slot name="description">Upload a PDF or Image to translate or summarize content.</x-slot>

    <div class="card p-4">
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form
            action="{{ route('document.process') }}"
            method="POST"
            enctype="multipart/form-data"
            id="process-form"
        >
            @csrf
            <div class="mb-3">
                <label for="document" class="form-label">
                    <b>1. Upload Document (PDF or Image)</b>
                </label>
                <input
                    type="file"
                    class="form-control"
                    name="document"
                    id="document"
                    required
                />
            </div>

            <div class="mb-3">
                <label class="form-label">
                    <b>2. Choose Operations</b>
                </label>
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="operations[]"
                        value="summarize"
                        id="op_summarize"
                    />
                    <label class="form-check-label" for="op_summarize">
                        Summarize
                    </label>
                </div>
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="operations[]"
                        value="translate"
                        id="op_translate"
                    />
                    <label class="form-check-label" for="op_translate">
                        Translate
                    </label>
                </div>
            </div>

            <div id="summarize_options" class="mb-3" style="display: none">
                <label for="summary_length" class="form-label">
                    <b>Summarization Length</b>
                </label>
                <select
                    name="summary_length"
                    id="summary_length"
                    class="form-select"
                >
                    <option value="short">Short (a few sentences)</option>
                    <option value="medium">Medium (a paragraph)</option>
                    <option value="long">Long (multiple paragraphs)</option>
                </select>
            </div>

            <div id="translate_options" class="mb-3" style="display: none">
                <label for="target_language" class="form-label">
                    <b>Translate To</b>
                </label>
                <select
                    name="target_language"
                    id="target_language"
                    class="form-select"
                >
                    <option value="" selected disabled>-- choose --</option>
                    <option value="German">🇩🇪 German</option>
                    <option value="Spanish">🇪🇸 Spanish</option>
                    <option value="Chinese">🇨🇳 Chinese</option>
                    <option value="Japanese">🇯🇵 Japanese</option>
                    <option value="Indonesia">🇮🇩 Indonesia</option>
                    <option value="English">🇬🇧 English</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="ai_model" class="form-label">
                    <b>3. Choose AI Model</b>
                </label>
                <select name="ai_model" id="ai_model" class="form-select">
                    <option value="" selected disabled>-- choose model --</option>
                    <option value="gemini">Google (Gemini 2.5 Flash) ✅</option>
                    <option value="openai">OpenAI (GPT-3.5) (lom bisa) ❌</option>
                    <option value="deepseek">DeepSeek (lom bisa) ❌</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <span
                    class="spinner-border spinner-border-sm"
                    role="status"
                    aria-hidden="true"
                ></span>
                Process Document
            </button>
        </form>
    </div>

    @if(isset($results))
    <div class="card results-card p-4">
        <h2 class="mb-3">Results</h2>

        @if(isset($results['summary']))
        <div class="mb-4">
            <h4>
                Summary @if(isset($results['summary_tokens']) &&
                $results['summary_tokens'] > 0)
                <span class="badge bg-info fw-normal ms-2">
                    {{ $results["summary_tokens"] }} tokens
                </span>
                @endif
            </h4>
            <p class="text-muted" style="white-space: pre-wrap">
                {{ $results["summary"] }}
            </p>
        </div>
        @endif

        @if(isset($results['translation']))
        <div class="mb-4">
            <h4>
                Translation (to {{ $results["language"] }})
                @if(isset($results['translation_tokens']) &&
                $results['translation_tokens'] > 0)
                <span class="badge bg-info fw-normal ms-2">
                    {{ $results["translation_tokens"] }} tokens
                </span>
                @endif
            </h4>
            <p class="text-muted" style="white-space: pre-wrap">
                {{ $results["translation"] }}
            </p>
        </div>
        @endif

        @if(isset($results['total_tokens']) && $results['total_tokens'] > 0)
        <hr />
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Total Tokens Used</h5>
            <span class="badge bg-primary fs-6">
                {{ $results["total_tokens"] }}
            </span>
        </div>
        @endif

        <hr />
        <div class="accordion" id="accordionOriginal">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button
                        class="accordion-button collapsed"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapseOne"
                        aria-expanded="false"
                        aria-controls="collapseOne"
                    >
                        View Original Extracted Text
                    </button>
                </h2>
                <div
                    id="collapseOne"
                    class="accordion-collapse collapse"
                    aria-labelledby="headingOne"
                    data-bs-parent="#accordionOriginal"
                >
                    <div class="accordion-body" style="white-space: pre-wrap">
                        {{ $originalText }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const summarizeCheckbox = document.getElementById("op_summarize");
            const translateCheckbox = document.getElementById("op_translate");
            const summarizeOptions = document.getElementById("summarize_options");
            const translateOptions = document.getElementById("translate_options");

            function toggleOptions() {
                summarizeOptions.style.display = summarizeCheckbox.checked
                    ? "block"
                    : "none";
                translateOptions.style.display = translateCheckbox.checked
                    ? "block"
                    : "none";
            }

            summarizeCheckbox.addEventListener("change", toggleOptions);
            translateCheckbox.addEventListener("change", toggleOptions);

            // Show spinner on form submission
            const form = document.getElementById("process-form");
            form.addEventListener("submit", function () {
                const button = form.querySelector('button[type="submit"]');
                button.disabled = true;
                button.querySelector(".spinner-border").style.display =
                    "inline-block";
            });

            toggleOptions(); // Initial check
        });
    </script>
    @endpush
</x-app-layout>
