<?php

namespace App\Support;

class SystemImageRenderer
{
    public static function makeTrimmedPng(string $path, int $width, int $height, int $padding = 8): ?string
    {
        if (class_exists(\Imagick::class)) {
            return self::makeWithImagick($path, $width, $height, $padding);
        }

        if (function_exists('imagecreatetruecolor')) {
            return self::makeWithGd($path, $width, $height, $padding);
        }

        return null;
    }

    private static function makeWithImagick(string $path, int $width, int $height, int $padding): ?string
    {
        try {
            $image = new \Imagick($path);
            $image->setImageFormat('png');
            $image->setImageAlphaChannel(\Imagick::ALPHACHANNEL_SET);
            $image->trimImage(0);
            $image->setImagePage(0, 0, 0, 0);
            $image->thumbnailImage($width - ($padding * 2), $height - ($padding * 2), true);

            $canvas = new \Imagick();
            $canvas->newImage($width, $height, new \ImagickPixel('transparent'), 'png');

            $x = (int) floor(($width - $image->getImageWidth()) / 2);
            $y = (int) floor(($height - $image->getImageHeight()) / 2);
            $canvas->compositeImage($image, \Imagick::COMPOSITE_OVER, $x, $y);

            return $canvas->getImagesBlob();
        } catch (\Throwable) {
            return null;
        }
    }

    private static function makeWithGd(string $path, int $width, int $height, int $padding): ?string
    {
        $mime = mime_content_type($path) ?: '';
        $source = match ($mime) {
            'image/png' => function_exists('imagecreatefrompng') ? @imagecreatefrompng($path) : false,
            'image/jpeg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($path) : false,
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };

        if (! $source) {
            return null;
        }

        imagesavealpha($source, true);
        [$cropX, $cropY, $cropWidth, $cropHeight] = self::transparentBounds($source);

        $canvas = imagecreatetruecolor($width, $height);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagefill($canvas, 0, 0, imagecolorallocatealpha($canvas, 0, 0, 0, 127));

        $scale = min(($width - ($padding * 2)) / $cropWidth, ($height - ($padding * 2)) / $cropHeight);
        $targetWidth = max(1, (int) round($cropWidth * $scale));
        $targetHeight = max(1, (int) round($cropHeight * $scale));
        $targetX = (int) floor(($width - $targetWidth) / 2);
        $targetY = (int) floor(($height - $targetHeight) / 2);

        imagecopyresampled($canvas, $source, $targetX, $targetY, $cropX, $cropY, $targetWidth, $targetHeight, $cropWidth, $cropHeight);

        ob_start();
        imagepng($canvas);
        $content = (string) ob_get_clean();

        imagedestroy($source);
        imagedestroy($canvas);

        return $content;
    }

    private static function transparentBounds(mixed $image): array
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
