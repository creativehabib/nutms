<?php

namespace App\Livewire;

use App\Models\AiConversation;
use App\Models\AiSetting;
use App\Services\AiChatService;
use App\Services\AiConversationPruner;
use App\Services\WebsiteKnowledgeService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use Livewire\Component;
use RuntimeException;
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

    public function send(AiChatService $chatService, WebsiteKnowledgeService $knowledgeService, AiConversationPruner $pruner): void
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

        $conversation = null;
        if (auth()->check() || $setting->save_guest_conversations) {
            $conversation = $this->conversation();
            $conversation->messages()->create(['role' => 'user', 'content' => $question]);
            $history = $conversation->messages()->latest('id')->limit($setting->history_limit)->get()->reverse()
                ->map(fn ($message): array => ['role' => $message->role, 'content' => $message->content])->values()->all();
        } else {
            $history = collect($this->messages)
                ->reject(fn (array $message, int $index): bool => $index === 0 && $message['role'] === 'assistant')
                ->take(-$setting->history_limit)
                ->values()
                ->all();
        }

        try {
            $reply = $chatService->reply($setting, $history, $knowledgeService->context($question));
        } catch (RuntimeException $exception) {
            report($exception);
            $reply = auth()->user()?->isAdmin()
                ? $exception->getMessage()
                : __('The AI service is temporarily unavailable. Please try again.');
        } catch (Throwable $exception) {
            report($exception);
            $reply = __('An unexpected AI service error occurred. Please contact the administrator.');
        }

        if ($conversation !== null) {
            $conversation->messages()->create(['role' => 'assistant', 'content' => $reply]);
            $conversation->touch();
            $pruner->trim($conversation, $setting->history_limit);
        }
        $this->messages[] = ['role' => 'assistant', 'content' => $reply];
        $this->dispatch('ai-message-added');
    }

    public function resetConversation(): void
    {
        $this->conversationId = null;
        $this->resetErrorBag();
        $this->mount();
    }

    public function renderAssistantMessage(string $message): HtmlString
    {
        $normalizedMessage = preg_replace_callback(
            '~\[([^\]]+)]\((https?://[^\s)]+)\)~u',
            fn (array $matches): string => trim($matches[1]) === $matches[2]
                ? $matches[2]
                : trim($matches[1]).': '.$matches[2],
            $message,
        ) ?? $message;
        $seenUrls = [];
        $normalizedMessage = preg_replace_callback(
            '~https?://[^\s<]+[^\s<\.,;:!?\)\]]~u',
            function (array $matches) use (&$seenUrls): string {
                if (in_array($matches[0], $seenUrls, true)) {
                    return '';
                }

                $seenUrls[] = $matches[0];

                return $matches[0];
            },
            $normalizedMessage,
        ) ?? $normalizedMessage;
        $converter = new GithubFlavoredMarkdownConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
        $html = (string) $converter->convert($normalizedMessage);
        $html = str_replace('<a href=', '<a target="_blank" rel="noopener noreferrer" href=', $html);

        return new HtmlString($html);
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
