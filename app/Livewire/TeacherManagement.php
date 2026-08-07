<?php

namespace App\Livewire;

use App\Enums\ApprovalStatus;
use App\Enums\UserRole as Role;
use App\Models\College;
use App\Models\Designation;
use App\Models\Employment;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherLevel;
use App\Models\TeacherOtherTraining;
use App\Models\TrainingInstitute;
use App\Models\TrainingType;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class TeacherManagement extends Component
{
    use WithPagination;

    // ফিল্টার এবং সার্চের জন্য প্রপার্টি
    public string $search = '';

    public string $subjectFilter = '';

    public string $collegeCodeFilter = '';

    /** @var array<int, string> */
    public array $selectedTeacherIds = [];

    public bool $selectAllOnPage = false;

    public bool $showTrashed = false;

    // এডিট করার জন্য নতুন প্রপার্টি
    public $editingId = null;

    #[Locked]
    public array $deletingTeacherIds = [];

    #[Locked]
    public string $deletingTeacherName = '';

    #[Locked]
    public bool $permanentDeletion = false;

    public $editForm = [
        'college_code' => '',
        'college_name' => '',
        'name' => '',
        'designation' => '',
        'subject' => '',
        'teacher_level' => '',
        'employment_type' => '',
        'ict_training_name' => '',
        'other_training_name' => '',
        'training_institute' => '',
        'mobile_number' => '',
        'email' => '',
    ];

    /** @var array<int, array<string, string>> */
    public array $trainingEntries = [];

    // কোনো ফিল্টারে পরিবর্তন হলে পেজ ১-এ ফিরে যাবে
    public function updatedSearch(): void
    {
        $this->resetFiltersAndSelection();
    }

    public function updatedSubjectFilter(): void
    {
        $this->resetFiltersAndSelection();
    }

    public function updatedCollegeCodeFilter(): void
    {
        if (auth()->user()->role === Role::Principal) {
            $this->collegeCodeFilter = '';
        }

        $this->resetFiltersAndSelection();
    }

    public function approveTeacher(int $teacherId): void
    {
        abort_unless(auth()->user()->can('teachers.approve'), 403);
        $teacher = $this->accessibleTeachersQuery()->where('approval_status', ApprovalStatus::Pending)->findOrFail($teacherId);
        $teacher->update(['approval_status' => ApprovalStatus::Approved, 'approved_by' => auth()->id(), 'approved_at' => now()]);
        $teacher->user?->update(['college_id' => $teacher->college_id]);
        Flux::toast(variant: 'success', text: 'শিক্ষক প্রোফাইল অনুমোদিত হয়েছে।');
    }

    public function toggleTeacherApproval(int $teacherId): void
    {
        abort_unless(auth()->user()->isAdmin() && auth()->user()->can('teachers.approve'), 403);

        $teacher = $this->accessibleTeachersQuery()->findOrFail($teacherId);
        $isApproved = $teacher->approval_status === ApprovalStatus::Approved;

        $teacher->update([
            'approval_status' => $isApproved ? ApprovalStatus::Pending : ApprovalStatus::Approved,
            'approved_by' => $isApproved ? null : auth()->id(),
            'approved_at' => $isApproved ? null : now(),
        ]);

        if (! $isApproved) {
            $teacher->user?->update(['college_id' => $teacher->college_id]);
        }

        Flux::toast(
            variant: 'success',
            text: $isApproved ? 'শিক্ষক প্রোফাইলের অনুমোদন বাতিল হয়েছে।' : 'শিক্ষক প্রোফাইল অনুমোদিত হয়েছে।',
        );
    }

    public function rejectTeacher(int $teacherId): void
    {
        abort_unless(auth()->user()->can('teachers.approve'), 403);
        $teacher = $this->accessibleTeachersQuery()->where('approval_status', ApprovalStatus::Pending)->findOrFail($teacherId);
        $teacher->update(['approval_status' => ApprovalStatus::Rejected, 'approved_by' => auth()->id(), 'approved_at' => now()]);
    }

    public function changeTeacherRole(int $teacherId, string $role): void
    {
        abort_unless(auth()->user()->can('teachers.assign-role'), 403);

        $validated = validator(['role' => $role], [
            'role' => ['required', Rule::in([Role::Teacher->value, Role::Principal->value])],
        ])->validate();

        DB::transaction(function () use ($teacherId, $validated): void {
            $teacher = Teacher::query()->with('user')->lockForUpdate()->findOrFail($teacherId);
            $user = $teacher->user;

            if ($user === null) {
                throw ValidationException::withMessages(['role' => 'এই শিক্ষকের সঙ্গে কোনো user account সংযুক্ত নেই।']);
            }

            $newRole = Role::from($validated['role']);
            if ($newRole === Role::Principal) {
                if ($teacher->college_id === null || $teacher->approval_status !== ApprovalStatus::Approved) {
                    throw ValidationException::withMessages(['role' => 'শুধু অনুমোদিত শিক্ষককে তার কলেজের Principal করা যাবে।']);
                }

                $existingPrincipals = User::query()->whereKeyNot($user->id)
                    ->where('role', Role::Principal->value)
                    ->where('college_id', $teacher->college_id)
                    ->lockForUpdate()
                    ->get();

                foreach ($existingPrincipals as $existingPrincipal) {
                    $existingPrincipal->update(['role' => Role::Teacher]);
                    $existingPrincipal->syncRoles([Role::Teacher->value]);
                }

                $user->approval_status = ApprovalStatus::Approved;
                $user->approved_by = auth()->id();
                $user->approved_at = now();
                College::query()->whereKey($teacher->college_id)->update(['submitted_by' => $user->id]);
            } elseif ($user->role === Role::Principal) {
                College::query()->where('submitted_by', $user->id)->update(['submitted_by' => null]);
            }

            $user->role = $newRole;
            $user->college_id = $teacher->college_id;
            $user->save();
            $user->syncRoles([$newRole->value]);
        });

        Flux::toast(variant: 'success', text: 'শিক্ষকের রোল সফলভাবে পরিবর্তন করা হয়েছে।');
    }

    public function updatedSelectAllOnPage(bool $selected): void
    {
        if (! $selected) {
            $this->selectedTeacherIds = [];
            $this->dispatch('teacher-selection-updated', selected: false);

            return;
        }

        $this->selectedTeacherIds = $this->filteredTeachersQuery()
            ->latest()
            ->forPage($this->getPage(), 8)
            ->pluck('id')
            ->map(fn (int $teacherId): string => (string) $teacherId)
            ->all();

        $this->dispatch('teacher-selection-updated', selected: true);
    }

    public function updatedSelectedTeacherIds(): void
    {
        $this->selectAllOnPage = false;
    }

    public function toggleSelectAllOnPage(): void
    {
        $this->selectAllOnPage = ! $this->selectAllOnPage;
        $this->updatedSelectAllOnPage($this->selectAllOnPage);
    }

    public function toggleTeacherSelection(int $teacherId): void
    {
        $teacherId = (string) $teacherId;

        if (in_array($teacherId, $this->selectedTeacherIds, true)) {
            $this->selectedTeacherIds = array_values(array_diff($this->selectedTeacherIds, [$teacherId]));
        } else {
            $this->selectedTeacherIds[] = $teacherId;
        }

        $this->selectAllOnPage = false;
    }

    public function toggleTrashed(): void
    {
        $this->showTrashed = ! $this->showTrashed;
        $this->resetFiltersAndSelection();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'subjectFilter', 'collegeCodeFilter');
        $this->resetFiltersAndSelection();
    }

    public function confirmTeacherDeletion(int $teacherId): void
    {
        abort_unless(auth()->user()->can('teachers.delete'), 403);
        $teacher = $this->accessibleTeachersQuery()->findOrFail($teacherId);

        $this->deletingTeacherIds = [$teacher->id];
        $this->deletingTeacherName = $teacher->display_name ?: 'এই শিক্ষক';
        $this->permanentDeletion = false;

        Flux::modal('confirm-teacher-deletion')->show();
    }

    public function confirmPermanentTeacherDeletion(int $teacherId): void
    {
        abort_unless(auth()->user()->can('teachers.delete'), 403);
        $teacher = $this->accessibleTeachersQuery(true)->findOrFail($teacherId);

        $this->deletingTeacherIds = [$teacher->id];
        $this->deletingTeacherName = $teacher->display_name ?: 'এই শিক্ষক';
        $this->permanentDeletion = true;

        Flux::modal('confirm-teacher-deletion')->show();
    }

    public function confirmBulkTeacherDeletion(): void
    {
        abort_unless(auth()->user()->can('teachers.delete'), 403);
        $teacherIds = collect($this->selectedTeacherIds)
            ->map(fn ($teacherId): int => (int) $teacherId)
            ->unique()
            ->values();

        $teachers = $this->accessibleTeachersQuery()->with('user:id,name')->whereKey($teacherIds)->get(['id', 'name', 'user_id']);

        if ($teachers->isEmpty()) {
            Flux::toast(variant: 'warning', text: 'মুছে ফেলার জন্য অন্তত একজন শিক্ষক নির্বাচন করুন।');

            return;
        }

        $this->deletingTeacherIds = $teachers->pluck('id')->all();
        $this->deletingTeacherName = $teachers->count() === 1
            ? ($teachers->first()->display_name ?: 'এই শিক্ষক')
            : "নির্বাচিত {$teachers->count()} জন শিক্ষক";
        $this->permanentDeletion = false;

        Flux::modal('confirm-teacher-deletion')->show();
    }

    public function confirmBulkPermanentDeletion(): void
    {
        abort_unless(auth()->user()->can('teachers.delete'), 403);
        $teacherIds = collect($this->selectedTeacherIds)
            ->map(fn ($teacherId): int => (int) $teacherId)
            ->unique()
            ->values();

        $teachers = $this->accessibleTeachersQuery(true)->with('user:id,name')->whereKey($teacherIds)->get(['id', 'name', 'user_id']);

        if ($teachers->isEmpty()) {
            Flux::toast(variant: 'warning', text: 'স্থায়ীভাবে মুছে ফেলার জন্য অন্তত একজন শিক্ষক নির্বাচন করুন।');

            return;
        }

        $this->deletingTeacherIds = $teachers->pluck('id')->all();
        $this->deletingTeacherName = $teachers->count() === 1
            ? ($teachers->first()->display_name ?: 'এই শিক্ষক')
            : "নির্বাচিত {$teachers->count()} জন শিক্ষক";
        $this->permanentDeletion = true;

        Flux::modal('confirm-teacher-deletion')->show();
    }

    public function deleteTeacher(): void
    {
        abort_unless(auth()->user()->can('teachers.delete'), 403);
        if ($this->deletingTeacherIds === []) {
            Flux::toast(variant: 'danger', text: 'মুছে ফেলার জন্য কোনো শিক্ষক নির্বাচন করা হয়নি।');

            return;
        }

        $isPermanentDeletion = $this->permanentDeletion;
        $deletedTeacherCount = $isPermanentDeletion
            ? $this->accessibleTeachersQuery(true)->whereKey($this->deletingTeacherIds)->forceDelete()
            : $this->accessibleTeachersQuery()->whereKey($this->deletingTeacherIds)->delete();

        $this->reset('deletingTeacherIds', 'deletingTeacherName', 'permanentDeletion');
        $this->resetSelection();
        Flux::modal('confirm-teacher-deletion')->close();
        Flux::toast(
            variant: 'success',
            text: $isPermanentDeletion
                ? "{$deletedTeacherCount} জন শিক্ষকের তথ্য স্থায়ীভাবে মুছে ফেলা হয়েছে।"
                : "{$deletedTeacherCount} জন শিক্ষকের তথ্য সফলভাবে ট্র্যাশে পাঠানো হয়েছে।",
        );
    }

    public function cancelTeacherDeletion(): void
    {
        $this->reset('deletingTeacherIds', 'deletingTeacherName', 'permanentDeletion');
    }

    public function restoreTeacher(int $teacherId): void
    {
        abort_unless(auth()->user()->can('teachers.delete'), 403);
        $restoredTeacherCount = $this->accessibleTeachersQuery(true)
            ->whereKey($teacherId)
            ->restore();

        if ($restoredTeacherCount === 0) {
            Flux::toast(variant: 'danger', text: 'শিক্ষকের তথ্য পুনরুদ্ধার করা যায়নি।');

            return;
        }

        $this->resetSelection();
        Flux::toast(variant: 'success', text: 'শিক্ষকের তথ্য সফলভাবে পুনরুদ্ধার করা হয়েছে।');
    }

    public function restoreSelectedTeachers(): void
    {
        abort_unless(auth()->user()->can('teachers.delete'), 403);
        $teacherIds = collect($this->selectedTeacherIds)
            ->map(fn ($teacherId): int => (int) $teacherId)
            ->unique()
            ->values();

        $restoredTeacherCount = $this->accessibleTeachersQuery(true)
            ->whereKey($teacherIds)
            ->restore();

        if ($restoredTeacherCount === 0) {
            Flux::toast(variant: 'warning', text: 'পুনরুদ্ধারের জন্য অন্তত একজন শিক্ষক নির্বাচন করুন।');

            return;
        }

        $this->resetSelection();
        Flux::toast(variant: 'success', text: "{$restoredTeacherCount} জন শিক্ষকের তথ্য সফলভাবে পুনরুদ্ধার করা হয়েছে।");
    }

    // এডিট মডাল ওপেন করা এবং ডেটা লোড করার ফাংশন
    public function editTeacher($id)
    {
        abort_unless(auth()->user()->can('teachers.update'), 403);
        $teacher = $this->accessibleTeachersQuery()->with(['trainingTypes.trainingInstitute', 'otherTrainings.trainingInstitute'])->findOrFail($id);
        $this->editingId = $id;

        // ফর্মের ইনপুটে বর্তমান ডেটা সেট করা
        $this->editForm = [
            'college_code' => $teacher->college_code,
            'college_name' => $teacher->college_name,
            'name' => $teacher->display_name,
            'designation' => $teacher->designation,
            'subject' => $teacher->subject,
            'teacher_level' => $teacher->teacher_level,
            'employment_type' => $teacher->employment_type,
            'ict_training_name' => $teacher->ict_training_name,
            'other_training_name' => $teacher->other_training_name,
            'training_institute' => $teacher->training_institute,
            'mobile_number' => $teacher->user?->mobile_no,
            'email' => $teacher->user?->email,
        ];

        $this->trainingEntries = $teacher->trainingTypes->map(fn (TrainingType $trainingType): array => [
            'kind' => 'catalog',
            'training_institute_id' => (string) $trainingType->training_institute_id,
            'institute_name' => '',
            'training_type_id' => (string) $trainingType->id,
            'name' => '',
            'duration_value' => '',
            'duration_unit' => 'days',
            'training_year' => (string) $trainingType->pivot->training_year,
        ])->concat($teacher->otherTrainings->map(fn (TeacherOtherTraining $training): array => [
            'kind' => 'other',
            'training_institute_id' => (string) ($training->training_institute_id ?? ''),
            'institute_name' => (string) ($training->institute_name ?? ''),
            'training_type_id' => '',
            'name' => $training->name,
            'duration_value' => (string) ($training->duration_value ?? ''),
            'duration_unit' => (string) ($training->duration_unit ?? 'days'),
            'training_year' => (string) $training->training_year,
        ]))->values()->all();

        if ($this->trainingEntries === []) {
            $this->addTrainingEntry();
        }

        // ফ্রন্টএন্ডে মডাল ওপেন করার জন্য ইভেন্ট ফায়ার
        $this->dispatch('open-edit-modal');
    }

    // আপডেট সেভ করার ফাংশন
    public function updateTeacher()
    {
        abort_unless(auth()->user()->can('teachers.update'), 403);
        // ভ্যালিডেশন
        try {
            $validated = $this->validate([
                'editForm.college_code' => ['nullable', 'string', 'max:255'],
                'editForm.college_name' => ['nullable', 'string', 'max:255'],
                'editForm.name' => 'required|string|max:255',
                'editForm.designation' => 'nullable|string|max:255',
                'editForm.subject' => 'nullable|string|max:255',
                'editForm.teacher_level' => ['nullable', 'string', 'max:255'],
                'editForm.employment_type' => ['nullable', 'string', 'max:255'],
                'editForm.ict_training_name' => ['nullable', 'string'],
                'editForm.other_training_name' => ['nullable', 'string'],
                'editForm.training_institute' => ['nullable', 'string'],
                'editForm.mobile_number' => ['nullable', 'string', 'max:50', Rule::unique('users', 'mobile_no')->ignore($this->accessibleTeachersQuery()->whereKey($this->editingId)->value('user_id'))],
                'editForm.email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->accessibleTeachersQuery()->whereKey($this->editingId)->value('user_id'))],
                'trainingEntries' => ['array'],
                'trainingEntries.*.kind' => ['required', Rule::in(['catalog', 'other'])],
                'trainingEntries.*.training_institute_id' => ['nullable', Rule::exists('training_institutes', 'id')],
                'trainingEntries.*.institute_name' => ['nullable', 'string', 'max:255'],
                'trainingEntries.*.training_type_id' => ['nullable', Rule::exists('training_types', 'id')],
                'trainingEntries.*.name' => ['nullable', 'string', 'max:255'],
                'trainingEntries.*.duration_value' => ['nullable', 'integer', 'min:1', 'max:999'],
                'trainingEntries.*.duration_unit' => ['nullable', Rule::in(['hours', 'days', 'weeks', 'months'])],
                'trainingEntries.*.training_year' => ['nullable', 'integer', 'min:1950', 'max:'.((int) date('Y') + 1)],
            ], [
                'editForm.name.required' => 'শিক্ষকের নাম অবশ্যই দিতে হবে।',
                'editForm.email.email' => 'সঠিক ইমেইল ঠিকানা লিখুন।',
                'editForm.*.max' => 'এই তথ্যটি অনুমোদিত দৈর্ঘ্যের চেয়ে বড় হয়েছে।',
                'trainingEntries.*.training_year.integer' => 'ট্রেনিং বছর চার সংখ্যার হতে হবে।',
                'trainingEntries.*.training_year.min' => 'ট্রেনিং বছর ১৯৫০ বা তার পরের হতে হবে।',
                'trainingEntries.*.training_year.max' => 'ভবিষ্যতের ট্রেনিং বছর গ্রহণযোগ্য নয়।',
            ]);

            $uniqueTrainingEntries = [];
            foreach ($validated['trainingEntries'] as $index => $entry) {
                $instituteId = $entry['training_institute_id'] ?? null;
                $trainingTypeId = $entry['training_type_id'] ?? null;
                $trainingYear = $entry['training_year'] ?? null;
                $kind = $entry['kind'];
                $hasAnyValue = filled($instituteId) || filled($trainingTypeId) || filled($entry['name'] ?? null) || filled($trainingYear);
                if (! $hasAnyValue) {
                    continue;
                }
                if ($kind === 'catalog') {
                    $trainingTypeBelongsToInstitute = TrainingType::query()
                        ->whereKey($trainingTypeId)->where('training_institute_id', $instituteId)->exists();
                    if (! filled($instituteId) || ! filled($trainingTypeId) || ! filled($trainingYear) || ! $trainingTypeBelongsToInstitute) {
                        $this->addError("trainingEntries.{$index}.training_type_id", 'প্রতিষ্ঠান, ট্রেনিং টাইপ ও বছর সঠিকভাবে নির্বাচন করুন।');
                    }
                } elseif (! filled($entry['name'] ?? null) || ! filled($trainingYear)) {
                    $this->addError("trainingEntries.{$index}.name", 'অন্যান্য ট্রেনিংয়ের নাম ও সম্পন্নের বছর দিতে হবে।');
                }
                $uniqueKey = $kind === 'catalog' ? $trainingTypeId.'-'.$trainingYear : 'other-'.mb_strtolower((string) $entry['name']).'-'.$trainingYear;
                if (isset($uniqueTrainingEntries[$uniqueKey])) {
                    $this->addError("trainingEntries.{$index}.training_type_id", 'একই বছরের একই ট্রেনিং একাধিকবার যোগ করা যাবে না।');
                }
                $uniqueTrainingEntries[$uniqueKey] = true;
            }

            if ($this->getErrorBag()->isNotEmpty()) {
                throw ValidationException::withMessages($this->getErrorBag()->toArray());
            }
        } catch (ValidationException $exception) {
            Flux::toast(variant: 'danger', text: 'তথ্য আপডেট করা যায়নি। চিহ্নিত ঘরগুলো ঠিক করুন।');

            throw $exception;
        }

        // ডেটাবেসে আপডেট করা
        if ($this->editingId) {
            DB::transaction(function () use ($validated): void {
                $teacher = $this->accessibleTeachersQuery()->findOrFail($this->editingId);
                $teacherData = collect($validated['editForm'])->except(['mobile_number', 'email'])->all();
                $teacherData['subject_id'] = Subject::query()->where('name', $teacherData['subject'])->value('id');
                $teacherData['designation_id'] = Designation::query()->where('name', $teacherData['designation'])->value('id');
                $teacherData['teacher_level_id'] = TeacherLevel::query()->where('name', $teacherData['teacher_level'])->value('id');
                $teacherData['employment_id'] = Employment::query()->where('name', $teacherData['employment_type'])->value('id');
                $teacherData['college_id'] = College::query()->where('code', $teacherData['college_code'])
                    ->orWhere('name', $teacherData['college_name'])->value('id');
                $teacher->update($teacherData);

                if ($teacher->user_id !== null) {
                    User::query()->whereKey($teacher->user_id)->update([
                        'email' => $validated['editForm']['email'],
                        'mobile_no' => $validated['editForm']['mobile_number'],
                    ]);
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
                            'name' => $entry['name'],
                            'duration_value' => $entry['duration_value'] ?: null,
                            'duration_unit' => filled($entry['duration_value'] ?? null) ? $entry['duration_unit'] : null,
                            'training_year' => (int) $entry['training_year'],
                        ]);
                    }
                }
            });

            Flux::toast(variant: 'success', text: 'শিক্ষকের তথ্য সফলভাবে আপডেট করা হয়েছে।');

            // মডাল বন্ধ করার ইভেন্ট ফায়ার
            $this->dispatch('close-edit-modal');
        }
    }

    public function render(): View
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();
        $query = $this->filteredTeachersQuery();
        $subjects = $isAdmin
            ? Subject::query()->where('is_active', true)->orderBy('name')->pluck('name')
            : $this->accessibleTeachersQuery()->whereNotNull('subject')->where('subject', '!=', '')->distinct()->orderBy('subject')->pluck('subject');

        return view('livewire.teacher-management', [
            'teachers' => $query->with('user:id,name,email,mobile_no,role')->latest()->paginate(8), // পেজিনেশন লিমিট ৮ রাখা হলো (আপনার দেওয়া কোড অনুযায়ী)
            'isAdmin' => $isAdmin,
            'collegeCount' => $isAdmin ? (clone $query)->whereNotNull('college_id')->distinct()->count('college_id') : null,
            'subjects' => $subjects,
            'collegeCodes' => $isAdmin ? College::query()->where('is_active', true)->whereNotNull('code')->orderBy('code')->pluck('code') : collect(),
            'colleges' => College::query()->where('is_active', true)
                ->when(! $isAdmin, fn (Builder $query): Builder => $query->whereKey($user->college_id))
                ->orderBy('name')->get(['code', 'name']),
            'designations' => Designation::query()->where('is_active', true)->orderBy('name')->pluck('name'),
            'teacherLevels' => TeacherLevel::query()->where('is_active', true)->orderBy('name')->pluck('name'),
            'employments' => Employment::query()->where('is_active', true)->orderBy('name')->pluck('name'),
            'trainingInstitutes' => TrainingInstitute::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'trainingTypes' => TrainingType::query()->where('is_active', true)->orderBy('name')->get(['id', 'training_institute_id', 'name', 'duration_value', 'duration_unit']),
        ]);
    }

    public function addTrainingEntry(): void
    {
        $this->trainingEntries[] = [
            'kind' => 'catalog', 'training_institute_id' => '', 'institute_name' => '',
            'training_type_id' => '', 'name' => '', 'duration_value' => '',
            'duration_unit' => 'days', 'training_year' => '',
        ];
    }

    public function removeTrainingEntry(int $index): void
    {
        unset($this->trainingEntries[$index]);
        $this->trainingEntries = array_values($this->trainingEntries);
    }

    public function updatedTrainingEntries(mixed $value, ?string $key = null): void
    {
        if ($key !== null && preg_match('/^(\d+)\.training_institute_id$/', $key, $matches) === 1) {
            $this->trainingEntries[(int) $matches[1]]['training_type_id'] = '';
        }
        if ($key !== null && preg_match('/^(\d+)\.kind$/', $key, $matches) === 1) {
            $index = (int) $matches[1];
            $kind = $this->trainingEntries[$index]['kind'];
            $this->trainingEntries[$index] = array_merge($this->trainingEntries[$index], [
                'training_type_id' => '', 'name' => '', 'duration_value' => '', 'duration_unit' => 'days',
            ]);
            $this->trainingEntries[$index]['kind'] = $kind;
        }
    }

    private function filteredTeachersQuery(): Builder
    {
        $query = $this->accessibleTeachersQuery($this->showTrashed);

        $searchTerm = trim($this->search);

        if ($searchTerm !== '') {
            $searchPattern = '%'.addcslashes($searchTerm, '%_\\').'%';

            $query->where(function (Builder $query) use ($searchPattern): void {
                $query->where('name', 'like', $searchPattern)
                    ->orWhereHas('user', fn (Builder $userQuery): Builder => $userQuery->where('name', 'like', $searchPattern))
                    ->orWhere('ttis_id', 'like', $searchPattern)
                    ->orWhereHas('user', fn (Builder $userQuery): Builder => $userQuery
                        ->where('email', 'like', $searchPattern)
                        ->orWhere('mobile_no', 'like', $searchPattern))
                    ->orWhere('college_code', 'like', $searchPattern)
                    ->orWhere('college_name', 'like', $searchPattern)
                    ->orWhereHas('college', fn (Builder $collegeQuery): Builder => $collegeQuery
                        ->where('name', 'like', $searchPattern)
                        ->orWhere('code', 'like', $searchPattern));
            });
        }

        // বিষয় অনুযায়ী ফিল্টার
        if ($this->subjectFilter !== '') {
            $query->where('subject', $this->subjectFilter);
        }

        // কলেজ কোড অনুযায়ী ফিল্টার
        if (auth()->user()->isAdmin() && $this->collegeCodeFilter !== '') {
            $query->where('college_code', $this->collegeCodeFilter);
        }

        return $query;
    }

    private function accessibleTeachersQuery(bool $onlyTrashed = false): Builder
    {
        $query = $onlyTrashed ? Teacher::onlyTrashed() : Teacher::query();

        if (auth()->user()->role === Role::Principal) {
            $query->where('college_id', auth()->user()->college_id);
        } elseif (auth()->user()->role === Role::Teacher) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }

    private function resetFiltersAndSelection(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    private function resetSelection(): void
    {
        $this->reset('selectedTeacherIds', 'selectAllOnPage');
        $this->dispatch('teacher-selection-updated', selected: false);
    }
}
