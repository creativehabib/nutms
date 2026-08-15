<?php

namespace App\Livewire\Layout;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class NotificationMenu extends Component
{
    public string $filter = 'all';

    public function open(string $notificationId): void
    {
        $notification = auth()->user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        $this->redirect($notification->data['url'] ?? route('dashboard'), navigate: true);
    }

    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
    }

    public function clearRead(): void
    {
        auth()->user()->readNotifications()->delete();
    }

    public function setFilter(string $filter): void
    {
        if (in_array($filter, ['all', 'unread'], true)) {
            $this->filter = $filter;
        }
    }

    public function render(): View
    {
        $notifications = auth()->user()->notifications()
            ->when($this->filter === 'unread', fn ($query) => $query->whereNull('read_at'))
            ->latest()
            ->limit(10)
            ->get();

        return view('livewire.layout.notification-menu', [
            'notifications' => $notifications,
            'unreadCount' => auth()->user()->unreadNotifications()->count(),
            'hasReadNotifications' => auth()->user()->readNotifications()->exists(),
        ]);
    }
}
