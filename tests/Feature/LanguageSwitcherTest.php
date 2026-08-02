<?php

use App\Http\Middleware\SetLocale;
use App\Livewire\Layout\LanguageSwitcher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\Response;

test('guest can switch the session locale from the language switcher', function () {
    Livewire::test(LanguageSwitcher::class)
        ->call('changeLanguage', 'bn')
        ->assertRedirect('/dashboard');

    expect(Session::get('locale'))->toBe('bn');
});

test('authenticated user can switch and persist the locale from the language switcher', function () {
    $user = User::factory()->create(['locale' => 'en']);

    Livewire::actingAs($user)
        ->test(LanguageSwitcher::class)
        ->call('changeLanguage', 'bn')
        ->assertRedirect('/dashboard');

    expect($user->refresh()->locale)->toBe('bn')
        ->and(Session::get('locale'))->toBe('bn');
});

test('locale middleware applies the persisted user locale to the request', function () {
    $user = User::factory()->create(['locale' => 'bn']);

    $this->actingAs($user);

    $middleware = new SetLocale();
    $response = $middleware->handle(Request::create('/dashboard'), function (): Response {
        return new Response(App::currentLocale());
    });

    expect($response->getContent())->toBe('bn');
});
