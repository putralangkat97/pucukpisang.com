<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>pucukpisang.ai</title>
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
        />
        <style>
            body {
                background-color: #f8f9fa;
            }
            .container {
                max-width: 800px;
            }
            .card {
                margin-top: 2rem;
            }
            .results-card {
                border-left: 5px solid #0d6efd;
            }
            .spinner-border {
                display: none;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="text-center mt-5">
                <h1 class="items-center">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="44"
                        height="44"
                        viewBox="0 0 32 32"
                        class="mb-2"
                    >
                        <path
                            fill="#0288d1"
                            d="M28 10a4 4 0 0 0-4-4h-6v6a6 6 0 0 1-6 6h-2v2h2v6h4v-6h8v6h4V16h2v-4a2 2 0 0 0-2-2"
                        />
                        <path
                            fill="#0288d1"
                            d="M12 4H8v2a6 6 0 0 0-6 6v6a2 2 0 0 0 2 2v2H2.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5H6a2 2 0 0 0 2-2v-8h4a4 4 0 0 0 4-4V8a4 4 0 0 0-4-4M6 14H4v-2h2Z"
                        />
                    </svg>
                    {{ $titlePage ?? 'title page here' }}
                </h1>
                <p class="lead">
                    {{ $description ?? "description page here" }}
                </p>
            </div>

            {{ $slot }}
        </div>

        @stack('scripts')
    </body>
</html>
