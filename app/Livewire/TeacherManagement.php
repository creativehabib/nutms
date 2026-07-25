<?php

namespace App\Livewire;

use App\Models\Teacher;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
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

            return;
        }

        $this->selectedTeacherIds = $this->filteredTeachersQuery()
            ->latest()
            ->forPage($this->getPage(), 8)
            ->pluck('id')
            ->map(fn (int $teacherId): string => (string) $teacherId)
            ->all();
    }

    public function updatedSelectedTeacherIds(): void
    {
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

        Flux::modal('confirm-teacher-deletion')->show();
    }

    public function deleteTeacher(): void
    {
        if ($this->deletingTeacherIds === []) {
            Flux::toast(variant: 'danger', text: 'মুছে ফেলার জন্য কোনো শিক্ষক নির্বাচন করা হয়নি।');

            return;
        }

        $deletedTeacherCount = Teacher::query()
            ->whereKey($this->deletingTeacherIds)
            ->delete();

        $this->reset('deletingTeacherIds', 'deletingTeacherName', 'selectedTeacherIds', 'selectAllOnPage');
        Flux::modal('confirm-teacher-deletion')->close();
        Flux::toast(
            variant: 'success',
            text: "{$deletedTeacherCount} জন শিক্ষকের তথ্য সফলভাবে মুছে ফেলা হয়েছে।",
        );
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

        $this->reset('selectedTeacherIds', 'selectAllOnPage');
        Flux::toast(variant: 'success', text: "{$restoredTeacherCount} জন শিক্ষকের তথ্য সফলভাবে পুনরুদ্ধার করা হয়েছে।");
    }

    // এডিট মডাল ওপেন করা এবং ডেটা লোড করার ফাংশন
    public function editTeacher($id)
    {
        $teacher = Teacher::findOrFail($id);
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
            ], [
                'editForm.name.required' => 'শিক্ষকের নাম অবশ্যই দিতে হবে।',
                'editForm.tmis_id.unique' => 'এই TMIS ID ইতোমধ্যে অন্য একজন শিক্ষকের জন্য ব্যবহার করা হয়েছে।',
                'editForm.has_computer_lab.in' => 'কম্পিউটার ল্যাবের সঠিক অবস্থা নির্বাচন করুন।',
                'editForm.computer_count.integer' => 'কম্পিউটার সংখ্যা অবশ্যই পূর্ণসংখ্যা হতে হবে।',
                'editForm.computer_count.min' => 'কম্পিউটার সংখ্যা শূন্যের কম হতে পারবে না।',
                'editForm.email.email' => 'সঠিক ইমেইল ঠিকানা লিখুন।',
                'editForm.*.max' => 'এই তথ্যটি অনুমোদিত দৈর্ঘ্যের চেয়ে বড় হয়েছে।',
            ]);
        } catch (ValidationException $exception) {
            Flux::toast(variant: 'danger', text: 'তথ্য আপডেট করা যায়নি। চিহ্নিত ঘরগুলো ঠিক করুন।');

            throw $exception;
        }

        // ডেটাবেসে আপডেট করা
        if ($this->editingId) {
            $teacher = Teacher::findOrFail($this->editingId);
            $teacher->update($validated['editForm']);

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
        $subjects = Teacher::select('subject')->distinct()->whereNotNull('subject')->pluck('subject');
        $collegeCodes = Teacher::select('college_code')->distinct()->whereNotNull('college_code')->pluck('college_code');

        return view('livewire.teacher-management', [
            'teachers' => $query->latest()->paginate(8), // পেজিনেশন লিমিট ৮ রাখা হলো (আপনার দেওয়া কোড অনুযায়ী)
            'collegeCount' => $collegeCount,
            'subjects' => $subjects,
            'collegeCodes' => $collegeCodes,
        ]);
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
        $this->reset('selectedTeacherIds', 'selectAllOnPage');
    }
}
