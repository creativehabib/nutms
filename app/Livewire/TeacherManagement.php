<?php

namespace App\Livewire;

use App\Models\Teacher;
use App\Models\College;
use App\Models\Designation;
use App\Models\Employment;
use App\Models\Subject;
use App\Models\TeacherLevel;
use App\Models\TrainingInstitute;
use App\Models\TrainingType;
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
    public $search = '';
    public $subjectFilter = '';
    public $collegeCodeFilter = '';
    public $labFilter = '';

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
        'tmis_id' => '',
        'ttis_id' => '',
        'name' => '',
        'designation' => '',
        'subject' => '',
        'teacher_level' => '',
        'employment_type' => '',
        'has_training' => '',
        'ict_training_name' => '',
        'ict_training_duration' => '',
        'other_training_name' => '',
        'other_training_duration' => '',
        'training_institute' => '',
        'training_year' => '',
        'has_computer_lab' => '',
        'computer_count' => null,
        'mobile_number' => '',
        'email' => '',
    ];

    /** @var array<int, array{training_institute_id: string, training_type_id: string, training_year: string}> */
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
        $this->resetFiltersAndSelection();
    }

    public function updatedLabFilter(): void
    {
        $this->resetFiltersAndSelection();
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

    public function confirmTeacherDeletion(int $teacherId): void
    {
        $teacher = Teacher::findOrFail($teacherId);

        $this->deletingTeacherIds = [$teacher->id];
        $this->deletingTeacherName = $teacher->name ?? 'এই শিক্ষক';
        $this->permanentDeletion = false;

        Flux::modal('confirm-teacher-deletion')->show();
    }

    public function confirmPermanentTeacherDeletion(int $teacherId): void
    {
        $teacher = Teacher::onlyTrashed()->findOrFail($teacherId);

        $this->deletingTeacherIds = [$teacher->id];
        $this->deletingTeacherName = $teacher->name ?? 'এই শিক্ষক';
        $this->permanentDeletion = true;

        Flux::modal('confirm-teacher-deletion')->show();
    }

    public function confirmBulkTeacherDeletion(): void
    {
        $teacherIds = collect($this->selectedTeacherIds)
            ->map(fn ($teacherId): int => (int) $teacherId)
            ->unique()
            ->values();

        $teachers = Teacher::query()->whereKey($teacherIds)->get(['id', 'name']);

        if ($teachers->isEmpty()) {
            Flux::toast(variant: 'warning', text: 'মুছে ফেলার জন্য অন্তত একজন শিক্ষক নির্বাচন করুন।');

            return;
        }

        $this->deletingTeacherIds = $teachers->pluck('id')->all();
        $this->deletingTeacherName = $teachers->count() === 1
            ? ($teachers->first()->name ?? 'এই শিক্ষক')
            : "নির্বাচিত {$teachers->count()} জন শিক্ষক";
        $this->permanentDeletion = false;

        Flux::modal('confirm-teacher-deletion')->show();
    }

    public function confirmBulkPermanentDeletion(): void
    {
        $teacherIds = collect($this->selectedTeacherIds)
            ->map(fn ($teacherId): int => (int) $teacherId)
            ->unique()
            ->values();

        $teachers = Teacher::onlyTrashed()->whereKey($teacherIds)->get(['id', 'name']);

        if ($teachers->isEmpty()) {
            Flux::toast(variant: 'warning', text: 'স্থায়ীভাবে মুছে ফেলার জন্য অন্তত একজন শিক্ষক নির্বাচন করুন।');

            return;
        }

        $this->deletingTeacherIds = $teachers->pluck('id')->all();
        $this->deletingTeacherName = $teachers->count() === 1
            ? ($teachers->first()->name ?? 'এই শিক্ষক')
            : "নির্বাচিত {$teachers->count()} জন শিক্ষক";
        $this->permanentDeletion = true;

        Flux::modal('confirm-teacher-deletion')->show();
    }

    public function deleteTeacher(): void
    {
        if ($this->deletingTeacherIds === []) {
            Flux::toast(variant: 'danger', text: 'মুছে ফেলার জন্য কোনো শিক্ষক নির্বাচন করা হয়নি।');

            return;
        }

        $isPermanentDeletion = $this->permanentDeletion;
        $deletedTeacherCount = $isPermanentDeletion
            ? Teacher::onlyTrashed()->whereKey($this->deletingTeacherIds)->forceDelete()
            : Teacher::query()->whereKey($this->deletingTeacherIds)->delete();

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
        $restoredTeacherCount = Teacher::onlyTrashed()
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
        $teacherIds = collect($this->selectedTeacherIds)
            ->map(fn ($teacherId): int => (int) $teacherId)
            ->unique()
            ->values();

        $restoredTeacherCount = Teacher::onlyTrashed()
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
        $teacher = Teacher::query()->with('trainingTypes.trainingInstitute')->findOrFail($id);
        $this->editingId = $id;

        // ফর্মের ইনপুটে বর্তমান ডেটা সেট করা
        $this->editForm = [
            'college_code' => $teacher->college_code,
            'college_name' => $teacher->college_name,
            'tmis_id' => $teacher->tmis_id,
            'ttis_id' => $teacher->ttis_id,
            'name' => $teacher->name,
            'designation' => $teacher->designation,
            'subject' => $teacher->subject,
            'teacher_level' => $teacher->teacher_level,
            'employment_type' => $teacher->employment_type,
            'has_training' => $teacher->has_training,
            'ict_training_name' => $teacher->ict_training_name,
            'ict_training_duration' => $teacher->ict_training_duration,
            'other_training_name' => $teacher->other_training_name,
            'other_training_duration' => $teacher->other_training_duration,
            'training_institute' => $teacher->training_institute,
            'training_year' => $teacher->training_year,
            'has_computer_lab' => $teacher->has_computer_lab,
            'computer_count' => $teacher->computer_count,
            'mobile_number' => $teacher->mobile_number,
            'email' => $teacher->email,
        ];

        $this->trainingEntries = $teacher->trainingTypes->map(fn (TrainingType $trainingType): array => [
            'training_institute_id' => (string) $trainingType->training_institute_id,
            'training_type_id' => (string) $trainingType->id,
            'training_year' => (string) $trainingType->pivot->training_year,
        ])->values()->all();

        if ($this->trainingEntries === []) {
            $this->addTrainingEntry();
        }

        // ফ্রন্টএন্ডে মডাল ওপেন করার জন্য ইভেন্ট ফায়ার
        $this->dispatch('open-edit-modal');
    }

    // আপডেট সেভ করার ফাংশন
    public function updateTeacher()
    {
        // ভ্যালিডেশন
        try {
            $validated = $this->validate([
                'editForm.college_code' => ['nullable', 'string', 'max:255'],
                'editForm.college_name' => ['nullable', 'string', 'max:255'],
                'editForm.tmis_id' => ['nullable', 'string', 'max:255', Rule::unique('teachers', 'tmis_id')->ignore($this->editingId)],
                'editForm.ttis_id' => ['nullable', 'string', 'max:255'],
                'editForm.name' => 'required|string|max:255',
                'editForm.designation' => 'nullable|string|max:255',
                'editForm.subject' => 'nullable|string|max:255',
                'editForm.teacher_level' => ['nullable', 'string', 'max:255'],
                'editForm.employment_type' => ['nullable', 'string', 'max:255'],
                'editForm.has_training' => ['nullable', 'string', 'max:255'],
                'editForm.ict_training_name' => ['nullable', 'string'],
                'editForm.ict_training_duration' => ['nullable', 'string'],
                'editForm.other_training_name' => ['nullable', 'string'],
                'editForm.other_training_duration' => ['nullable', 'string'],
                'editForm.training_institute' => ['nullable', 'string'],
                'editForm.training_year' => ['nullable', 'string', 'max:255'],
                'editForm.has_computer_lab' => ['nullable', Rule::in(['Yes', 'No'])],
                'editForm.computer_count' => ['nullable', 'integer', 'min:0'],
                'editForm.mobile_number' => 'nullable|string|max:50',
                'editForm.email' => 'nullable|email|max:255',
                'trainingEntries' => ['array'],
                'trainingEntries.*.training_institute_id' => ['nullable', Rule::exists('training_institutes', 'id')],
                'trainingEntries.*.training_type_id' => ['nullable', Rule::exists('training_types', 'id')],
                'trainingEntries.*.training_year' => ['nullable', 'integer', 'min:1950', 'max:'.((int) date('Y') + 1)],
            ], [
                'editForm.name.required' => 'শিক্ষকের নাম অবশ্যই দিতে হবে।',
                'editForm.tmis_id.unique' => 'এই TMIS ID ইতোমধ্যে অন্য একজন শিক্ষকের জন্য ব্যবহার করা হয়েছে।',
                'editForm.has_computer_lab.in' => 'কম্পিউটার ল্যাবের সঠিক অবস্থা নির্বাচন করুন।',
                'editForm.computer_count.integer' => 'কম্পিউটার সংখ্যা অবশ্যই পূর্ণসংখ্যা হতে হবে।',
                'editForm.computer_count.min' => 'কম্পিউটার সংখ্যা শূন্যের কম হতে পারবে না।',
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
                $hasAnyValue = filled($instituteId) || filled($trainingTypeId) || filled($trainingYear);
                if (! $hasAnyValue) {
                    continue;
                }
                $trainingTypeBelongsToInstitute = TrainingType::query()
                    ->whereKey($trainingTypeId)->where('training_institute_id', $instituteId)->exists();
                if (! filled($instituteId) || ! filled($trainingTypeId) || ! filled($trainingYear) || ! $trainingTypeBelongsToInstitute) {
                    $this->addError("trainingEntries.{$index}.training_type_id", 'প্রতিষ্ঠান, ট্রেনিং টাইপ ও বছর সঠিকভাবে নির্বাচন করুন।');
                }
                $uniqueKey = $trainingTypeId.'-'.$trainingYear;
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
                $teacher = Teacher::findOrFail($this->editingId);
                $teacherData = $validated['editForm'];
                $teacherData['subject_id'] = Subject::query()->where('name', $teacherData['subject'])->value('id');
                $teacherData['designation_id'] = Designation::query()->where('name', $teacherData['designation'])->value('id');
                $teacherData['teacher_level_id'] = TeacherLevel::query()->where('name', $teacherData['teacher_level'])->value('id');
                $teacherData['employment_id'] = Employment::query()->where('name', $teacherData['employment_type'])->value('id');
                $teacherData['college_id'] = College::query()->where('code', $teacherData['college_code'])
                    ->orWhere('name', $teacherData['college_name'])->value('id');
                $teacher->update($teacherData);

                $teacher->trainingTypes()->detach();
                foreach ($validated['trainingEntries'] as $entry) {
                    if (filled($entry['training_type_id'] ?? null) && filled($entry['training_year'] ?? null)) {
                        $teacher->trainingTypes()->attach((int) $entry['training_type_id'], ['training_year' => (int) $entry['training_year']]);
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
        $query = $this->filteredTeachersQuery();
        $collegeCount = (clone $query)
            ->whereNotNull('college_code')
            ->where('college_code', '!=', '')
            ->distinct()
            ->count('college_code');

        // ড্রপডাউনের জন্য ডেটাবেস থেকে ইউনিক সাবজেক্ট এবং কলেজ কোড বের করা
        $subjects = Subject::query()->where('is_active', true)->orderBy('name')->pluck('name');
        $collegeCodes = College::query()->where('is_active', true)->whereNotNull('code')->orderBy('code')->pluck('code');

        return view('livewire.teacher-management', [
            'teachers' => $query->latest()->paginate(8), // পেজিনেশন লিমিট ৮ রাখা হলো (আপনার দেওয়া কোড অনুযায়ী)
            'collegeCount' => $collegeCount,
            'subjects' => $subjects,
            'collegeCodes' => $collegeCodes,
            'colleges' => College::query()->where('is_active', true)->orderBy('name')->get(['code', 'name']),
            'designations' => Designation::query()->where('is_active', true)->orderBy('name')->pluck('name'),
            'teacherLevels' => TeacherLevel::query()->where('is_active', true)->orderBy('name')->pluck('name'),
            'employments' => Employment::query()->where('is_active', true)->orderBy('name')->pluck('name'),
            'trainingInstitutes' => TrainingInstitute::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'trainingTypes' => TrainingType::query()->where('is_active', true)->orderBy('name')->get(['id', 'training_institute_id', 'name', 'duration_value', 'duration_unit']),
        ]);
    }

    public function addTrainingEntry(): void
    {
        $this->trainingEntries[] = ['training_institute_id' => '', 'training_type_id' => '', 'training_year' => ''];
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
    }

    private function filteredTeachersQuery(): Builder
    {
        $query = $this->showTrashed
            ? Teacher::onlyTrashed()
            : Teacher::query();

        // সার্চ (নাম, TMIS ID অথবা মোবাইল নাম্বার)
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('tmis_id', 'like', '%' . $this->search . '%')
                    ->orWhere('mobile_number', 'like', '%' . $this->search . '%');
            });
        }

        // বিষয় অনুযায়ী ফিল্টার
        if (!empty($this->subjectFilter)) {
            $query->where('subject', $this->subjectFilter);
        }

        // কলেজ কোড অনুযায়ী ফিল্টার
        if (!empty($this->collegeCodeFilter)) {
            $query->where('college_code', $this->collegeCodeFilter);
        }

        // ল্যাব আছে কি নেই অনুযায়ী ফিল্টার
        if (!empty($this->labFilter)) {
            $query->where('has_computer_lab', $this->labFilter);
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
