<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use UnexpectedValueException;

class CollegeMediaImporter
{
    private const DEFAULT_MEDIA_URL = 'https://collegeportal.nu.ac.bd/uploads';

    private const MAX_FILE_SIZE = 5 * 1024 * 1024;

    /**
     * @var array<string, string>
     */
    private const EXTENSIONS_BY_MIME = [
        'image/gif' => 'gif',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    public function import(string $reference): string
    {
        $url = $this->sourceUrl($reference);
        $path = $this->storagePath($reference);

        try {
            $response = Http::accept('image/*')
                ->connectTimeout(5)
                ->timeout(20)
                ->get($url);
        } catch (ConnectionException $exception) {
            throw new RuntimeException("Could not connect to {$url}.", previous: $exception);
        }

        if ($response->notFound()) {
            throw new UnexpectedValueException("No image exists at {$url} (HTTP 404).");
        }

        if ($response->failed()) {
            throw new RuntimeException("Downloading {$url} failed with HTTP {$response->status()}.");
        }

        $contents = $response->body();
        $imageInformation = @getimagesizefromstring($contents);
        $mimeType = is_array($imageInformation) ? ($imageInformation['mime'] ?? '') : '';
        $extension = self::EXTENSIONS_BY_MIME[Str::lower($mimeType)] ?? null;

        if ($extension === null) {
            throw new UnexpectedValueException("The response from {$url} is not a supported image.");
        }

        if ($contents === '' || strlen($contents) > self::MAX_FILE_SIZE) {
            throw new UnexpectedValueException("The image from {$url} is empty or larger than 5 MB.");
        }

        if (! Storage::disk('public')->put($path, $contents)) {
            throw new RuntimeException("The downloaded image could not be saved to {$path}.");
        }

        return $path;
    }

    private function sourceUrl(string $reference): string
    {
        $configuredBaseUrl = trim((string) config('services.college_portal.media_url'));
        $baseUrl = rtrim($configuredBaseUrl !== '' ? $configuredBaseUrl : self::DEFAULT_MEDIA_URL, '/');
        $reference = trim($reference);

        if (filter_var($reference, FILTER_VALIDATE_URL)) {
            $host = Str::lower((string) parse_url($reference, PHP_URL_HOST));
            $path = (string) parse_url($reference, PHP_URL_PATH);

            if ($host !== 'collegeportal.nu.ac.bd' || ! Str::startsWith($path, '/uploads/')) {
                throw new RuntimeException('The college media URL is not from the approved National University uploads directory.');
            }

            return $reference;
        }

        $reference = Str::after(ltrim($reference, '/'), 'uploads/');

        if ($reference === '' || str_contains($reference, '..')) {
            throw new RuntimeException('The college media reference is invalid.');
        }

        return $baseUrl.'/'.implode('/', array_map('rawurlencode', explode('/', $reference)));
    }

    private function storagePath(string $reference): string
    {
        if (filter_var($reference, FILTER_VALIDATE_URL)) {
            $reference = (string) parse_url($reference, PHP_URL_PATH);
        }

        $path = Str::after(ltrim(trim($reference), '/'), 'uploads/');
        $path = rawurldecode($path);

        if ($path === '' || str_contains($path, '..') || Str::startsWith($path, '/')) {
            throw new RuntimeException('The college media storage path is invalid.');
        }

        return $path;
    }
}
