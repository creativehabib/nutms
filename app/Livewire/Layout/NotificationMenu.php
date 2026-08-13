<?php

namespace App\Livewire\Layout;

use Illuminate\Contracts\View\View;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Component;

class NotificationMenu extends Component
{
    public function open(string $notificationId): void
    {
        $notification = auth()->user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        $this->redirect($notification->data['url'] ?? route('dashboard'), navigate: true);
    }

    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->each(
            fn (DatabaseNotification $notification) => $notification->markAsRead(),
        );
    }

    public function render(): View
    {
        return view('livewire.layout.notification-menu', [
            'notifications' => auth()->user()->notifications()->latest()->limit(8)->get(),
            'unreadCount' => auth()->user()->unreadNotifications()->count(),
        ]);
    }
}
