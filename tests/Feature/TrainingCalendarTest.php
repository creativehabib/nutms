<?php

use App\Enums\ApprovalStatus;
use App\Livewire\Layout\NotificationMenu;
use App\Livewire\Training\TrainingCalendar;
use App\Livewire\Training\TrainingManagement;
use App\Livewire\Training\TrainingRegistrationManagement;
use App\Livewire\Training\UpcomingTrainings;
use App\Models\College;
use App\Models\Training;
use App\Models\TrainingInstitute;
use App\Models\TrainingType;
use App\Models\Teacher;
use App\Models\User;
use App\Notifications\TrainingRegistrationStatusNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
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

it('renders the teacher dashboard with certificate compatibility data', function () {
    $teacherUser = registeredAffiliatedTeacher();

    $this->actingAs($teacherUser)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertDontSee(__('My Training Certificates'));
});

it('allows an admin to create a training for all affiliated college teachers', function () {
    $admin = User::factory()->create();
    $trainingType = trainingCatalogItem('Outcome Based Education');

    Livewire::actingAs($admin)->test(TrainingManagement::class)
        ->call('create')
        ->assertSet('showTrainingModal', true)
        ->set('trainingTypeId', (string) $trainingType->id)
        ->set('description', 'Detailed workshop information')
        ->set('startDate', '2026-08-20T10:00')
        ->set('endDate', '2026-08-20T16:00')
        ->set('registrationDeadline', '2026-08-18T23:59')
        ->set('type', 'Offline')
        ->set('capacity', '30')
        ->call('save')
        ->assertHasNoErrors();

    $training = Training::query()->where('title', 'Outcome Based Education')->firstOrFail();
    expect($training->title)->toBe('Outcome Based Education');
});

it('uses the same training modal for editing a published training', function () {
    $admin = User::factory()->create();
    $trainingType = trainingCatalogItem('Published Training');
    $editedTrainingType = trainingCatalogItem('Edited Published Training');
    $training = Training::factory()->create(['training_type_id' => $trainingType->id, 'title' => $trainingType->name]);

    Livewire::actingAs($admin)->test(TrainingManagement::class)
        ->call('edit', $training->id)
        ->assertSet('showTrainingModal', true)
        ->assertSet('editingTrainingId', $training->id)
        ->assertSet('trainingTypeId', (string) $trainingType->id)
        ->set('trainingTypeId', (string) $editedTrainingType->id)
        ->call('save')
        ->assertSet('showTrainingModal', false);

    expect($training->refresh()->title)->toBe('Edited Published Training');
});

it('shows only the registered teacher count in training management', function () {
    $admin = User::factory()->create();
    $teacherUser = registeredAffiliatedTeacher();
    $training = Training::factory()->create();
    $training->participants()->attach($teacherUser, ['status' => 'Pending']);

    Livewire::actingAs($admin)->test(TrainingManagement::class)
        ->assertSee('1')
        ->assertDontSee($teacherUser->name)
        ->assertDontSee($teacherUser->email);
});

it('prevents a teacher from registering for the same catalog training twice after completion', function () {
    $teacherUser = registeredAffiliatedTeacher();
    $trainingType = trainingCatalogItem('Digital Pedagogy');
    $completedTraining = Training::factory()->create(['training_type_id' => $trainingType->id]);
    $completedTraining->participants()->attach($teacherUser, ['status' => 'Completed', 'completed_at' => now()]);
    $upcomingTraining = Training::factory()->create([
        'training_type_id' => $trainingType->id,
        'start_date' => now()->addDays(5),
        'end_date' => now()->addDays(5)->addHours(4),
    ]);

    Livewire::actingAs($teacherUser)->test(UpcomingTrainings::class)
        ->call('enroll', $upcomingTraining->id);

    expect($upcomingTraining->participants()->whereKey($teacherUser->id)->exists())->toBeFalse();
});

