<?php

namespace App\Livewire;

use App\Models\AiSetting;
use App\Services\AiChatService;
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
    /** @var array<int, array{role: string, content: string}> */
    public array $messages = [];

    public function mount(): void
    {
        $this->messages = [['role' => 'assistant', 'content' => __('Hello! I am the website AI assistant. How can I help you today?')]];
    }

    public function send(AiChatService $chatService, WebsiteKnowledgeService $knowledgeService): void
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

        $history = collect($this->messages)
            ->reject(fn (array $message, int $index): bool => $index === 0 && $message['role'] === 'assistant')
            ->take(-$setting->history_limit)
            ->values()
            ->all();

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

        $this->messages[] = ['role' => 'assistant', 'content' => $reply];
        $this->dispatch('ai-message-added');
    }

    public function resetConversation(): void
    {
        $this->resetErrorBag();
        $this->mount();
        $this->dispatch('ai-conversation-reset');
    }

    /** @param array<int, mixed> $messages */
    public function restoreMessages(array $messages): void
    {
        $restoredMessages = collect($messages)
            ->filter(fn (mixed $message): bool => is_array($message)
                && in_array($message['role'] ?? null, ['user', 'assistant'], true)
                && is_string($message['content'] ?? null))
            ->map(fn (array $message): array => [
                'role' => $message['role'],
                'content' => Str::limit(strip_tags($message['content']), 5000, ''),
            ])
            ->take(-30)
            ->values()
            ->all();

        if ($restoredMessages !== []) {
            $this->messages = $restoredMessages;
        }
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

    public function render(): View
    {
        return view('livewire.ai-chat');
    }
}
