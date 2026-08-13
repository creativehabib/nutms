<?php

use App\Enums\TrainingRegistrationStatus;

it('defines explicit registration lifecycle transitions', function () {
    expect(TrainingRegistrationStatus::Pending->canTransitionTo(TrainingRegistrationStatus::Approved))->toBeTrue()
        ->and(TrainingRegistrationStatus::Pending->canTransitionTo(TrainingRegistrationStatus::Completed))->toBeFalse()
        ->and(TrainingRegistrationStatus::Approved->canTransitionTo(TrainingRegistrationStatus::Completed))->toBeTrue()
        ->and(TrainingRegistrationStatus::Completed->allowedTransitions())->toBeEmpty();
});