it('can explicitly allow teachers to repeat a catalog training', function () {
    $teacherUser = registeredAffiliatedTeacher();
    $trainingType = trainingCatalogItem('Repeatable Workshop');
    $completedTraining = Training::factory()->create(['training_type_id' => $trainingType->id]);
    $completedTraining->participants()->attach($teacherUser, ['status' => 'Completed', 'completed_at' => now()]);
    $repeatableTraining = Training::factory()->create([
        'training_type_id' => $trainingType->id,
        'allows_repeat' => true,
        'start_date' => now()->addDays(5),
        'end_date' => now()->addDays(5)->addHours(4),
    ]);

    Livewire::actingAs($teacherUser)->test(UpcomingTrainings::class)
        ->call('enroll', $repeatableTraining->id);

    expect($repeatableTraining->participants()->whereKey($teacherUser->id)->exists())->toBeTrue();
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
    $user = registeredAffiliatedTeacher();
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

    Livewire::actingAs($user)->test(UpcomingTrainings::class)
        ->assertSee('Within Thirty Days')
        ->assertDontSee('Outside Thirty Days');
});

it('allows an authenticated teacher to register only once', function () {
    $user = registeredAffiliatedTeacher();
    $training = Training::factory()->create([
        'start_date' => now()->addDays(5),
        'end_date' => now()->addDays(5)->addHours(4),
        'registration_deadline' => now()->addDays(4),
    ]);
    Livewire::actingAs($user)->test(UpcomingTrainings::class)
        ->call('enroll', $training->id)
        ->call('enroll', $training->id);

    expect($training->participants()->whereKey($user->id)->count())->toBe(1)
        ->and($training->participants()->whereKey($user->id)->first()->pivot->status)->toBe('Pending');
});

it('exposes the enroll action from the upcoming trainings component', function () {
    expect(method_exists(UpcomingTrainings::class, 'enroll'))->toBeTrue()
        ->and((new ReflectionMethod(UpcomingTrainings::class, 'enroll'))->isPublic())->toBeTrue()
        ->and((new ReflectionMethod(UpcomingTrainings::class, 'enroll'))->getNumberOfParameters())->toBe(1);
});

it('accepts applications beyond capacity but never selects beyond capacity', function () {
    $admin = User::factory()->create();
    $firstTeacher = registeredAffiliatedTeacher();
    $secondTeacher = registeredAffiliatedTeacher();
    $training = Training::factory()->create([
        'capacity' => 1,
        'start_date' => now()->addDays(5),
        'end_date' => now()->addDays(5)->addHours(4),
        'registration_deadline' => now()->addDays(4),
    ]);

    Livewire::actingAs($firstTeacher)->test(UpcomingTrainings::class)->call('enroll', $training->id);
    Livewire::actingAs($secondTeacher)->test(UpcomingTrainings::class)->call('enroll', $training->id);

    expect($training->participants()->wherePivot('status', 'Pending')->count())->toBe(2);

    Livewire::actingAs($admin)->test(TrainingRegistrationManagement::class)
        ->call('approve', $training->id, $firstTeacher->id)
        ->call('approve', $training->id, $secondTeacher->id)
        ->assertHasErrors('registrationStatus');

    expect($training->participants()->wherePivot('status', 'Approved')->count())->toBe(1);
});

it('enforces registration lifecycle transitions', function () {
    $admin = User::factory()->create();
    $teacher = registeredAffiliatedTeacher();
    $training = Training::factory()->create([
        'start_date' => now()->addDay(),
        'end_date' => now()->addDay()->addHours(4),
    ]);
    $training->participants()->attach($teacher, ['status' => 'Pending']);

    Livewire::actingAs($admin)->test(TrainingRegistrationManagement::class)
        ->call('complete', $training->id, $teacher->id)
        ->assertHasErrors('registrationStatus');

    expect($training->participants()->whereKey($teacher->id)->first()->pivot->status)->toBe('Pending');
});

it('does not register a teacher after the deadline', function () {
    $user = registeredAffiliatedTeacher();
    $training = Training::factory()->create([
        'start_date' => now()->addDays(5),
        'end_date' => now()->addDays(5)->addHours(4),
        'registration_deadline' => now()->subMinute(),
    ]);
    Livewire::actingAs($user)->test(UpcomingTrainings::class)
        ->call('enroll', $training->id);

    expect($training->participants()->exists())->toBeFalse();
});

