<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Support\SystemImageRenderer;
use Illuminate\Support\Facades\Storage;

class SystemLogoController extends Controller
{
    public function __invoke()
    {
        $logoPath = SystemSetting::current()->logo_path;

        if (! $logoPath || ! Storage::disk('public')->exists($logoPath)) {
            return response()->file(public_path('images/central-leads-logo.svg'), [
                'Cache-Control' => 'public, max-age=300',
                'Content-Type' => 'image/svg+xml',
            ]);
        }

        $path = Storage::disk('public')->path($logoPath);
        $content = SystemImageRenderer::makeTrimmedPng($path, 420, 128, 10);

        if ($content) {
            return response($content, 200, [
                'Cache-Control' => 'public, max-age=300',
                'Content-Type' => 'image/png',
            ]);
        }

        return response()->file($path, [
            'Cache-Control' => 'public, max-age=300',
            'Content-Type' => mime_content_type($path) ?: 'image/png',
        ]);
    }
}
