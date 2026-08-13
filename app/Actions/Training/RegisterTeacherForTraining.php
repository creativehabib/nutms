<?php

namespace App\Actions\Training;

use App\Enums\ApprovalStatus;
use App\Enums\TrainingRegistrationStatus;
use App\Models\Training;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegisterTeacherForTraining
{
    public function handle(Training $training, User $teacher): void
    {
        $this->ensureEligibleTeacher($teacher);

        DB::transaction(function () use ($training, $teacher): void {
            $lockedTraining = Training::query()->lockForUpdate()->findOrFail($training->id);

            if ($lockedTraining->status !== 'Upcoming' || $lockedTraining->start_date->isPast()) {
                throw ValidationException::withMessages(['training' => __('This training is not open for registration.')]);
            }

            if ($lockedTraining->registration_deadline?->isPast()) {
                throw ValidationException::withMessages(['training' => __('Registration for this training has closed.')]);
            }

            if (! $lockedTraining->allows_repeat && $lockedTraining->training_type_id !== null && $teacher->trainings()
                ->where('trainings.training_type_id', $lockedTraining->training_type_id)
                ->wherePivot('status', TrainingRegistrationStatus::Completed->value)
                ->exists()) {
                throw ValidationException::withMessages(['training' => __('You have already completed this training.')]);
            }

            $existingRegistration = DB::table('training_user')
                ->where('training_id', $lockedTraining->id)
                ->where('user_id', $teacher->id)
                ->lockForUpdate()
                ->first();

            if ($existingRegistration !== null) {
                throw ValidationException::withMessages(['training' => __('You have already registered for this training.')]);
            }

            $lockedTraining->participants()->attach($teacher->id, [
                'status' => TrainingRegistrationStatus::Pending->value,
            ]);
        }, attempts: 3);
    }

    private function ensureEligibleTeacher(User $teacher): void
    {
        abort_unless($teacher->hasRole('teacher')
            && $teacher->teacherProfile()
                ->where('approval_status', ApprovalStatus::Approved)
                ->whereHas('college', fn ($query) => $query
                    ->where('approval_status', ApprovalStatus::Approved)
                    ->where('is_active', true))
                ->exists(), 403);
    }
}
