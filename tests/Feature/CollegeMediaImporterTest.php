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

    expect($college->logo)->toBe('college-logos/0205_logo.jpg')
        ->and($college->banner)->toBe('college-banners/0205_banner.jpg');
    Storage::disk('public')->assertExists($college->logo);
    Storage::disk('public')->assertExists($college->banner);
});

it('moves previously downloaded root media into college media directories', function () {
    Storage::fake('public');
    Http::preventStrayRequests();
    Storage::disk('public')->put('0205_logo.jpg', 'existing-logo');

    $college = College::query()->create([
        'name' => 'College With Root Media',
        'logo' => '/0205_logo.jpg',
    ]);

    $this->artisan('colleges:download-media')->assertSuccessful();

    expect($college->refresh()->logo)->toBe('college-logos/0205_logo.jpg');
    Storage::disk('public')->assertMissing('0205_logo.jpg');
    Storage::disk('public')->assertExists('college-logos/0205_logo.jpg');
    Http::assertNothingSent();
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

it('clears references that are missing or are not images', function () {
    Storage::fake('public');
    Http::preventStrayRequests();
    Http::fake([
        'https://collegeportal.nu.ac.bd/uploads/missing-logo.jpg' => Http::response(status: 404),
        'https://collegeportal.nu.ac.bd/uploads/invalid-banner.jpg' => Http::response('<html>Not an image</html>'),
    ]);

    $college = College::query()->create([
        'name' => 'College With Unavailable Media',
        'logo' => 'missing-logo.jpg',
        'banner' => 'invalid-banner.jpg',
    ]);

    $this->artisan('colleges:download-media')
        ->expectsOutputToContain('2 unavailable references cleared')
        ->assertSuccessful();

    expect($college->refresh()->logo)->toBeNull()
        ->and($college->banner)->toBeNull();
});
