<?php

use App\Livewire\Training\TrainingCalendar;
use App\Livewire\Training\TrainingManagement;
use App\Livewire\Training\UpcomingTrainings;
use App\Models\Training;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

beforeEach(function () {
    Carbon::setTestNow('2026-08-12 09:00:00');
});

afterEach(function () {
    Carbon::setTestNow();
});

it('requires authentication to view the training calendar', function () {
    $this->get(route('training.calendar'))->assertRedirect(route('login'));
});

it('allows an admin to create a training for selected teachers', function () {
    $admin = User::factory()->create();
    $teacherUser = User::factory()->withRole('teacher')->create();
    Teacher::query()->create(['user_id' => $teacherUser->id, 'name' => $teacherUser->name]);

    Livewire::actingAs($admin)->test(TrainingManagement::class)
        ->set('title', 'Outcome Based Education')
        ->set('description', 'Detailed workshop information')
        ->set('startDate', '2026-08-20T10:00')
        ->set('endDate', '2026-08-20T16:00')
        ->set('registrationDeadline', '2026-08-18T23:59')
        ->set('type', 'Offline')
        ->set('capacity', '30')
        ->set('eligibleTeacherIds', [$teacherUser->id])
        ->call('save')
        ->assertHasNoErrors();

    $training = Training::query()->where('title', 'Outcome Based Education')->firstOrFail();
    expect($training->eligibleTeachers)->toHaveCount(1)
        ->and($training->eligibleTeachers->first()->is($teacherUser))->toBeTrue();
});

it('shows published training sessions in the selected month', function () {
    Training::factory()->create([
        'title' => 'Digital Pedagogy',
        'start_date' => '2026-08-24 10:00:00',
        'end_date' => '2026-08-24 14:00:00',
        'type' => 'Online',
    ]);
    Training::factory()->create([
        'title' => 'Unpublished Workshop',
        'start_date' => '2026-08-25 10:00:00',
        'end_date' => '2026-08-25 14:00:00',
        'status' => 'Draft',
    ]);

    Livewire::test(TrainingCalendar::class)
        ->assertSet('currentYear', 2026)
        ->assertSet('currentMonth', 8)
        ->assertSee('Digital Pedagogy')
        ->assertDontSee('Unpublished Workshop')
        ->set('type', 'Offline')
        ->assertDontSee('Digital Pedagogy');
});

it('navigates between calendar months and returns to today', function () {
    Livewire::test(TrainingCalendar::class)
        ->call('nextMonth')
        ->assertSet('currentMonth', 9)
        ->call('previousMonth')
        ->assertSet('currentMonth', 8)
        ->call('goToToday')
        ->assertSet('currentYear', 2026)
        ->assertSet('currentMonth', 8);
});

it('only lists upcoming training sessions in the configured window', function () {
    Training::factory()->create([
        'title' => 'Within Thirty Days',
        'start_date' => now()->addDays(10),
        'end_date' => now()->addDays(10)->addHours(4),
    ]);
    Training::factory()->create([
        'title' => 'Outside Thirty Days',
        'start_date' => now()->addDays(31),
        'end_date' => now()->addDays(31)->addHours(4),
    ]);

    Livewire::test(UpcomingTrainings::class)
        ->assertSee('Within Thirty Days')
        ->assertDontSee('Outside Thirty Days');
});

it('allows an authenticated teacher to register only once', function () {
    $user = User::factory()->create();
    $training = Training::factory()->create([
        'start_date' => now()->addDays(5),
        'end_date' => now()->addDays(5)->addHours(4),
        'registration_deadline' => now()->addDays(4),
    ]);
    $training->eligibleTeachers()->attach($user);

    Livewire::actingAs($user)->test(UpcomingTrainings::class)
        ->call('enroll', $training->id)
        ->call('enroll', $training->id);

    expect($training->participants()->whereKey($user->id)->count())->toBe(1)
        ->and($training->participants()->whereKey($user->id)->first()->pivot->status)->toBe('Pending');
});

it('does not register a teacher after the deadline', function () {
    $user = User::factory()->create();
    $training = Training::factory()->create([
        'start_date' => now()->addDays(5),
        'end_date' => now()->addDays(5)->addHours(4),
        'registration_deadline' => now()->subMinute(),
    ]);
    $training->eligibleTeachers()->attach($user);

    Livewire::actingAs($user)->test(UpcomingTrainings::class)
        ->call('enroll', $training->id);

    expect($training->participants()->exists())->toBeFalse();
});

it('lets an admin approve and complete a registration after the training ends', function () {
    $admin = User::factory()->create();
    $teacherUser = User::factory()->withRole('teacher')->create();
    Teacher::query()->create(['user_id' => $teacherUser->id, 'name' => $teacherUser->name]);
    $training = Training::factory()->create([
        'start_date' => now()->addDay(),
        'end_date' => now()->addDay()->addHours(4),
        'registration_deadline' => now()->addHours(12),
    ]);
    $training->eligibleTeachers()->attach($teacherUser);

    Livewire::actingAs($teacherUser)->test(UpcomingTrainings::class)->call('enroll', $training->id);
    Livewire::actingAs($admin)->test(TrainingManagement::class)->call('approve', $training->id, $teacherUser->id);

    expect($training->participants()->whereKey($teacherUser->id)->first()->pivot->status)->toBe('Approved');

    Carbon::setTestNow('2026-08-14 09:00:00');
    Livewire::actingAs($admin)->test(TrainingManagement::class)->call('complete', $training->id, $teacherUser->id);

    $registration = $training->participants()->whereKey($teacherUser->id)->first()->pivot;
    expect($registration->status)->toBe('Completed')
        ->and($registration->certificate_number)->not->toBeNull();
});

it('allows only the completed participant to download a certificate', function () {
    $teacherUser = User::factory()->withRole('teacher')->create();
    $otherUser = User::factory()->withRole('teacher')->create();
    $training = Training::factory()->create();
    $training->participants()->attach($teacherUser, [
        'status' => 'Completed',
        'completed_at' => now(),
        'certificate_number' => 'NU-TC-TEST-1',
    ]);

    $this->actingAs($teacherUser)
        ->get(route('trainings.certificate', $training))
        ->assertSuccessful()
        ->assertHeader('Content-Type', 'image/svg+xml; charset=UTF-8')
        ->assertSee('NU-TC-TEST-1');

    $this->actingAs($otherUser)
        ->get(route('trainings.certificate', $training))
        ->assertNotFound();
});

it('generates a Google Calendar event link', function () {
    $training = Training::factory()->create([
        'title' => 'Curriculum Workshop',
        'start_date' => '2026-08-24 10:00:00',
        'end_date' => '2026-08-24 14:00:00',
    ]);

    expect($training->googleCalendarUrl())
        ->toStartWith('https://calendar.google.com/calendar/render?')
        ->toContain('Curriculum%20Workshop')
        ->toContain('20260824T100000Z%2F20260824T140000Z');
});
