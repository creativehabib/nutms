<div class="fixed bottom-5 right-5 z-50" x-data x-on:ai-message-added.window="$nextTick(() => { const box = $refs.messages; if (box) box.scrollTop = box.scrollHeight })">
    @if($open)
        <section class="mb-3 flex h-[min(680px,calc(100vh-7rem))] w-[min(420px,calc(100vw-2rem))] flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-2xl dark:border-zinc-700 dark:bg-zinc-900" aria-label="{{ __('AI Assistant') }}">
            <header class="flex items-center gap-3 border-b border-zinc-200 px-4 py-3 dark:border-zinc-700">
                <div class="flex size-10 items-center justify-center rounded-xl bg-blue-600 text-white"><flux:icon.sparkles class="size-5" /></div>
                <div class="min-w-0 flex-1">
                    <flux:heading>{{ __('Website AI') }} <flux:badge size="sm" color="blue">{{ __('Beta') }}</flux:badge></flux:heading>
                    <flux:text size="sm">{{ __('Smart website assistance') }}</flux:text>
                </div>
                <flux:button wire:click="resetConversation" variant="ghost" size="sm" icon="arrow-path" :aria-label="__('New conversation')" />
                <flux:button wire:click="$toggle('open')" variant="ghost" size="sm" icon="x-mark" :aria-label="__('Close')" />
            </header>

            <div x-ref="messages" class="flex-1 space-y-4 overflow-y-auto bg-zinc-50/70 p-4 dark:bg-zinc-950/40">
                @foreach($messages as $index => $message)
                    <div wire:key="ai-message-{{ $index }}" class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[88%] rounded-2xl px-4 py-3 text-sm leading-6 {{ $message['role'] === 'user' ? 'rounded-br-md bg-blue-600 text-white' : 'rounded-bl-md border border-zinc-200 bg-white text-zinc-700 shadow-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200' }}">
                            @if($message['role'] === 'assistant')
                                {!! $this->renderAssistantMessage($message['content']) !!}
                            @else
                                {{ $message['content'] }}
                            @endif
                        </div>
                    </div>
                @endforeach
                <div wire:loading wire:target="send" class="text-sm text-zinc-500">{{ __('AI is thinking...') }}</div>
            </div>

            <form wire:submit="send" class="border-t border-zinc-200 p-3 dark:border-zinc-700">
                <div class="flex items-end gap-2">
                    <flux:textarea wire:model="question" rows="2" maxlength="2000" resize="none" :placeholder="__('Ask about this website...')" class="flex-1" />
                    <flux:button type="submit" variant="primary" icon="paper-airplane" wire:loading.attr="disabled" :aria-label="__('Send')" />
                </div>
                <flux:error name="question" />
                <p class="mt-2 text-center text-xs text-zinc-400">{{ __('AI can make mistakes. Verify important information.') }}</p>
            </form>
        </section>
    @endif

    <flux:button wire:click="$toggle('open')" variant="primary" icon="sparkles" class="rounded-full shadow-lg">
        {{ $open ? __('Close') : __('AI Assistant') }}
    </flux:button>
</div>
