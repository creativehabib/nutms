<?php

use App\Services\NationalUniversityNoticeService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Cache::flush();
    config(['services.national_university_notices.url' => 'https://notices.example.test/']);
});

it('normalizes and limits recent national university notices', function () {
    Http::fake(['notices.example.test/*' => Http::response([
        'data' => [
            'notices' => [
                ['title' => 'প্রথম নোটিশ', 'date' => '28-08-2026', 'link' => 'https://www.nu.ac.bd/notice-one.pdf', 'category' => 'পরীক্ষা'],
                ['notice_title' => 'দ্বিতীয় নোটিশ', 'published_at' => '27-08-2026', 'url' => 'https://www.nu.ac.bd/notice-two.pdf'],
            ],
        ],
    ])]);

    $notices = app(NationalUniversityNoticeService::class)->latest(1);

    expect($notices)->toHaveCount(1)
        ->and($notices[0])->toBe([
            'title' => 'প্রথম নোটিশ',
            'url' => 'https://www.nu.ac.bd/notice-one.pdf',
            'published_at' => '28-08-2026',
            'category' => 'পরীক্ষা',
        ]);
});

it('returns an empty list when the notice api is unavailable', function () {
    Http::fake(['notices.example.test/*' => Http::response(status: 503)]);

    expect(app(NationalUniversityNoticeService::class)->latest())->toBe([]);
});

it('rejects unsafe notice links', function () {
    Http::fake(['notices.example.test/*' => Http::response([
        ['title' => 'নিরাপদ শিরোনাম', 'url' => 'javascript:alert(1)'],
    ])]);

    expect(app(NationalUniversityNoticeService::class)->latest()[0]['url'])->toBeNull();
});

it('resolves relative links against the national university website', function () {
    Http::fake(['notices.example.test/*' => Http::response([
        ['title' => 'আপেক্ষিক লিংকের নোটিশ', 'link' => '/uploads/notices/example.pdf'],
    ])]);

    expect(app(NationalUniversityNoticeService::class)->latest()[0]['url'])
        ->toBe('https://www.nu.ac.bd/uploads/notices/example.pdf');
});
