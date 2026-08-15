<?php

use App\Livewire\AiChat;
use App\Models\AiSetting;
use App\Models\College;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

it('answers a teacher without storing the conversation', function () {
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

    expect(Schema::hasTable('ai_conversations'))->toBeFalse()
        ->and(Schema::hasTable('ai_messages'))->toBeFalse();

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
    $collegeUrl = route('public.colleges.show', $college);
    Http::fake(['https://api.openai.com/v1/chat/completions' => Http::response([
        'choices' => [['message' => ['content' => "**বিস্তারিত:**\n[{$collegeUrl}]({$collegeUrl}) — {$collegeUrl}"]]],
    ])]);
    AiSetting::query()->create([
        'is_enabled' => true,
        'provider' => 'openai',
        'model' => 'gpt-4o-mini',
        'endpoint' => 'https://api.openai.com/v1',
        'api_key' => 'test-key',
        'history_limit' => 10,
    ]);

    $component = Livewire::test(AiChat::class)
        ->set('question', 'BHAWL BADR ALM GVT COLGE এই কলেজে কোন কোর্স চালু আছে আমাকে জানাও')
        ->call('send')
        ->assertSeeHtml('href="'.$collegeUrl.'"')
        ->assertSeeHtml('target="_blank"')
        ->assertSeeHtml('rel="noopener noreferrer"')
        ->assertDontSee('**বিস্তারিত:**');

    expect(substr_count($component->html(), 'href="'.$collegeUrl.'"'))->toBe(1);

    Http::assertSent(fn ($request): bool => str_contains($request['messages'][0]['content'], 'Bangla, English, Economics')
        && str_contains($request['messages'][0]['content'], 'BHAWAL BADRE ALAM GOVT. COLLEGE')
        && ! str_contains($request['messages'][0]['content'], 'SHERE BANGLA COLLEGE')
        && str_contains($request['messages'][0]['content'], route('public.colleges.show', $college)));

    expect(Schema::hasTable('ai_conversations'))->toBeFalse();
});

it('restores only safe temporary browser messages', function () {
    $messages = collect(range(1, 35))->map(fn (int $number): array => [
        'role' => $number % 2 === 0 ? 'assistant' : 'user',
        'content' => "<script>alert(1)</script> Message {$number}",
    ])->all();

    Livewire::test(AiChat::class)
        ->call('restoreMessages', $messages)
        ->assertCount('messages', 30)
        ->assertDontSee('<script>')
        ->assertSee('Message 35');
});
