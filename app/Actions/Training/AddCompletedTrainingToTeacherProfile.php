<?php

namespace App\Actions\Training;

use App\Models\Training;
use App\Models\User;

class AddCompletedTrainingToTeacherProfile
{
    public function handle(User $participant, Training $training): void
    {
        $teacher = $participant->teacherProfile;

        if ($teacher === null || $training->training_type_id === null) {
            return;
        }

        $trainingYear = $training->end_date->year;
        $alreadyRecorded = $teacher->trainingTypes()
            ->whereKey($training->training_type_id)
            ->wherePivot('training_year', $trainingYear)
            ->exists();

        if (! $alreadyRecorded) {
            $teacher->trainingTypes()->attach($training->training_type_id, ['training_year' => $trainingYear]);
        }
    }
}
