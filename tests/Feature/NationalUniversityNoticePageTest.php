<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Cache::forget('national-university.latest-notices');
    config(['services.national_university_notices.url' => 'https://notices.example.test/']);
});

it('renders and paginates the national university notice archive', function () {
    Http::fake(['notices.example.test/*' => Http::response(collect(range(1, 25))->map(fn (int $number): array => [
        'title' => "নোটিশ নম্বর {$number}",
        'date' => '28-08-2026',
        'url' => "https://www.nu.ac.bd/notice-{$number}.pdf",
        'category' => $number % 2 === 0 ? 'পরীক্ষা' : 'সাধারণ',
    ])->all())]);

    $this->get(route('notices.index'))
        ->assertOk()
        ->assertSee('data-notice-search', false)
        ->assertSee('data-live-search-status', false)
        ->assertSee('টাইপ করার সঙ্গে সঙ্গে ফলাফল দেখাবে')
        ->assertSee('data-notice-list', false)
        ->assertSee('data-notice-pagination', false)
        ->assertSee('নোটিশ নম্বর 1')
        ->assertDontSee('নোটিশ নম্বর 21');

    $this->get(route('notices.index', ['page' => 2]))
        ->assertOk()
        ->assertSee('নোটিশ নম্বর 21');
});

it('searches notices by title and filters them by category', function () {
    Http::fake(['notices.example.test/*' => Http::response([
        ['title' => 'অনার্স পরীক্ষার সময়সূচি', 'category' => 'পরীক্ষা'],
        ['title' => 'ভর্তি সংক্রান্ত বিজ্ঞপ্তি', 'category' => 'ভর্তি'],
    ])]);

    $this->get(route('notices.index', ['search' => 'অনার্স']))
        ->assertOk()
        ->assertSee('অনার্স পরীক্ষার সময়সূচি')
        ->assertDontSee('ভর্তি সংক্রান্ত বিজ্ঞপ্তি');

    $this->get(route('notices.index', ['category' => 'ভর্তি']))
        ->assertOk()
        ->assertSee('ভর্তি সংক্রান্ত বিজ্ঞপ্তি')
        ->assertDontSee('অনার্স পরীক্ষার সময়সূচি');
});

it('renders an empty state when the notice source is unavailable', function () {
    Http::fake(['notices.example.test/*' => Http::response(status: 503)]);

    $this->get(route('notices.index'))
        ->assertOk()
        ->assertSee('কোনো নোটিশ পাওয়া যায়নি');
});
