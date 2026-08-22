<?php

use App\Models\College;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

it('downloads National University college media to public storage', function () {
    Storage::fake('public');
    Http::preventStrayRequests();
    config()->set('services.college_portal.media_url', '');
    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=', true);
    Http::fake([
        'https://collegeportal.nu.ac.bd/uploads/0205_logo.jpg' => Http::response($png, 200, ['Content-Type' => 'image/png']),
        'https://collegeportal.nu.ac.bd/uploads/0205_banner.jpg' => Http::response($png, 200, ['Content-Type' => 'image/png']),
    ]);

    $college = College::query()->create([
        'name' => 'College With Remote Media',
        'logo' => '0205_logo.jpg',
        'banner' => '0205_banner.jpg',
    ]);

    $this->artisan('colleges:download-media')->assertSuccessful();

    $college->refresh();

    expect($college->logo)->toBe('0205_logo.jpg')
        ->and($college->banner)->toBe('0205_banner.jpg');
    Storage::disk('public')->assertExists($college->logo);
    Storage::disk('public')->assertExists($college->banner);
});

it('rejects media outside the approved National University uploads directory', function () {
    Storage::fake('public');
    Http::preventStrayRequests();

    College::query()->create([
        'name' => 'College With Unsafe Media',
        'logo' => 'https://example.com/logo.png',
    ]);

    $this->artisan('colleges:download-media')->assertFailed();

    Http::assertNothingSent();
    Storage::disk('public')->assertDirectoryEmpty('college-logos');
});
