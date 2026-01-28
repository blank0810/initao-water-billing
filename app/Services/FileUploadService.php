<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Base directory for uploads within the public folder.
     */
    private const UPLOAD_BASE_DIR = 'uploads';

    /**
     * Process and store a base64 encoded image.
     *
     * @param  string  $base64Data  Base64 encoded image data (with or without data URI prefix)
     * @param  string  $directory  Directory within the uploads folder
     * @param  string|null  $filename  Optional custom filename (without extension)
     * @return array{success: bool, path?: string, url?: string, error?: string}
     */
    public function storeBase64Image(string $base64Data, string $directory, ?string $filename = null): array
    {
        Log::debug('FileUploadService: Starting base64 image upload', [
            'directory' => $directory,
            'filename' => $filename,
            'base64_length' => strlen($base64Data),
            'base64_prefix' => substr($base64Data, 0, 50),
        ]);

        // Validate and extract image data
        $imageInfo = $this->parseBase64Image($base64Data);

        if (! $imageInfo['success']) {
            Log::error('FileUploadService: Failed to parse base64 image', [
                'error' => $imageInfo['error'] ?? 'Unknown parse error',
            ]);

            return $imageInfo;
        }

        // Generate unique filename
        $filename = $filename ?? Str::uuid()->toString();
        $fullFilename = $filename.'.'.$imageInfo['extension'];

        // Build full path within public directory
        $relativePath = self::UPLOAD_BASE_DIR.'/'.$directory.'/'.$fullFilename;
        $fullPath = public_path($relativePath);
        $directoryPath = dirname($fullPath);

        try {
            Log::debug('FileUploadService: Attempting to save file', [
                'full_path' => $fullPath,
                'directory_path' => $directoryPath,
                'relative_path' => $relativePath,
                'data_size' => strlen($imageInfo['data']),
            ]);

            // Ensure directory exists
            if (! File::isDirectory($directoryPath)) {
                File::makeDirectory($directoryPath, 0755, true);
                Log::debug('FileUploadService: Created directory', ['path' => $directoryPath]);
            }

            // Save the file
            File::put($fullPath, $imageInfo['data']);

            Log::info('FileUploadService: File saved successfully', [
                'path' => $relativePath,
                'full_path' => $fullPath,
            ]);

            return [
                'success' => true,
                'path' => $relativePath,
                'url' => asset($relativePath),
            ];
        } catch (\Exception $e) {
            Log::error('FileUploadService: Exception saving file', [
                'error' => $e->getMessage(),
                'full_path' => $fullPath,
            ]);

            return [
                'success' => false,
                'error' => 'Failed to save image: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Parse and validate base64 image data.
     */
    private function parseBase64Image(string $base64Data): array
    {
        // Handle data URI format (e.g., "data:image/jpeg;base64,/9j/4AAQ...")
        if (preg_match('/^data:image\/(\w+);base64,(.+)$/', $base64Data, $matches)) {
            $extension = $this->normalizeExtension($matches[1]);
            $data = base64_decode($matches[2], true);
        } else {
            // Assume raw base64 data, try to detect format
            $data = base64_decode($base64Data, true);
            $extension = $this->detectImageExtension($data);
        }

        if ($data === false) {
            return ['success' => false, 'error' => 'Invalid base64 encoding'];
        }

        if (! $extension) {
            return ['success' => false, 'error' => 'Unable to determine image format'];
        }

        // Validate allowed extensions
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (! in_array($extension, $allowedExtensions)) {
            return ['success' => false, 'error' => 'Unsupported image format: '.$extension];
        }

        // Validate file size (max 10MB)
        $maxSize = 10 * 1024 * 1024; // 10MB
        if (strlen($data) > $maxSize) {
            return ['success' => false, 'error' => 'Image exceeds maximum size of 10MB'];
        }

        return [
            'success' => true,
            'data' => $data,
            'extension' => $extension,
        ];
    }

    /**
     * Detect image extension from binary data.
     */
    private function detectImageExtension(?string $data): ?string
    {
        if (! $data || strlen($data) < 12) {
            return null;
        }

        // Check magic bytes
        $signatures = [
            'jpg' => ["\xFF\xD8\xFF"],
            'png' => ["\x89PNG\r\n\x1A\n"],
            'gif' => ['GIF87a', 'GIF89a'],
            'webp' => ['RIFF'],
        ];

        foreach ($signatures as $ext => $sigs) {
            foreach ($sigs as $sig) {
                if (str_starts_with($data, $sig)) {
                    // Additional check for WebP
                    if ($ext === 'webp' && ! str_contains(substr($data, 0, 12), 'WEBP')) {
                        continue;
                    }

                    return $ext;
                }
            }
        }

        return null;
    }

    /**
     * Normalize image extension.
     */
    private function normalizeExtension(string $ext): string
    {
        $ext = strtolower($ext);

        return $ext === 'jpeg' ? 'jpg' : $ext;
    }

    /**
     * Delete a file from the public directory.
     */
    public function deleteFile(string $path): bool
    {
        $fullPath = public_path($path);

        if (File::exists($fullPath)) {
            return File::delete($fullPath);
        }

        return true;
    }
}
