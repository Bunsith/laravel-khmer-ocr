<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KhmerOCR</title>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700&family=IBM+Plex+Mono:wght@400;600&display=swap");

        :root {
            --bg: #0b1120;
            --bg-soft: #121a33;
            --card: #111827;
            --card-soft: #1f2937;
            --accent: #f97316;
            --accent-soft: #fdba74;
            --text: #f8fafc;
            --muted: #cbd5f5;
            --border: rgba(148, 163, 184, 0.2);
            --shadow: 0 20px 60px rgba(15, 23, 42, 0.55);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Space Grotesk", "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(1200px 600px at 10% -10%, rgba(249, 115, 22, 0.3), transparent 60%),
                radial-gradient(900px 500px at 90% 10%, rgba(56, 189, 248, 0.25), transparent 55%),
                linear-gradient(180deg, var(--bg), var(--bg-soft));
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1.5rem;
        }

        main {
            width: min(920px, 100%);
            display: grid;
            gap: 1.5rem;
        }

        .hero {
            background: rgba(15, 23, 42, 0.65);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2rem 2.25rem;
            box-shadow: var(--shadow);
            backdrop-filter: blur(8px);
            animation: rise 0.6s ease-out both;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #fff7ed;
            background: rgba(249, 115, 22, 0.2);
            border: 1px solid rgba(249, 115, 22, 0.45);
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
        }

        h1 {
            font-size: clamp(2rem, 4vw, 3rem);
            margin: 0.75rem 0 0.5rem;
            letter-spacing: -0.02em;
        }

        p {
            margin: 0;
            color: var(--muted);
            font-size: 1rem;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 1.75rem 2rem;
            box-shadow: var(--shadow);
            animation: rise 0.6s ease-out 0.08s both;
        }

        .section-title {
            font-size: 1.2rem;
            margin: 0 0 0.75rem;
        }

        .result-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .result-header h2 {
            margin: 0;
        }

        .result-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        form {
            display: grid;
            gap: 1.25rem;
        }

        .field {
            display: grid;
            gap: 0.5rem;
        }

        .label {
            font-weight: 600;
        }

        input[type="file"] {
            width: 100%;
            padding: 0.85rem 1rem;
            border-radius: 12px;
            border: 1px dashed rgba(148, 163, 184, 0.5);
            background: rgba(15, 23, 42, 0.6);
            color: var(--text);
        }

        input[type="file"]::file-selector-button {
            margin-right: 1rem;
            padding: 0.6rem 1rem;
            border-radius: 10px;
            border: 1px solid rgba(249, 115, 22, 0.6);
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.85), rgba(251, 146, 60, 0.85));
            color: #fff7ed;
            font-weight: 600;
            cursor: pointer;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1rem;
        }

        .preview {
            border-radius: 14px;
            border: 1px solid rgba(148, 163, 184, 0.2);
            background: rgba(15, 23, 42, 0.4);
            padding: 1rem;
            display: grid;
            gap: 0.75rem;
        }

        .preview-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
        }

        .preview-frame {
            position: relative;
            border-radius: 12px;
            background: rgba(11, 17, 32, 0.7);
            border: 1px dashed rgba(148, 163, 184, 0.3);
            padding: 1rem;
            min-height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .preview-frame img {
            max-width: 100%;
            max-height: 320px;
            border-radius: 10px;
            display: none;
        }

        .preview-placeholder {
            color: rgba(203, 213, 245, 0.7);
            text-align: center;
            font-size: 0.95rem;
        }

        .preview.has-image img {
            display: block;
        }

        .preview.has-image .preview-placeholder {
            display: none;
        }

        .btn {
            padding: 0.75rem 1.4rem;
            border-radius: 999px;
            border: none;
            font-weight: 700;
            letter-spacing: 0.02em;
            background: linear-gradient(135deg, var(--accent), var(--accent-soft));
            color: #0b1120;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 12px 25px rgba(249, 115, 22, 0.35);
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn.ghost {
            background: rgba(15, 23, 42, 0.6);
            color: var(--text);
            border: 1px solid rgba(148, 163, 184, 0.3);
            box-shadow: none;
        }

        .btn.ghost:hover {
            border-color: rgba(249, 115, 22, 0.6);
        }

        .hint {
            font-size: 0.9rem;
            color: var(--muted);
        }

        .alert {
            margin-top: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            background: rgba(248, 113, 113, 0.12);
            border: 1px solid rgba(248, 113, 113, 0.45);
            color: #fecaca;
        }

        .result-output {
            margin-top: 1rem;
            padding: 1.25rem 1.5rem;
            border-radius: 14px;
            border: 1px solid rgba(148, 163, 184, 0.2);
            background: rgba(15, 23, 42, 0.5);
            line-height: 1.7;
            font-size: 1.05rem;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .result-image {
            margin-top: 1rem;
            border-radius: 14px;
            border: 1px solid rgba(148, 163, 184, 0.2);
            background: rgba(15, 23, 42, 0.5);
            overflow: hidden;
        }

        .result-image img {
            width: 100%;
            display: block;
            object-fit: contain;
            max-height: 360px;
        }

        .result-block {
            background: var(--card-soft);
            border-radius: 14px;
            padding: 1rem 1.25rem;
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        .meta {
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.7rem;
            color: var(--muted);
        }

        .text-output {
            margin-top: 0.5rem;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .mono {
            font-family: "IBM Plex Mono", ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: 0.95rem;
            color: #e2e8f0;
        }

        details {
            margin-top: 1rem;
        }

        summary {
            cursor: pointer;
            color: var(--muted);
        }

        pre {
            background: #0b1120;
            color: #e2e8f0;
            padding: 1rem;
            border-radius: 12px;
            overflow-x: auto;
            border: 1px solid rgba(148, 163, 184, 0.2);
            font-family: "IBM Plex Mono", ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        }

        @keyframes rise {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 720px) {
            .hero,
            .card {
                padding: 1.5rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .hero,
            .card {
                animation: none;
            }
            .btn {
                transition: none;
            }
        }
    </style>
</head>
<body>
    <main>
        <header class="hero">
            <span class="badge">Multi-line OCR</span>
            <h1>KhmerOCR</h1>
            <p>Upload Khmer text images to extract readable lines with basic line detection.</p>
        </header>

        <section class="card">
            <h2 class="section-title">Upload</h2>
            <form method="POST" action="{{ route('ocr.recognize') }}" enctype="multipart/form-data">
                @csrf
                <label class="field">
                    <span class="label">Image file</span>
                    <input id="image-input" type="file" name="image" accept="image/*" required>
                </label>
                <div class="preview {{ isset($image_url) ? 'has-image' : '' }}" id="image-preview">
                    <span class="preview-label">Preview</span>
                    <div class="preview-frame">
                        <img
                            id="preview-image"
                            alt="Selected image preview"
                            data-initial-src="{{ $image_url ?? '' }}"
                            @isset($image_url) src="{{ $image_url }}" @endisset
                        >
                        <div class="preview-placeholder">Select an image to see a preview here.</div>
                    </div>
                </div>
                <div class="actions">
                    <button class="btn" type="submit">Recognize</button>
                    <span class="hint">Single or multi-line. JPG or PNG. Max 10 MB.</span>
                </div>
            </form>

            @if ($errors->any())
                <div class="alert">
                    <p>{{ $errors->first() }}</p>
                </div>
            @endif

            @isset($result)
                <div class="result-header">
                    <h2 class="section-title">Result</h2>
                    <div class="result-actions">
                        <button class="btn ghost" type="button" data-copy="text">Copy text</button>
                    </div>
                </div>
                <div class="result-output">{{ $result['text'] ?? '' }}</div>
            @endisset
        </section>
    </main>

    @isset($result)
        <script>
            (function () {
                const payload = {
                    text: @json($result['text'] ?? ''),
                };

                const buttons = document.querySelectorAll('[data-copy]');
                const copyText = async (value) => {
                    if (navigator.clipboard && window.isSecureContext) {
                        await navigator.clipboard.writeText(value);
                        return;
                    }
                    const textarea = document.createElement('textarea');
                    textarea.value = value;
                    textarea.style.position = 'fixed';
                    textarea.style.left = '-9999px';
                    document.body.appendChild(textarea);
                    textarea.select();
                    document.execCommand('copy');
                    textarea.remove();
                };

                buttons.forEach((button) => {
                    button.addEventListener('click', async () => {
                        const value = payload.text;
                        try {
                            await copyText(value);
                            const original = button.textContent;
                            button.textContent = 'Copied';
                            setTimeout(() => {
                                button.textContent = original;
                            }, 1400);
                        } catch (err) {
                            button.textContent = 'Copy failed';
                        }
                    });
                });
            })();
        </script>
    @endisset

    <script>
        (function () {
            const input = document.getElementById('image-input');
            const preview = document.getElementById('image-preview');
            const image = document.getElementById('preview-image');
            if (!input || !preview || !image) {
                return;
            }

            let currentUrl = null;
            const initialSrc = image.dataset.initialSrc;
            input.addEventListener('change', () => {
                const file = input.files && input.files[0];
                if (!file) {
                    if (currentUrl) {
                        URL.revokeObjectURL(currentUrl);
                        currentUrl = null;
                    }
                    if (initialSrc) {
                        image.src = initialSrc;
                        preview.classList.add('has-image');
                    } else {
                        preview.classList.remove('has-image');
                        image.removeAttribute('src');
                    }
                    return;
                }

                if (currentUrl) {
                    URL.revokeObjectURL(currentUrl);
                }
                currentUrl = URL.createObjectURL(file);
                image.src = currentUrl;
                preview.classList.add('has-image');
            });
        })();
    </script>
</body>
</html>
