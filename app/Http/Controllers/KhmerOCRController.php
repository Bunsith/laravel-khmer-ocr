<?php

namespace App\Http\Controllers;

use App\Services\KhmerOCRService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KhmerOCRController extends Controller
{
    public function show(Request $request)
    {
        return view('ocr', [
            'result' => $request->session()->get('result'),
            'image_url' => $request->session()->get('image_url'),
        ]);
    }

    public function recognize(Request $request, KhmerOCRService $ocr)
    {
        $request->validate([
            'image' => ['required', 'image', 'max:10240'],
        ]);

        $disk = Storage::disk('public');
        $previousPath = $request->session()->get('last_ocr_image');
        if ($previousPath) {
            $disk->delete($previousPath);
        }

        $path = $request->file('image')->storePublicly('ocr', 'public');
        $fullPath = $disk->path($path);
        $realPath = realpath($fullPath);

        if ($realPath === false || !is_file($realPath)) {
            throw new \RuntimeException('Uploaded file was not saved to disk.');
        }

        try {
            $result = $ocr->recognize($realPath);
        } catch (\Throwable $exception) {
            $disk->delete($path);
            throw $exception;
        }

        $imageUrl = $disk->url($path);
        $request->session()->put('last_ocr_image', $path);

        if ($request->wantsJson()) {
            return response()->json(array_merge($result, [
                'image_url' => $imageUrl,
            ]));
        }

        return redirect()
            ->route('ocr.show')
            ->with('result', $result)
            ->with('image_url', $imageUrl);
    }
}
