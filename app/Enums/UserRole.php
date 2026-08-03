<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Principal = 'principal';
    case Teacher = 'teacher';

    public function label(): string
    {
        return match ($this) {
            self::Admin => __('Admin'),
            self::Principal => __('College Principal'),
            self::Teacher => __('Teacher'),
        };
    }

    /** @return array<int, string> */
    public function permissions(): array
    {
        return match ($this) {
            self::Admin => [__('Manage all colleges and teachers'), __('Change roles'), __('Manage reference data and training catalog'), __('View all reports')],
            self::Principal => [__('Edit own college profile'), __('Manage teachers at own college'), __('Approve teacher profiles')],
            self::Teacher => [__('Create, view, and edit own teacher profile')],
        };
    }
}
