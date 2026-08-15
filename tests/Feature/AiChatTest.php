<?php

use App\Livewire\AiChat;
use App\Models\AiConversation;
use App\Models\AiSetting;
use App\Models\College;
use App\Models\User;
use App\Services\AiConversationPruner;
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

it('shows provider diagnostics to an administrator when a configured key is rejected', function () {
    Http::fake(['https://api.openai.com/v1/chat/completions' => Http::response([], 401)]);
    AiSetting::query()->create([
        'is_enabled' => true,
        'provider' => 'openai',
        'model' => 'gpt-4o-mini',
        'endpoint' => 'https://api.openai.com/v1',
        'api_key' => 'invalid-key',
        'history_limit' => 10,
    ]);
    $admin = User::factory()->withRole('admin')->create();

    Livewire::actingAs($admin)->test(AiChat::class)
        ->set('question', 'Is the connection working?')
        ->call('send')
        ->assertSee('The AI provider rejected the API key. Create a valid key and save it again.');

    Http::assertSent(fn ($request): bool => $request->url() === 'https://api.openai.com/v1/chat/completions');
});

it('does not expose provider configuration errors to teachers', function () {
    Http::fake(['https://api.openai.com/v1/chat/completions' => Http::response([], 401)]);
    AiSetting::query()->create([
        'is_enabled' => true,
        'provider' => 'openai',
        'model' => 'gpt-4o-mini',
        'endpoint' => 'https://api.openai.com/v1',
        'api_key' => 'invalid-key',
        'history_limit' => 10,
    ]);
    $teacher = User::factory()->withRole('teacher')->create();

    Livewire::actingAs($teacher)->test(AiChat::class)
        ->set('question', 'প্রশিক্ষণ কোথায় পাব?')
        ->call('send')
        ->assertSee('The AI service is temporarily unavailable. Please try again.')
        ->assertDontSee('rejected the API key');
});

it('uses the native Gemini generateContent API', function () {
    Http::fake(['https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent' => Http::response([
        'candidates' => [['content' => ['parts' => [['text' => 'Gemini is connected.']]]]],
    ])]);
    AiSetting::query()->create([
        'is_enabled' => true,
        'provider' => 'gemini',
        'model' => 'gemini-2.5-flash',
        'endpoint' => 'https://generativelanguage.googleapis.com/v1beta',
        'api_key' => 'gemini-key',
        'history_limit' => 10,
    ]);
    $admin = User::factory()->withRole('admin')->create();

    Livewire::actingAs($admin)->test(AiChat::class)
        ->set('question', 'Can Gemini answer?')
        ->call('send')
        ->assertSee('Gemini is connected.');

    Http::assertSent(fn ($request): bool => $request->url() === 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent'
        && $request->hasHeader('x-goog-api-key', 'gemini-key')
        && $request['contents'][0]['role'] === 'user'
        && filled($request['system_instruction']['parts'][0]['text']));
});

it('grounds public answers with matching college programs and a verified clickable link', function () {
    College::query()->create([
        'name' => 'GOVT. P. C. COLLEGE',
        'college_code' => '101',
        'is_active' => true,
        'approval_status' => 'approved',
    ]);
    College::query()->create([
        'name' => 'SHERE BANGLA COLLEGE',
        'college_code' => '102',
        'is_active' => true,
        'approval_status' => 'approved',
    ]);
    $college = College::query()->create([
        'name' => 'BHAWAL BADRE ALAM GOVT. COLLEGE',
        'college_code' => '5201',
        'is_active' => true,
        'approval_status' => 'approved',
    ]);
    $college->programs()->create([
        'level' => 'Honours',
        'name' => 'Honours Courses',
        'items' => ['Bangla', 'English', 'Economics'],
    ]);
    Http::fake(['https://api.openai.com/v1/chat/completions' => Http::response([
        'choices' => [['message' => ['content' => '[কলেজের বিস্তারিত পেজ]('.route('public.colleges.show', $college).')']]],
    ])]);
    AiSetting::query()->create([
        'is_enabled' => true,
        'provider' => 'openai',
        'model' => 'gpt-4o-mini',
        'endpoint' => 'https://api.openai.com/v1',
        'api_key' => 'test-key',
        'history_limit' => 10,
    ]);

    Livewire::test(AiChat::class)
        ->set('question', 'BHAWL BADR ALM GVT COLGE এই কলেজে কোন কোর্স চালু আছে আমাকে জানাও')
        ->call('send')
        ->assertSeeHtml('href="'.route('public.colleges.show', $college).'"')
        ->assertSeeHtml('target="_blank"')
        ->assertSeeHtml('rel="noopener noreferrer"');

    Http::assertSent(fn ($request): bool => str_contains($request['messages'][0]['content'], 'Bangla, English, Economics')
        && str_contains($request['messages'][0]['content'], 'BHAWAL BADRE ALAM GOVT. COLLEGE')
        && ! str_contains($request['messages'][0]['content'], 'SHERE BANGLA COLLEGE')
        && str_contains($request['messages'][0]['content'], route('public.colleges.show', $college)));

    expect(AiConversation::query()->count())->toBe(0);
});

it('deletes expired conversations and bounds messages in active conversations', function () {
    AiSetting::query()->create([
        'is_enabled' => true,
        'provider' => 'openai',
        'model' => 'gpt-4o-mini',
        'endpoint' => 'https://api.openai.com/v1',
        'api_key' => 'test-key',
        'history_limit' => 10,
        'retention_days' => 7,
    ]);
    $expired = AiConversation::query()->create(['guest_token' => fake()->uuid()]);
    $expired->messages()->create(['role' => 'user', 'content' => 'Expired']);
    $expired->forceFill(['created_at' => now()->subDays(10), 'updated_at' => now()->subDays(10)])->saveQuietly();
    $active = AiConversation::query()->create(['guest_token' => fake()->uuid()]);
    foreach (range(1, 30) as $number) {
        $active->messages()->create(['role' => $number % 2 === 0 ? 'assistant' : 'user', 'content' => "Message {$number}"]);
    }

    $pruner = app(AiConversationPruner::class);
    $pruner->trim($active, 10);
    $deleted = $pruner->prune();

    expect($deleted)->toBe(1)
        ->and(AiConversation::query()->whereKey($expired->id)->exists())->toBeFalse()
        ->and($active->messages()->count())->toBe(20);
});
