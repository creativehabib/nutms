<?php

namespace App\Notifications;

use App\Enums\ApprovalStatus;
use App\Models\Teacher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeacherApprovalStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Teacher $teacher,
        public ApprovalStatus $status,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return config('mail.approval_notifications_enabled', false)
            ? ['database', 'mail']
            : ['database'];
    }

    /** @return array<string, string> */
    public function viaConnections(): array
    {
        return [
            'database' => 'sync',
            'mail' => (string) config('mail.notifications_queue_connection', 'sync'),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Teacher profile status: :status', ['status' => __($this->status->value)]))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
            ->line($this->message())
            ->action($this->actionLabel(), $this->targetUrl())
            ->line(__('Thank you.'));
    }

    /** @return array<string, int|string> */
    public function toArray(object $notifiable): array
    {
        return [
            'teacher_id' => $this->teacher->id,
            'status' => $this->status->value,
            'message' => $this->message(),
            'url' => $this->targetUrl(),
        ];
    }

    private function targetUrl(): string
    {
        return $this->status === ApprovalStatus::Rejected
            ? route('teachers.resubmit', $this->teacher)
            : route('dashboard');
    }

    private function actionLabel(): string
    {
        return $this->status === ApprovalStatus::Rejected
            ? __('Review and resubmit profile')
            : __('Go to dashboard');
    }

    private function message(): string
    {
        return match ($this->status) {
            ApprovalStatus::Approved => __('Your teacher profile has been approved.'),
            ApprovalStatus::Rejected => __('Your teacher profile has been rejected. Please review and resubmit it.'),
            ApprovalStatus::Pending => __('Your teacher profile is pending approval.'),
        };
    }
}
