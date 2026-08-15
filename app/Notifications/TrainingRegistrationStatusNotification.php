<?php

namespace App\Notifications;

use App\Models\Training;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingRegistrationStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Training $training,
        public string $status,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return config('mail.training_notifications_enabled', false)
            ? ['database', 'mail']
            : ['database'];
    }

    /** @return array<string, string> */
    public function viaConnections(): array
    {
        return [
            'database' => 'sync',
            'mail' => (string) config('queue.default'),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject(__('Training status: :training', ['training' => $this->training->title]))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
            ->line($this->message());

        if ($this->status === 'Approved') {
            $message
                ->line(__('Training date: :date', ['date' => $this->training->start_date->format('d M Y, g:i A')]))
                ->line(__('Venue / link: :location', ['location' => $this->training->location_or_link ?: __('To be announced')]));
        }

        return $message
            ->action($this->status === 'Completed' ? __('Download Certificate') : __('View Training Calendar'), $this->targetUrl())
            ->line(__('Thank you.'));
    }

    /** @return array<string, int|string|null> */
    public function toArray(object $notifiable): array
    {
        return [
            'training_id' => $this->training->id,
            'training_title' => $this->training->title,
            'status' => $this->status,
            'message' => $this->message(),
            'start_date' => $this->training->start_date?->toIso8601String(),
            'location' => $this->training->location_or_link,
            'url' => $this->targetUrl(),
        ];
    }

    private function targetUrl(): string
    {
        return $this->status === 'Completed'
            ? route('training.certificates')
            : route('training.calendar');
    }

    private function message(): string
    {
        return match ($this->status) {
            'Approved' => __('You have been selected for :training.', ['training' => $this->training->title]),
            'Rejected' => __('Your registration for :training was not selected.', ['training' => $this->training->title]),
            'Completed' => __('Your certificate for :training is ready to download.', ['training' => $this->training->title]),
            'Pending' => __('Your registration for :training is pending review.', ['training' => $this->training->title]),
            default => __('Your registration status for :training is now :status.', [
                'training' => $this->training->title,
                'status' => __($this->status),
            ]),
        };
    }
}
