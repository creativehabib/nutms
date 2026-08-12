<?php

use App\Livewire\Training\TrainingCalendar;
use App\Livewire\Training\UpcomingTrainings;
use App\Models\Training;
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

    Livewire::actingAs($user)->test(UpcomingTrainings::class)
        ->call('enroll', $training->id)
        ->call('enroll', $training->id);

    expect($training->participants()->whereKey($user->id)->count())->toBe(1);
});

it('does not register a teacher after the deadline', function () {
    $user = User::factory()->create();
    $training = Training::factory()->create([
        'start_date' => now()->addDays(5),
        'end_date' => now()->addDays(5)->addHours(4),
        'registration_deadline' => now()->subMinute(),
    ]);

    Livewire::actingAs($user)->test(UpcomingTrainings::class)
        ->call('enroll', $training->id);

    expect($training->participants()->exists())->toBeFalse();
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
