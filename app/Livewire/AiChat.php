<?php

namespace App\Livewire;

use App\Models\AiConversation;
use App\Models\AiSetting;
use App\Services\AiChatService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;
use Throwable;

class AiChat extends Component
{
    public bool $open = false;
    public string $question = '';
    public ?int $conversationId = null;

    /** @var array<int, array{role: string, content: string}> */
    public array $messages = [];

    public function mount(): void
    {
        $this->messages = [['role' => 'assistant', 'content' => __('Hello! I am the website AI assistant. How can I help you today?')]];
    }

    public function send(AiChatService $chatService): void
    {
        $validated = $this->validate(['question' => ['required', 'string', 'min:2', 'max:2000']]);
        $rateLimitKey = 'ai-chat:'.(auth()->id() ?? request()->ip());

        if (RateLimiter::tooManyAttempts($rateLimitKey, 10)) {
            $this->addError('question', __('Too many messages. Please wait :seconds seconds.', ['seconds' => RateLimiter::availableIn($rateLimitKey)]));

            return;
        }

        RateLimiter::hit($rateLimitKey, 60);
        $question = trim($validated['question']);
        $this->question = '';
        $this->messages[] = ['role' => 'user', 'content' => $question];

        $setting = AiSetting::query()->latest('id')->first();

        if ($setting === null || ! $setting->is_enabled) {
            $this->messages[] = ['role' => 'assistant', 'content' => __('The AI assistant is currently unavailable.')];

            return;
        }

        $conversation = $this->conversation();
        $conversation->messages()->create(['role' => 'user', 'content' => $question]);
        $history = $conversation->messages()->latest('id')->limit($setting->history_limit)->get()->reverse()
            ->map(fn ($message): array => ['role' => $message->role, 'content' => $message->content])->values()->all();

        try {
            $reply = $chatService->reply($setting, $history);
        } catch (Throwable $exception) {
            report($exception);
            $reply = __('The AI service is temporarily unavailable. Please try again.');
        }

        $conversation->messages()->create(['role' => 'assistant', 'content' => $reply]);
        $this->messages[] = ['role' => 'assistant', 'content' => $reply];
        $this->dispatch('ai-message-added');
    }

    public function resetConversation(): void
    {
        $this->conversationId = null;
        $this->resetErrorBag();
        $this->mount();
    }

    private function conversation(): AiConversation
    {
        if ($this->conversationId !== null) {
            return AiConversation::query()
                ->whereKey($this->conversationId)
                ->when(auth()->check(), fn ($query) => $query->where('user_id', auth()->id()))
                ->when(auth()->guest(), fn ($query) => $query->where('guest_token', session('ai_guest_token')))
                ->firstOrFail();
        }

        $guestToken = auth()->check() ? null : session('ai_guest_token');
        if (auth()->guest() && blank($guestToken)) {
            $guestToken = Str::uuid()->toString();
        }
        if ($guestToken !== null) {
            session()->put('ai_guest_token', $guestToken);
        }

        $conversation = AiConversation::query()->create([
            'user_id' => auth()->id(),
            'guest_token' => $guestToken,
            'title' => Str::limit($this->messages[array_key_last($this->messages)]['content'], 100),
        ]);
        $this->conversationId = $conversation->id;

        return $conversation;
    }

    public function render(): View
    {
        return view('livewire.ai-chat');
    }
}
