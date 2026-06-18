<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Storage;

class SystemFaviconController extends Controller
{
    public function __invoke()
    {
        $settings = SystemSetting::current();
        $logoPath = $settings->logo_path;

        if (! $logoPath || ! Storage::disk('public')->exists($logoPath)) {
            return response()->file(public_path('images/central-leads-favicon.svg'), [
                'Cache-Control' => 'public, max-age=300',
                'Content-Type' => 'image/svg+xml',
            ]);
        }

        $path = Storage::disk('public')->path($logoPath);
        $mime = mime_content_type($path) ?: 'image/png';

        if (class_exists(\Imagick::class)) {
            $content = $this->makeFaviconWithImagick($path);

            if ($content) {
                return response($content, 200, [
                    'Cache-Control' => 'public, max-age=300',
                    'Content-Type' => 'image/png',
                ]);
            }
        }

        if (! function_exists('imagecreatetruecolor')) {
            return response()->file($path, [
                'Cache-Control' => 'public, max-age=300',
                'Content-Type' => $mime,
            ]);
        }

        $source = $this->makeImage($path, $mime);

        if (! $source) {
            return response()->file($path, [
                'Cache-Control' => 'public, max-age=300',
                'Content-Type' => $mime,
            ]);
        }

        imagesavealpha($source, true);

        [$cropX, $cropY, $cropWidth, $cropHeight] = $this->transparentBounds($source);
        $size = 96;
        $padding = 6;
        $canvas = imagecreatetruecolor($size, $size);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagefill($canvas, 0, 0, imagecolorallocatealpha($canvas, 0, 0, 0, 127));

        $scale = min(($size - ($padding * 2)) / $cropWidth, ($size - ($padding * 2)) / $cropHeight);
        $targetWidth = max(1, (int) round($cropWidth * $scale));
        $targetHeight = max(1, (int) round($cropHeight * $scale));
        $targetX = (int) floor(($size - $targetWidth) / 2);
        $targetY = (int) floor(($size - $targetHeight) / 2);

        imagecopyresampled(
            $canvas,
            $source,
            $targetX,
            $targetY,
            $cropX,
            $cropY,
            $targetWidth,
            $targetHeight,
            $cropWidth,
            $cropHeight,
        );

        ob_start();
        imagepng($canvas);
        $content = (string) ob_get_clean();

        imagedestroy($source);
        imagedestroy($canvas);

        return response($content, 200, [
            'Cache-Control' => 'public, max-age=300',
            'Content-Type' => 'image/png',
        ]);
    }

    private function makeFaviconWithImagick(string $path): ?string
    {
        try {
            $image = new \Imagick($path);
            $image->setImageFormat('png');
            $image->setImageAlphaChannel(\Imagick::ALPHACHANNEL_SET);
            $image->trimImage(0);
            $image->setImagePage(0, 0, 0, 0);
            $image->thumbnailImage(84, 84, true);

            $canvas = new \Imagick();
            $canvas->newImage(96, 96, new \ImagickPixel('transparent'), 'png');

            $x = (int) floor((96 - $image->getImageWidth()) / 2);
            $y = (int) floor((96 - $image->getImageHeight()) / 2);
            $canvas->compositeImage($image, \Imagick::COMPOSITE_OVER, $x, $y);

            return $canvas->getImagesBlob();
        } catch (\Throwable) {
            return null;
        }
    }

    private function makeImage(string $path, string $mime): mixed
    {
        return match ($mime) {
            'image/png' => function_exists('imagecreatefrompng') ? @imagecreatefrompng($path) : false,
            'image/jpeg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($path) : false,
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };
    }

    private function transparentBounds(mixed $image): array
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $minX = $width;
        $minY = $height;
        $maxX = 0;
        $maxY = 0;

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgba = imagecolorat($image, $x, $y);
                $alpha = ($rgba & 0x7F000000) >> 24;

                if ($alpha < 120) {
                    $minX = min($minX, $x);
                    $minY = min($minY, $y);
                    $maxX = max($maxX, $x);
                    $maxY = max($maxY, $y);
                }
            }
        }

        if ($minX > $maxX || $minY > $maxY) {
            return [0, 0, $width, $height];
        }

        return [$minX, $minY, $maxX - $minX + 1, $maxY - $minY + 1];
    }
}
