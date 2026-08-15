<?php

use App\Livewire\AiChat;
use App\Models\AiConversation;
use App\Models\AiSetting;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

it('answers a teacher and stores the conversation', function () {
    Http::fake(['https://api.openai.com/v1/chat/completions' => Http::response([
        'choices' => [['message' => ['content' => 'প্রশিক্ষণ ক্যালেন্ডার দেখুন।']]],
    ])]);
    AiSetting::query()->create([
        'is_enabled' => true, 'provider' => 'openai', 'model' => 'gpt-4o-mini',
        'endpoint' => 'https://api.openai.com/v1', 'api_key' => 'test-key', 'history_limit' => 10,
    ]);
    $teacher = User::factory()->withRole('teacher')->create();

    Livewire::actingAs($teacher)->test(AiChat::class)
        ->set('question', 'প্রশিক্ষণ কোথায় পাব?')
        ->call('send')
        ->assertHasNoErrors()
        ->assertSee('প্রশিক্ষণ ক্যালেন্ডার দেখুন।');

    $conversation = AiConversation::query()->firstOrFail();
    expect($conversation->user_id)->toBe($teacher->id)
        ->and($conversation->messages()->count())->toBe(2);

    Http::assertSent(fn ($request): bool => $request->hasHeader('Authorization', 'Bearer test-key')
        && str_contains($request['messages'][0]['content'], route('training.calendar')));
});

it('shows a safe unavailable message when AI is disabled', function () {
    Livewire::test(AiChat::class)
        ->set('question', 'কলেজ তালিকা কোথায়?')
        ->call('send')
        ->assertSee('The AI assistant is currently unavailable.');
});
