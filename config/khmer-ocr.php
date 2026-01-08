<?php

return [
    'repo_path' => env('KHMER_OCR_REPO_PATH', base_path('_refs/KhmerOCR')),
    'python_bin' => env('KHMER_OCR_PYTHON', 'python'),
    'timeout' => (int) env('KHMER_OCR_TIMEOUT', 60),
];
