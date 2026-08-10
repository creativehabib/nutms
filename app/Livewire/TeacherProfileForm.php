<?php

namespace App\Livewire;

use App\Concerns\PasswordValidationRules;
use App\Enums\ApprovalStatus;
use App\Enums\UserRole as Role;
use App\Models\College;
use App\Models\Designation;
use App\Models\District;
use App\Models\Division;
use App\Models\Employment;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherLevel;
use App\Models\Thana;
use App\Models\TeacherOtherTraining;
use App\Models\TrainingInstitute;
use App\Models\TrainingType;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

class TeacherProfileForm extends Component
{
    use PasswordValidationRules, WithFileUploads;

    #[Locked]
    public ?int $editingId = null;
    public string $collegeId = '';
    public string $ttisId = '';
    public string $name = '';
    public string $birthDate = '';
    public string $designation = '';
    public string $subject = '';
    public string $teacherLevel = '';
    public string $employmentType = '';
    public string $divisionId = '';
    public string $districtId = '';
    public string $thanaId = '';
    public string $presentAddress = '';
    public string $permanentAddress = '';
    public string $mobileNumber = '';
    public string $email = '';
    public string $bankName = '';
    public string $bankBranchName = '';
    public string $bankAccountNumber = '';
    public string $bankRoutingNumber = '';
    public $profileImage;
    public $digitalSignature;
    public ?string $currentProfileImage = null;
    public ?string $currentDigitalSignature = null;
    public string $accountEmail = '';
    public string $accountPassword = '';
    public string $accountPassword_confirmation = '';

    /** @var array<int, array<string, string>> */
    public array $trainingEntries = [];

    public function mount(?Teacher $teacher = null): void
    {
        $user = auth()->user();

        abort_unless($user->can($teacher?->exists ? 'teachers.update' : 'teachers.create'), 403);

        if ($user->role === Role::Teacher && $user->college_id !== null) {
            $this->collegeId = (string) $user->college_id;
        }
        if ($user->role === Role::Teacher && (! $teacher?->exists)) {
            $this->name = $user->name;
        }
        if ((! $teacher?->exists) && $user->role === Role::Teacher && $user->teacherProfile !== null) {
            $teacher = $user->teacherProfile;
            abort_unless($user->can('teachers.update'), 403);
            abort_if($teacher?->approval_status === ApprovalStatus::Pending, 403, 'প্রোফাইলটি অনুমোদনের অপেক্ষায় আছে।');
        }
        if ($teacher?->exists) {
            abort_unless($user->isAdmin() || ($user->role === Role::Principal && $teacher->college_id === $user->college_id) || ($user->role === Role::Teacher && $teacher->user_id === $user->id && $teacher->approval_status === ApprovalStatus::Approved), 403);
        }
        if ($teacher !== null && $teacher->exists) {
            $this->editingId = $teacher->id;
            $this->collegeId = (string) ($teacher->college_id ?? '');
            $this->ttisId = (string) ($teacher->ttis_id ?? '');
            $this->name = $teacher->display_name;
            $this->birthDate = $teacher->birth_date?->format('Y-m-d') ?? '';
            $this->designation = (string) ($teacher->designation ?? '');
            $this->subject = (string) ($teacher->subject ?? '');
            $this->teacherLevel = (string) ($teacher->teacher_level ?? '');
            $this->employmentType = (string) ($teacher->employment_type ?? '');
            $this->divisionId = (string) ($teacher->division_id ?? '');
            $this->districtId = (string) ($teacher->district_id ?? '');
            $this->thanaId = (string) ($teacher->thana_id ?? '');
            $this->presentAddress = (string) ($teacher->present_address ?? '');
            $this->permanentAddress = (string) ($teacher->permanent_address ?? '');
            $this->mobileNumber = (string) ($teacher->user?->mobile_no ?? '');
            $this->email = (string) ($teacher->user?->email ?? '');
            $this->accountEmail = (string) ($teacher->user?->email ?? '');
            $this->bankName = (string) ($teacher->bank_name ?? '');
            $this->bankBranchName = (string) ($teacher->bank_branch_name ?? '');
            $this->bankAccountNumber = (string) ($teacher->bank_account_number ?? '');
            $this->bankRoutingNumber = (string) ($teacher->bank_routing_number ?? '');
            $teacher->load(['trainingTypes', 'otherTrainings']);
            $this->trainingEntries = $teacher->trainingTypes->map(fn (TrainingType $training): array => [
                'kind' => 'catalog', 'training_institute_id' => (string) $training->training_institute_id,
                'institute_name' => '', 'training_type_id' => (string) $training->id, 'name' => '',
                'duration_value' => '', 'duration_unit' => 'days', 'training_year' => (string) $training->pivot->training_year,
            ])->concat($teacher->otherTrainings->map(fn (TeacherOtherTraining $training): array => [
                'kind' => 'other', 'training_institute_id' => (string) ($training->training_institute_id ?? ''),
                'institute_name' => (string) ($training->institute_name ?? ''), 'training_type_id' => '', 'name' => $training->name,
                'duration_value' => (string) ($training->duration_value ?? ''), 'duration_unit' => (string) ($training->duration_unit ?? 'days'),
                'training_year' => (string) $training->training_year,
            ]))->values()->all();
        }

        if ($user->role === Role::Teacher) {
            $this->email = $user->email;
            $this->accountEmail = $user->email;
            $this->mobileNumber = (string) ($user->mobile_no ?? $this->mobileNumber);
            $this->currentProfileImage = $user->picture;
            $this->currentDigitalSignature = $user->digital_signature;
        } elseif ($teacher?->user !== null) {
            $this->currentProfileImage = $teacher->user->picture;
            $this->currentDigitalSignature = $teacher->user->digital_signature;
        }

        if ($this->trainingEntries === []) {
            $this->addTrainingEntry();
        }
    }

