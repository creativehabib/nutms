<?php

namespace App\Enums;

enum TrainingRegistrationStatus: string
{
    case Pending = 'Pending';
    case Approved = 'Approved';
    case Rejected = 'Rejected';
    case Completed = 'Completed';

    /** @return array<int, self> */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Pending => [self::Approved, self::Rejected],
            self::Approved => [self::Pending, self::Rejected, self::Completed],
            self::Rejected => [self::Pending],
            self::Completed => [],
        };
    }

    /** @return array<int, self> */
    public function reviewOptions(): array
    {
        return array_values(array_filter(
            [$this, ...$this->allowedTransitions()],
            fn (self $status): bool => $status !== self::Completed,
        ));
    }

    public function canTransitionTo(self $status): bool
    {
        return in_array($status, $this->allowedTransitions(), true);
    }
}
