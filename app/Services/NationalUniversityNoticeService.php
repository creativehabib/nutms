<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class NationalUniversityNoticeService
{
    private const CACHE_KEY = 'national-university.latest-notices';

    /**
     * @return list<array{title: string, url: string|null, published_at: string|null, category: string|null}>
     */
    public function latest(int $limit = 6): array
    {
        return array_slice($this->all(), 0, $limit);
    }

    /**
     * @return list<array{title: string, url: string|null, published_at: string|null, category: string|null}>
     */
    public function all(): array
    {
        $cachedNotices = Cache::get(self::CACHE_KEY);

        if (is_array($cachedNotices)) {
            return $cachedNotices;
        }

        try {
            $response = Http::acceptJson()
                ->connectTimeout(3)
                ->timeout(6)
                ->retry(2, 200, throw: false)
                ->get((string) config('services.national_university_notices.url'));

            if (! $response->successful()) {
                return [];
            }

            $notices = $this->normalize($response->json());
            Cache::put(self::CACHE_KEY, $notices, now()->addMinutes(30));

            return $notices;
        } catch (Throwable) {
            return [];
        }
    }

    /**
     * @return list<array{title: string, url: string|null, published_at: string|null, category: string|null}>
     */
    private function normalize(mixed $payload): array
    {
        if (! is_array($payload)) {
            return [];
        }

        $items = Arr::isList($payload)
            ? $payload
            : Arr::first([
                data_get($payload, 'notices'),
                data_get($payload, 'data.notices'),
                data_get($payload, 'data.results'),
                data_get($payload, 'results'),
                data_get($payload, 'items'),
                data_get($payload, 'data'),
            ], fn (mixed $value): bool => is_array($value), []);

        return collect($items)
            ->filter(fn (mixed $item): bool => is_array($item))
            ->map(function (array $item): array {
                $title = $this->firstString($item, ['title', 'name', 'notice_title', 'noticeTitle', 'headline']);
                $url = $this->safeUrl($this->firstString($item, ['url', 'link', 'noticeLink', 'href', 'pdf', 'pdf_url', 'pdfLink', 'download_url', 'file']));

                return [
                    'title' => $title ?? '',
                    'url' => $url,
                    'published_at' => $this->firstString($item, ['published_at', 'publishedAt', 'date', 'noticeDate', 'created_at']),
                    'category' => $this->firstString($item, ['category', 'type', 'notice_type']),
                ];
            })
            ->filter(fn (array $notice): bool => $notice['title'] !== '')
            ->values()
            ->all();
    }

    /**
     * @param  list<string>  $keys
     */
    private function firstString(array $item, array $keys): ?string
    {
        foreach ($keys as $key) {
            $value = data_get($item, $key);

            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return null;
    }

    private function safeUrl(?string $url): ?string
    {
        if ($url === null) {
            return null;
        }

        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            $url = rtrim((string) config('services.national_university_notices.base_url'), '/').$url;
        }

        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        return in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true) ? $url : null;
    }
}
