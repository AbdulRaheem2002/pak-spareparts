<?php
/**
 * PakAutoParts - Secure Image Processor (GD Only)
 * Validates via finfo MIME inspection, re-encodes through GD to eliminate malware payloads,
 * generates WebP optimized versions and responsive thumbnails.
 */

declare(strict_types=1);

namespace App\Core;

class ImageHandler
{
    private static array $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/pjpeg'=> 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    public static function validate(array $file): ?string
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            return 'Invalid upload parameter.';
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'File exceeded maximum size limit of 5MB.';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded.';
            default:
                return 'An unknown file upload error occurred.';
        }

        $maxSize = Config::get('uploads.max_file_size', 5242880);
        if ($file['size'] > $maxSize) {
            return 'File size cannot exceed 5MB.';
        }

        // Validate via finfo (do not trust client provided mime type)
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $realMime = $finfo->file($file['tmp_name']);

        if (!array_key_exists($realMime, self::$allowedMimes)) {
            return 'Invalid image type. Only JPG, PNG, and WebP are accepted.';
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExts = Config::get('uploads.allowed_exts', ['jpg', 'jpeg', 'png', 'webp']);
        if (!in_array($ext, $allowedExts, true)) {
            return 'Invalid file extension.';
        }

        // Check if actually valid image through getimagesize
        $imageSize = @getimagesize($file['tmp_name']);
        if ($imageSize === false) {
            return 'The uploaded file is not a valid image.';
        }

        return null; // Valid
    }

    public static function processUpload(array $file, string $subfolder = 'products', bool $createThumb = true): array
    {
        $validationError = self::validate($file);
        if ($validationError !== null) {
            throw new \InvalidArgumentException($validationError);
        }

        $targetDir = rtrim(Config::get('paths.uploads'), '/') . '/' . trim($subfolder, '/');
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        // Generate cryptographically random safe filename
        $uniqueId = bin2hex(random_bytes(16));
        $mainFileName = $uniqueId . '.webp';
        $thumbFileName = $uniqueId . '_thumb.webp';

        $mainPath = $targetDir . '/' . $mainFileName;
        $thumbPath = $targetDir . '/' . $thumbFileName;

        // Load image resource via GD
        $srcImage = self::createImageResource($file['tmp_name']);
        if (!$srcImage) {
            throw new \RuntimeException('Failed to read image source with GD.');
        }

        $origWidth = imagesx($srcImage);
        $origHeight = imagesy($srcImage);

        // 1. Process Large / Main Image
        $maxLargeW = (int)Config::get('uploads.large_width', 1000);
        $maxLargeH = (int)Config::get('uploads.large_height', 1000);
        $mainResource = self::resizeProportional($srcImage, $origWidth, $origHeight, $maxLargeW, $maxLargeH);

        // Save main image as WebP
        $quality = (int)Config::get('uploads.quality_webp', 82);
        imagewebp($mainResource, $mainPath, $quality);
        imagedestroy($mainResource);

        // 2. Process Thumbnail
        $thumbUrl = null;
        if ($createThumb) {
            $maxThumbW = (int)Config::get('uploads.thumb_width', 320);
            $maxThumbH = (int)Config::get('uploads.thumb_height', 320);
            $thumbResource = self::resizeProportional($srcImage, $origWidth, $origHeight, $maxThumbW, $maxThumbH);

            imagewebp($thumbResource, $thumbPath, $quality);
            imagedestroy($thumbResource);
            $thumbUrl = '/uploads/' . trim($subfolder, '/') . '/' . $thumbFileName;
        }

        imagedestroy($srcImage);

        return [
            'original_name' => $file['name'],
            'filename'      => $mainFileName,
            'url'           => '/uploads/' . trim($subfolder, '/') . '/' . $mainFileName,
            'thumbnail_url' => $thumbUrl ?? ('/uploads/' . trim($subfolder, '/') . '/' . $mainFileName),
            'path'          => $mainPath,
        ];
    }

    private static function createImageResource(string $filePath): \GdImage|false
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($filePath);

        return match ($mime) {
            'image/jpeg', 'image/pjpeg' => @imagecreatefromjpeg($filePath),
            'image/png'  => @imagecreatefrompng($filePath),
            'image/webp' => @imagecreatefromwebp($filePath),
            default      => false,
        };
    }

    private static function resizeProportional(\GdImage $src, int $srcW, int $srcH, int $maxW, int $maxH): \GdImage
    {
        $ratio = min($maxW / $srcW, $maxH / $srcH);
        
        // Don't upscale if smaller
        if ($ratio > 1.0) {
            $targetW = $srcW;
            $targetH = $srcH;
        } else {
            $targetW = (int)round($srcW * $ratio);
            $targetH = (int)round($srcH * $ratio);
        }

        $dst = imagecreatetruecolor($targetW, $targetH);

        // Preserve alpha transparency for PNG / WebP
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
        imagefilledrectangle($dst, 0, 0, $targetW, $targetH, $transparent);
        imagealphablending($dst, true);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $targetW, $targetH, $srcW, $srcH);

        return $dst;
    }

    public static function delete(string $relativeUrl): bool
    {
        if (empty($relativeUrl) || !str_starts_with($relativeUrl, '/uploads/')) {
            return false;
        }

        $baseDir = Config::get('paths.uploads');
        $filePath = rtrim($baseDir, '/') . '/' . ltrim(substr($relativeUrl, strlen('/uploads/')), '/');

        if (file_exists($filePath) && is_file($filePath)) {
            @unlink($filePath);

            // Also delete thumbnail if exists
            $thumbPath = preg_replace('/\.webp$/', '_thumb.webp', $filePath);
            if (file_exists($thumbPath) && is_file($thumbPath)) {
                @unlink($thumbPath);
            }
            return true;
        }

        return false;
    }
}

