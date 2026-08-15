<flux:dropdown align="end" position="bottom" wire:poll.30s>
    <div class="relative">
        <flux:button type="button" variant="ghost" icon="bell" :aria-label="__('Notifications')" />
        @if ($unreadCount > 0)
            <span class="pointer-events-none absolute -right-1 -top-1 flex min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold leading-5 text-white">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </div>

    <flux:menu class="w-80 sm:w-96">
        <div class="flex items-center justify-between gap-3 px-3 py-2">
            <flux:heading size="sm">{{ __('Notifications') }}</flux:heading>
            @if ($unreadCount > 0)
                <flux:button size="xs" variant="ghost" wire:click="markAllAsRead">{{ __('Mark all as read') }}</flux:button>
            @endif
        </div>
        <div class="flex gap-1 px-3 pb-2" role="group" aria-label="{{ __('Filter notifications') }}">
            <flux:button size="xs" :variant="$filter === 'all' ? 'primary' : 'ghost'" wire:click="setFilter('all')">
                {{ __('All') }}
            </flux:button>
            <flux:button size="xs" :variant="$filter === 'unread' ? 'primary' : 'ghost'" wire:click="setFilter('unread')">
                {{ __('Unread') }}
                @if ($unreadCount > 0)
                    <span class="ms-1">({{ $unreadCount }})</span>
                @endif
            </flux:button>
            @if ($hasReadNotifications)
                <flux:button size="xs" variant="ghost" class="ms-auto text-red-600 dark:text-red-400" wire:click="clearRead" wire:confirm="{{ __('Delete all read notifications?') }}">
                    {{ __('Clear read') }}
                </flux:button>
            @endif
        </div>
        <flux:menu.separator />

        @forelse ($notifications as $notification)
            <button type="button" wire:key="notification-{{ $notification->id }}" wire:click="open('{{ $notification->id }}')" class="flex w-full gap-3 px-3 py-3 text-left transition hover:bg-zinc-100 dark:hover:bg-zinc-800">
                <span class="mt-1 size-2 shrink-0 rounded-full {{ $notification->read_at ? 'bg-transparent' : 'bg-indigo-500' }}"></span>
                <span class="min-w-0 flex-1">
                    <span class="block text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $notification->data['message'] ?? __('Notification') }}</span>
                    @if (filled($notification->data['location'] ?? null) && ($notification->data['status'] ?? null) === 'Approved')
                        <span class="mt-1 block truncate text-xs text-zinc-500">{{ $notification->data['location'] }}</span>
                    @endif
                    <span class="mt-1 block text-xs text-zinc-400">{{ $notification->created_at->diffForHumans() }}</span>
                </span>
            </button>
        @empty
            <div class="px-4 py-8 text-center">
                <flux:icon.bell class="mx-auto size-8 text-zinc-400" />
                <flux:text class="mt-2">{{ $filter === 'unread' ? __('You are all caught up.') : __('No notifications yet.') }}</flux:text>
            </div>
        @endforelse
    </flux:menu>
</flux:dropdown>