it('lets an admin approve and complete a registration after the training ends', function () {
    $admin = User::factory()->create();
    $teacherUser = registeredAffiliatedTeacher();
    $trainingType = trainingCatalogItem('Profile Training');
    $training = Training::factory()->create([
        'training_type_id' => $trainingType->id,
        'start_date' => now()->addDay(),
        'end_date' => now()->addDay()->addHours(4),
        'registration_deadline' => now()->addHours(12),
    ]);
    Livewire::actingAs($teacherUser)->test(UpcomingTrainings::class)->call('enroll', $training->id);
    Livewire::actingAs($admin)->test(TrainingManagement::class)->call('approve', $training->id, $teacherUser->id);

    expect($training->participants()->whereKey($teacherUser->id)->first()->pivot->status)->toBe('Approved');
    expect($teacherUser->unreadNotifications()->count())->toBe(1)
        ->and($teacherUser->unreadNotifications()->first()->data['status'])->toBe('Approved');

    Carbon::setTestNow('2026-08-14 09:00:00');
    Livewire::actingAs($admin)->test(TrainingManagement::class)->call('complete', $training->id, $teacherUser->id);

    $registration = $training->participants()->whereKey($teacherUser->id)->first()->pivot;
    expect($registration->status)->toBe('Completed')
        ->and($registration->certificate_number)->not->toBeNull()
        ->and($teacherUser->unreadNotifications()->count())->toBe(2)
        ->and($teacherUser->unreadNotifications()->latest()->first()->data['status'])->toBe('Completed');
    expect($teacherUser->teacherProfile->fresh()->trainingTypes()
        ->whereKey($trainingType->id)
        ->wherePivot('training_year', 2026)
        ->exists())->toBeTrue();

    $this->actingAs($teacherUser)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee($training->title)
        ->assertDontSee(__('My Training Certificates'));

    $this->actingAs($teacherUser)
        ->get(route('training.certificates'))
        ->assertSuccessful()
        ->assertSee($training->title)
        ->assertSee(__('Download Certificate'))
        ->assertSee($registration->certificate_number);
});

it('queues training status notifications for database and email delivery', function () {
    $admin = User::factory()->create();
    $teacherUser = registeredAffiliatedTeacher();
    $training = Training::factory()->create();
    $training->participants()->attach($teacherUser, ['status' => 'Pending']);
    config(['mail.training_notifications_enabled' => true]);
    Notification::fake();

    Livewire::actingAs($admin)->test(TrainingManagement::class)
        ->call('approve', $training->id, $teacherUser->id);

    Notification::assertSentTo(
        $teacherUser,
        TrainingRegistrationStatusNotification::class,
        fn (TrainingRegistrationStatusNotification $notification, array $channels): bool => $notification->status === 'Approved'
            && $channels === ['database', 'mail'],
    );
});

it('lets a teacher read a training notification from the header', function () {
    $teacherUser = registeredAffiliatedTeacher();
    $training = Training::factory()->create();
    $teacherUser->notify(new TrainingRegistrationStatusNotification($training, 'Approved'));
    $notification = $teacherUser->unreadNotifications()->firstOrFail();

    Livewire::actingAs($teacherUser)->test(NotificationMenu::class)
        ->assertSee($training->title)
        ->call('open', $notification->id)
        ->assertRedirect(route('training.calendar'));

    expect($notification->fresh()->read_at)->not->toBeNull();
});

