<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageOptimizer
{
    public static function process(UploadedFile $file, string $baseDir = 'products'): array
    {
        $ext = strtolower($file->getClientOriginalExtension());
        $image = match ($ext) {
            'png' => imagecreatefrompng($file->getRealPath()),
            'jpeg', 'jpg' => imagecreatefromjpeg($file->getRealPath()),
            default => imagecreatefromstring(file_get_contents($file->getRealPath())),
        };

        $width = imagesx($image);
        $height = imagesy($image);

        $largeW = 1200; $largeH = (int)round($height * ($largeW / $width));
        $thumbW = 600;  $thumbH = (int)round($height * ($thumbW / $width));

        $large = imagecreatetruecolor($largeW, $largeH);
        imagecopyresampled($large, $image, 0, 0, 0, 0, $largeW, $largeH, $width, $height);

        $thumb = imagecreatetruecolor($thumbW, $thumbH);
        imagecopyresampled($thumb, $image, 0, 0, 0, 0, $thumbW, $thumbH, $width, $height);

        $baseName = uniqid('p_', true);
        $paths = [
            'large_jpg' => $baseDir.'/'.$baseName.'_large.jpg',
            'thumb_jpg' => $baseDir.'/'.$baseName.'_thumb.jpg',
            'large_webp' => $baseDir.'/'.$baseName.'_large.webp',
            'thumb_webp' => $baseDir.'/'.$baseName.'_thumb.webp',
        ];

        ob_start(); imagejpeg($large, null, 82); $largeJpg = ob_get_clean();
        ob_start(); imagejpeg($thumb, null, 82); $thumbJpg = ob_get_clean();

        ob_start(); imagewebp($large, null, 82); $largeWebp = ob_get_clean();
        ob_start(); imagewebp($thumb, null, 82); $thumbWebp = ob_get_clean();

        Storage::disk('public')->put($paths['large_jpg'], $largeJpg);
        Storage::disk('public')->put($paths['thumb_jpg'], $thumbJpg);
        Storage::disk('public')->put($paths['large_webp'], $largeWebp);
        Storage::disk('public')->put($paths['thumb_webp'], $thumbWebp);

        imagedestroy($image); imagedestroy($large); imagedestroy($thumb);

        return array_map(fn($p) => '/storage/'.$p, $paths);
    }
}

