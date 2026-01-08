<?php

namespace App\Services;

use RuntimeException;
use Symfony\Component\Process\Process;

class KhmerOCRService
{
    public function recognize(string $imagePath): array
    {
        $repoPath = config('khmer-ocr.repo_path');

        if (!$repoPath || !is_dir($repoPath)) {
            throw new RuntimeException('KhmerOCR repo path is not configured. Set KHMER_OCR_REPO_PATH.');
        }

        $python = config('khmer-ocr.python_bin', 'python');

        $scriptPath = base_path('python/khmer_ocr_multi.py');
        if (!is_file($scriptPath)) {
            throw new RuntimeException('KhmerOCR multi-line script was not found.');
        }

        $systemRoot = getenv('SystemRoot') ?: getenv('SYSTEMROOT') ?: getenv('WINDIR');
        if (!is_string($systemRoot) || $systemRoot === '') {
            $systemRoot = 'C:\\Windows';
        }

        $systemDrive = getenv('SystemDrive');
        if (!is_string($systemDrive) || $systemDrive === '') {
            $systemDrive = substr($systemRoot, 0, 2);
        }

        $env = getenv();
        if (!is_array($env)) {
            $env = [];
        }

        $env['PYTHONIOENCODING'] = 'utf-8';
        $env['PYTHONHASHSEED'] = '0';
        $env['SystemRoot'] = $systemRoot;
        $env['SYSTEMROOT'] = $systemRoot;
        $env['WINDIR'] = $systemRoot;
        $env['SystemDrive'] = $systemDrive;

        $pythonPath = isset($env['PYTHONPATH']) && $env['PYTHONPATH'] !== ''
            ? $repoPath . PATH_SEPARATOR . $env['PYTHONPATH']
            : $repoPath;
        $env['PYTHONPATH'] = $pythonPath;

        $process = new Process([$python, $scriptPath, $imagePath], $repoPath, $env);
        $process->setTimeout((int) config('khmer-ocr.timeout', 60));
        $process->run();

        if (!$process->isSuccessful()) {
            $error = trim($process->getErrorOutput());
            throw new RuntimeException($error !== '' ? $error : 'KhmerOCR failed to run.');
        }

        $output = trim($process->getOutput());

        try {
            return json_decode($output, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new RuntimeException('KhmerOCR returned invalid JSON.', 0, $exception);
        }
    }
}