it('lets an admin manage registrations and training status from the registered teachers page', function () {
    $admin = User::factory()->create();
    $teacherUser = registeredAffiliatedTeacher();
    $training = Training::factory()->create([
        'title' => 'Dashboard Managed Training',
        'start_date' => now()->addDay(),
        'end_date' => now()->addDay()->addHours(4),
    ]);
    $training->participants()->attach($teacherUser, ['status' => 'Pending']);

    Livewire::actingAs($admin)->test(TrainingRegistrationManagement::class)
        ->assertSee('Dashboard Managed Training')
        ->assertSee($teacherUser->name)
        ->call('updateRegistrationStatus', $training->id, $teacherUser->id, 'Approved');

    expect($training->participants()->whereKey($teacherUser->id)->first()->pivot->status)->toBe('Approved');
});

it('shows registration details and lets an admin delete a registration', function () {
    $admin = User::factory()->create();
    $teacherUser = registeredAffiliatedTeacher();
    $training = Training::factory()->create();
    $training->participants()->attach($teacherUser, ['status' => 'Pending']);

    Livewire::actingAs($admin)->test(TrainingRegistrationManagement::class)
        ->call('viewRegistration', $training->id, $teacherUser->id)
        ->assertSet('showRegistrationModal', true)
        ->assertSee($teacherUser->email)
        ->call('confirmDelete', $training->id, $teacherUser->id)
        ->assertSet('showDeleteModal', true)
        ->call('deleteRegistration')
        ->assertDontSee($teacherUser->name);

    expect($training->participants()->whereKey($teacherUser->id)->exists())->toBeFalse();
});

it('removes completed teachers from the registered teachers table', function () {
    $admin = User::factory()->create();
    $teacherUser = registeredAffiliatedTeacher();
    $training = Training::factory()->create([
        'title' => 'Finished Registration',
        'start_date' => now()->subDays(2),
        'end_date' => now()->subDay(),
    ]);
    $training->participants()->attach($teacherUser, ['status' => 'Approved']);

    Livewire::actingAs($admin)->test(TrainingRegistrationManagement::class)
        ->assertSee($teacherUser->name)
        ->call('complete', $training->id, $teacherUser->id)
        ->assertDontSee($teacherUser->name);
});

it('protects the registered teachers menu page', function () {
    $this->get(route('training.registrations'))->assertRedirect(route('login'));

    $this->actingAs(User::factory()->create())
        ->get(route('training.registrations'))
        ->assertSuccessful()
        ->assertSee(__('Registered Teachers'));
});

it('does not render training registrations on the admin dashboard', function () {
    $admin = User::factory()->create();
    $teacherUser = registeredAffiliatedTeacher();
    $training = Training::factory()->create(['title' => 'Dashboard Hidden Training']);
    $training->participants()->attach($teacherUser, ['status' => 'Pending']);

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertDontSee('Dashboard Hidden Training')
        ->assertDontSee(__('Registered Teachers'))
        ->assertDontSee(__('Review and verify teacher registrations before training participation.'));
});

it('hides trainings from teachers outside active affiliated colleges', function () {
    $teacherUser = User::factory()->withRole('teacher')->create();
    Teacher::query()->create([
        'user_id' => $teacherUser->id,
        'name' => $teacherUser->name,
        'approval_status' => ApprovalStatus::Approved,
    ]);
    Training::factory()->create(['title' => 'Affiliated Teachers Only']);

    Livewire::actingAs($teacherUser)->test(UpcomingTrainings::class)
        ->assertDontSee('Affiliated Teachers Only');
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

function registeredAffiliatedTeacher(): User
{
    $college = College::query()->create([
        'name' => fake()->company(),
        'approval_status' => ApprovalStatus::Approved,
        'is_active' => true,
    ]);
    $user = User::factory()->withRole('teacher')->create(['college_id' => $college->id]);
    Teacher::query()->create([
        'user_id' => $user->id,
        'college_id' => $college->id,
        'name' => $user->name,
        'approval_status' => ApprovalStatus::Approved,
    ]);

    return $user;
}

function trainingCatalogItem(string $name): TrainingType
{
    $institute = TrainingInstitute::query()->firstOrCreate(['name' => 'National University']);

    return TrainingType::query()->create([
        'training_institute_id' => $institute->id,
        'name' => $name,
        'duration_value' => 1,
        'duration_unit' => 'days',
    ]);
}

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
