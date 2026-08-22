<?php

use App\Models\College;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

it('downloads National University college media to public storage', function () {
    Storage::fake('public');
    Http::preventStrayRequests();
    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=', true);
    Http::fake([
        'https://collegeportal.nu.ac.bd/uploads/logos/college.png' => Http::response($png, 200, ['Content-Type' => 'image/png']),
        'https://collegeportal.nu.ac.bd/uploads/banners/college.jpg' => Http::response($png, 200, ['Content-Type' => 'image/png']),
    ]);

    $college = College::query()->create([
        'name' => 'College With Remote Media',
        'logo' => 'logos/college.png',
        'banner' => 'banners/college.jpg',
    ]);

    $this->artisan('colleges:download-media')->assertSuccessful();

    $college->refresh();

    expect($college->logo)->toStartWith("college-logos/{$college->id}-")
        ->and($college->banner)->toStartWith("college-banners/{$college->id}-");
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