    public function updatedDivisionId(): void
    {
        $this->reset('districtId', 'thanaId');
    }

    public function updatedDistrictId(): void
    {
        $this->reset('thanaId');
    }

    public function addTrainingEntry(): void
    {
        $this->trainingEntries[] = ['kind' => 'catalog', 'training_institute_id' => '', 'institute_name' => '', 'training_type_id' => '', 'name' => '', 'duration_value' => '', 'duration_unit' => 'days', 'training_year' => ''];
    }

    public function removeTrainingEntry(int $index): void
    {
        unset($this->trainingEntries[$index]);
        $this->trainingEntries = array_values($this->trainingEntries);
    }

    public function updatedTrainingEntries(mixed $value, ?string $key = null): void
    {
        if ($key !== null && preg_match('/^(\d+)\.(kind|training_institute_id)$/', $key, $matches) === 1) {
            $index = (int) $matches[1];
            $this->trainingEntries[$index]['training_type_id'] = '';
            if ($matches[2] === 'kind') {
                $this->trainingEntries[$index]['name'] = '';
                $this->trainingEntries[$index]['duration_value'] = '';
            }
        }
    }

    // নতুন মেথড: প্রতিটি ট্যাবের ডেটা আলাদাভাবে ভ্যালিডেট করার জন্য
    public function validateStep(string $step): void
    {
        $isStaffCreatingTeacherAccount = $this->editingId === null && auth()->user()->role !== Role::Teacher;
        $profileRequiredRule = $isStaffCreatingTeacherAccount ? 'nullable' : 'required';

        $profileUserId = null;
        if ($this->editingId !== null) {
            $profileUserId = Teacher::query()->whereKey($this->editingId)->value('user_id');
        }
        if ($profileUserId === null && auth()->user()->role === Role::Teacher) {
            $profileUserId = auth()->id();
        }

        $emailRequiredRule = $profileUserId !== null && ! $isStaffCreatingTeacherAccount ? 'required' : 'nullable';

        if ($step === 'basic') {
            $this->validate([
                'collegeId' => ['required', Rule::exists('colleges', 'id')->where('is_active', true)],
                'name' => ['required', 'string', 'max:255'],
                'birthDate' => ['nullable', 'date', 'before:today'],
                'profileImage' => ['nullable', 'image', 'max:2048'],
                'digitalSignature' => ['nullable', 'image', 'max:2048'],
                'accountEmail' => [$isStaffCreatingTeacherAccount ? 'required' : 'nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($profileUserId)],
                'accountPassword' => $isStaffCreatingTeacherAccount ? $this->passwordRules() : ['nullable'],
            ]);
        } elseif ($step === 'professional') {
            $this->validate([
                'designation' => ['nullable', 'string', 'max:255'],
                'subject' => ['nullable', 'string', 'max:255'],
                'teacherLevel' => ['nullable', 'string', 'max:255'],
                'employmentType' => ['nullable', 'string', 'max:255'],
            ]);
        } elseif ($step === 'contact') {
            $this->validate([
                'divisionId' => [$profileRequiredRule, Rule::exists('divisions', 'id')],
                'districtId' => [$profileRequiredRule, Rule::exists('districts', 'id')],
                'thanaId' => [$profileRequiredRule, Rule::exists('thanas', 'id')],
                'presentAddress' => [$profileRequiredRule, 'string', 'max:2000'],
                'permanentAddress' => [$profileRequiredRule, 'string', 'max:2000'],
                'mobileNumber' => ['required', 'string', 'max:50', Rule::unique('users', 'mobile_no')->ignore($profileUserId)],
                'email' => [$emailRequiredRule, 'email', 'max:255', Rule::unique('users', 'email')->ignore($profileUserId)],
            ]);

            if (filled($this->districtId) && ! District::query()->whereKey($this->districtId)->where('division_id', $this->divisionId)->exists()) {
                throw ValidationException::withMessages(['districtId' => 'নির্বাচিত জেলা এই বিভাগের অন্তর্ভুক্ত নয়।']);
            }
            if (filled($this->thanaId) && ! Thana::query()->whereKey($this->thanaId)->where('district_id', $this->districtId)->exists()) {
                throw ValidationException::withMessages(['thanaId' => 'নির্বাচিত থানা এই জেলার অন্তর্ভুক্ত নয়।']);
            }
        } elseif ($step === 'training') {
            $this->validate([
                'trainingEntries' => ['array'],
                'trainingEntries.*.kind' => ['required', Rule::in(['catalog', 'other'])],
                'trainingEntries.*.training_institute_id' => ['nullable', Rule::exists('training_institutes', 'id')],
                'trainingEntries.*.institute_name' => ['nullable', 'string', 'max:255'],
                'trainingEntries.*.training_type_id' => ['nullable', Rule::exists('training_types', 'id')],
                'trainingEntries.*.name' => ['nullable', 'string', 'max:255'],
                'trainingEntries.*.duration_value' => ['nullable', 'integer', 'min:1', 'max:999'],
                'trainingEntries.*.duration_unit' => ['nullable', Rule::in(['hours', 'days', 'weeks', 'months'])],
                'trainingEntries.*.training_year' => ['nullable', 'integer', 'min:1950', 'max:'.((int) date('Y') + 1)],
            ]);

            foreach ($this->trainingEntries as $index => $entry) {
                $hasValue = filled($entry['training_institute_id'] ?? null) || filled($entry['training_type_id'] ?? null) || filled($entry['name'] ?? null) || filled($entry['training_year'] ?? null);
                if (! $hasValue) {
                    continue;
                }
                if ($entry['kind'] === 'catalog' && (! filled($entry['training_type_id'] ?? null) || ! filled($entry['training_year'] ?? null) || ! TrainingType::query()->whereKey($entry['training_type_id'])->where('training_institute_id', $entry['training_institute_id'])->exists())) {
                    throw ValidationException::withMessages(["trainingEntries.{$index}.training_type_id" => 'প্রতিষ্ঠান, ট্রেনিং এবং বছর সঠিকভাবে নির্বাচন করুন।']);
                }
                if ($entry['kind'] === 'other' && (! filled($entry['name'] ?? null) || ! filled($entry['training_year'] ?? null))) {
                    throw ValidationException::withMessages(["trainingEntries.{$index}.name" => 'অন্যান্য ট্রেনিংয়ের নাম ও বছর দিন।']);
                }
            }
        } elseif ($step === 'bank') {
            $this->validate([
                'bankName' => ['nullable', 'string', 'max:255'],
                'bankBranchName' => ['nullable', 'string', 'max:255'],
                'bankAccountNumber' => ['nullable', 'string', 'max:100'],
                'bankRoutingNumber' => ['nullable', 'string', 'max:30'],
            ]);
        }
    }

    public function save(): void
    {
        abort_unless(auth()->user()->can($this->editingId === null ? 'teachers.create' : 'teachers.update'), 403);

        $isStaffCreatingTeacherAccount = $this->editingId === null && auth()->user()->role !== Role::Teacher;
        $profileRequiredRule = $isStaffCreatingTeacherAccount ? 'nullable' : 'required';

        $profileUserId = null;
        if ($this->editingId !== null) {
            $profileUserId = Teacher::query()->whereKey($this->editingId)->value('user_id');
        }
        if ($profileUserId === null && auth()->user()->role === Role::Teacher) {
            $profileUserId = auth()->id();
        }

        $emailRequiredRule = $profileUserId !== null && ! $isStaffCreatingTeacherAccount ? 'required' : 'nullable';

        $validated = $this->validate([
            'collegeId' => ['required', Rule::exists('colleges', 'id')->where('is_active', true)],
            'name' => ['required', 'string', 'max:255'],
            'birthDate' => ['nullable', 'date', 'before:today'],
            'designation' => ['nullable', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'teacherLevel' => ['nullable', 'string', 'max:255'],
            'employmentType' => ['nullable', 'string', 'max:255'],
            'divisionId' => [$profileRequiredRule, Rule::exists('divisions', 'id')],
            'districtId' => [$profileRequiredRule, Rule::exists('districts', 'id')],
            'thanaId' => [$profileRequiredRule, Rule::exists('thanas', 'id')],
            'presentAddress' => [$profileRequiredRule, 'string', 'max:2000'],
            'permanentAddress' => [$profileRequiredRule, 'string', 'max:2000'],
            'mobileNumber' => ['required', 'string', 'max:50', Rule::unique('users', 'mobile_no')->ignore($profileUserId)],
            'email' => [$emailRequiredRule, 'email', 'max:255', Rule::unique('users', 'email')->ignore($profileUserId)],
            'accountEmail' => [$isStaffCreatingTeacherAccount ? 'required' : 'nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($profileUserId)],
            'accountPassword' => $isStaffCreatingTeacherAccount ? $this->passwordRules() : ['nullable'],
            'bankName' => ['nullable', 'string', 'max:255'],
            'bankBranchName' => ['nullable', 'string', 'max:255'],
            'bankAccountNumber' => ['nullable', 'string', 'max:100'],
            'bankRoutingNumber' => ['nullable', 'string', 'max:30'],
            'profileImage' => ['nullable', 'image', 'max:2048'],
            'digitalSignature' => ['nullable', 'image', 'max:2048'],
            'trainingEntries' => ['array'],
            'trainingEntries.*.kind' => ['required', Rule::in(['catalog', 'other'])],
            'trainingEntries.*.training_institute_id' => ['nullable', Rule::exists('training_institutes', 'id')],
            'trainingEntries.*.institute_name' => ['nullable', 'string', 'max:255'],
            'trainingEntries.*.training_type_id' => ['nullable', Rule::exists('training_types', 'id')],
            'trainingEntries.*.name' => ['nullable', 'string', 'max:255'],
            'trainingEntries.*.duration_value' => ['nullable', 'integer', 'min:1', 'max:999'],
            'trainingEntries.*.duration_unit' => ['nullable', Rule::in(['hours', 'days', 'weeks', 'months'])],
            'trainingEntries.*.training_year' => ['nullable', 'integer', 'min:1950', 'max:'.((int) date('Y') + 1)],
        ]);

        if (filled($validated['districtId'] ?? null) && ! District::query()->whereKey($validated['districtId'])->where('division_id', $validated['divisionId'])->exists()) {
            throw ValidationException::withMessages(['districtId' => 'নির্বাচিত জেলা এই বিভাগের অন্তর্ভুক্ত নয়।']);
        }
        if (filled($validated['thanaId'] ?? null) && ! Thana::query()->whereKey($validated['thanaId'])->where('district_id', $validated['districtId'])->exists()) {
            throw ValidationException::withMessages(['thanaId' => 'নির্বাচিত থানা এই জেলার অন্তর্ভুক্ত নয়।']);
        }

        foreach ($validated['trainingEntries'] as $index => $entry) {
            $hasValue = filled($entry['training_institute_id'] ?? null) || filled($entry['training_type_id'] ?? null) || filled($entry['name'] ?? null) || filled($entry['training_year'] ?? null);
            if (! $hasValue) {
                continue;
            }
            if ($entry['kind'] === 'catalog' && (! filled($entry['training_type_id'] ?? null) || ! filled($entry['training_year'] ?? null) || ! TrainingType::query()->whereKey($entry['training_type_id'])->where('training_institute_id', $entry['training_institute_id'])->exists())) {
                throw ValidationException::withMessages(["trainingEntries.{$index}.training_type_id" => 'প্রতিষ্ঠান, ট্রেনিং এবং বছর সঠিকভাবে নির্বাচন করুন।']);
            }
            if ($entry['kind'] === 'other' && (! filled($entry['name'] ?? null) || ! filled($entry['training_year'] ?? null))) {
                throw ValidationException::withMessages(["trainingEntries.{$index}.name" => 'অন্যান্য ট্রেনিংয়ের নাম ও বছর দিন।']);
            }
        }

        // সিকিউরিটি: যদি লগইন করা ইউজার শিক্ষক (Teacher) হন, তবে তার ইমেইল এবং মোবাইল নম্বর
        // জোরপূর্বক তার বর্তমান User অ্যাকাউন্ট থেকেই নেওয়া হবে, ফর্ম থেকে নয়।
        if (auth()->user()->role === Role::Teacher) {
            $validated['email'] = auth()->user()->email;
            $validated['mobileNumber'] = auth()->user()->mobile_no;
        }

        DB::transaction(function () use ($validated, $isStaffCreatingTeacherAccount): void {
            $user = auth()->user();
            $college = College::query()->findOrFail($validated['collegeId']);

            if ($user->role === Role::Teacher) {
                abort_unless($college->id === $user->college_id && $college->approval_status === ApprovalStatus::Approved, 403);
            } elseif ($user->role === Role::Principal) {
                abort_unless($college->id === $user->college_id && $college->approval_status === ApprovalStatus::Approved, 403);
            }

            $isApprovedTeacherEditingOwnProfile = $user->role === Role::Teacher
                && $this->editingId !== null
                && Teacher::query()
                    ->whereKey($this->editingId)
                    ->where('user_id', $user->id)
                    ->where('approval_status', ApprovalStatus::Approved)
                    ->exists();

            $teacherAccount = null;
            if ($isStaffCreatingTeacherAccount) {
                $teacherAccount = User::query()->create([
                    'name' => $validated['name'],
                    'email' => $validated['accountEmail'],
                    'password' => $validated['accountPassword'],
                    'mobile_no' => $validated['mobileNumber'],
                    'role' => Role::Teacher,
                    'college_id' => $college->id,
                    'approval_status' => ApprovalStatus::Approved,
                    'approved_by' => $user->id,
                    'approved_at' => now(),
                ]);
            }

            $teacher = Teacher::query()->updateOrCreate(['id' => $this->editingId], [
                'college_id' => $college->id, 'college_code' => $college->code, 'college_name' => $college->name,
                'name' => $validated['name'], 'birth_date' => $validated['birthDate'] ?: null, 'designation' => $validated['designation'] ?: null,
                'subject' => $validated['subject'] ?: null, 'teacher_level' => $validated['teacherLevel'] ?: null,
                'employment_type' => $validated['employmentType'] ?: null,
                'division_id' => $validated['divisionId'] ?: null, 'district_id' => $validated['districtId'] ?: null, 'thana_id' => $validated['thanaId'] ?: null,
                'present_address' => $validated['presentAddress'] ?: null, 'permanent_address' => $validated['permanentAddress'] ?: null,
                'bank_name' => $validated['bankName'] ?: null, 'bank_branch_name' => $validated['bankBranchName'] ?: null,
                'bank_account_number' => $validated['bankAccountNumber'] ?: null,
                'bank_routing_number' => $validated['bankRoutingNumber'] ?: null,
                'user_id' => $this->editingId ? Teacher::query()->whereKey($this->editingId)->value('user_id') : ($teacherAccount?->id ?? ($user->role === Role::Teacher ? $user->id : null)),
                'approval_status' => $user->role === Role::Teacher && ! $isApprovedTeacherEditingOwnProfile ? ApprovalStatus::Pending : ApprovalStatus::Approved,
                'approved_by' => $user->role === Role::Teacher ? Teacher::query()->whereKey($this->editingId)->value('approved_by') : $user->id,
                'approved_at' => $user->role === Role::Teacher ? Teacher::query()->whereKey($this->editingId)->value('approved_at') : now(),
            ]);

            $linkedUser = $teacherAccount ?? ($teacher->user_id !== null ? User::query()->find($teacher->user_id) : null);

            if ($linkedUser !== null) {
                $linkedUserUpdates = [
                    'email' => $isStaffCreatingTeacherAccount ? $validated['accountEmail'] : $validated['email'],
                    'mobile_no' => $validated['mobileNumber'],
                    'college_id' => $college->id,
                ];

                if ($this->profileImage) {
                    if ($linkedUser->picture && Storage::disk('public')->exists($linkedUser->picture)) {
                        Storage::disk('public')->delete($linkedUser->picture);
                    }
                    $linkedUserUpdates['picture'] = $this->profileImage->store('profile-images', 'public');
                }

                if ($this->digitalSignature) {
                    if ($linkedUser->digital_signature && Storage::disk('public')->exists($linkedUser->digital_signature)) {
                        Storage::disk('public')->delete($linkedUser->digital_signature);
                    }
                    $linkedUserUpdates['digital_signature'] = $this->digitalSignature->store('signatures', 'public');
                }

                $linkedUser->update($linkedUserUpdates);
            }

            $teacher->trainingTypes()->detach();
            $teacher->otherTrainings()->delete();

            foreach ($validated['trainingEntries'] as $entry) {
                if ($entry['kind'] === 'catalog' && filled($entry['training_type_id'] ?? null) && filled($entry['training_year'] ?? null)) {
                    $teacher->trainingTypes()->attach((int) $entry['training_type_id'], ['training_year' => (int) $entry['training_year']]);
                } elseif ($entry['kind'] === 'other' && filled($entry['name'] ?? null) && filled($entry['training_year'] ?? null)) {
                    $teacher->otherTrainings()->create([
                        'training_institute_id' => filled($entry['training_institute_id'] ?? null) ? (int) $entry['training_institute_id'] : null,
                        'institute_name' => filled($entry['training_institute_id'] ?? null) ? null : ($entry['institute_name'] ?: null),
                        'name' => $entry['name'], 'duration_value' => $entry['duration_value'] ?: null,
                        'duration_unit' => filled($entry['duration_value'] ?? null) ? $entry['duration_unit'] : null,
                        'training_year' => (int) $entry['training_year'],
                    ]);
                }
            }
        });

        $isNewTeacherSubmission = auth()->user()->role === Role::Teacher && $this->editingId === null;
        Flux::toast(variant: 'success', text: $isNewTeacherSubmission ? 'প্রোফাইলটি প্রিন্সিপালের অনুমোদনের জন্য জমা হয়েছে।' : 'শিক্ষকের প্রোফাইল সংরক্ষণ করা হয়েছে।');
        $this->redirectRoute(auth()->user()->role === Role::Teacher ? 'dashboard' : 'teachers.manage', navigate: true);
    }

    public function submit(): void
    {
        try {
            $this->save();
        } catch (ValidationException $exception) {
            $this->setErrorBag($exception->errors());
            $this->dispatch(
                'teacher-profile-validation-failed',
                step: $this->validationStepFor(array_keys($exception->errors())),
            );
        }
    }

    public function render(): View
    {
        return view('livewire.teacher-profile-form', [
            'colleges' => College::query()->where('is_active', true)->where('approval_status', ApprovalStatus::Approved)
                ->when(auth()->user()->role === Role::Principal, fn ($query) => $query->whereKey(auth()->user()->college_id))
                ->when(auth()->user()->role === Role::Teacher, fn ($query) => $query->whereKey(auth()->user()->college_id))
                ->orderBy('name')->get(['id', 'code', 'name']),
            'designations' => Designation::query()->where('is_active', true)->orderBy('name')->pluck('name'),
            'subjects' => Subject::query()->where('is_active', true)->orderBy('name')->pluck('name'),
            'teacherLevels' => TeacherLevel::query()->where('is_active', true)->orderBy('name')->pluck('name'),
            'employments' => Employment::query()->where('is_active', true)->orderBy('name')->pluck('name'),
            'divisions' => Division::query()->where('status', true)->orderBy('name')->get(['id', 'name', 'bn_name']),
            'districts' => District::query()->where('division_id', $this->divisionId ?: 0)->where('status', true)->orderBy('name')->get(['id', 'name', 'bn_name']),
            'thanas' => Thana::query()->where('district_id', $this->districtId ?: 0)->where('status', true)->orderBy('name')->get(['id', 'name', 'bn_name']),
            'trainingInstitutes' => TrainingInstitute::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'trainingTypes' => TrainingType::query()->where('is_active', true)->orderBy('name')->get(['id', 'training_institute_id', 'name', 'duration_value', 'duration_unit']),
        ]);
    }

    /**
     * @param  array<int, string>  $fields
     */
    private function validationStepFor(array $fields): string
    {
        $stepFields = [
            'basic' => ['collegeId', 'name', 'birthDate', 'profileImage', 'digitalSignature', 'accountEmail', 'accountPassword'],
            'professional' => ['designation', 'subject', 'teacherLevel', 'employmentType'],
            'contact' => ['divisionId', 'districtId', 'thanaId', 'presentAddress', 'permanentAddress', 'mobileNumber', 'email'],
            'training' => ['trainingEntries'],
            'bank' => ['bankName', 'bankBranchName', 'bankAccountNumber', 'bankRoutingNumber'],
        ];

        foreach ($stepFields as $step => $prefixes) {
            foreach ($fields as $field) {
                foreach ($prefixes as $prefix) {
                    if ($field === $prefix || str_starts_with($field, "{$prefix}.")) {
                        return $step;
                    }
                }
            }
        }

        return 'basic';
    }
}
