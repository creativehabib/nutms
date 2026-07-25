<?php

namespace App\Livewire;

use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
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

    // এডিট করার জন্য নতুন প্রপার্টি
    public $editingId = null;
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
    public function updatedSearch() { $this->resetPage(); }
    public function updatedSubjectFilter() { $this->resetPage(); }
    public function updatedCollegeCodeFilter() { $this->resetPage(); }
    public function updatedLabFilter() { $this->resetPage(); }

    // ডেটা ডিলিট করার ফাংশন
    public function deleteTeacher($id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->delete();
        session()->flash('message', 'ডেটা সফলভাবে ডিলিট করা হয়েছে।');
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

        // ডেটাবেসে আপডেট করা
        if ($this->editingId) {
            $teacher = Teacher::findOrFail($this->editingId);
            $teacher->update($validated['editForm']);

            session()->flash('message', 'শিক্ষকের তথ্য সফলভাবে আপডেট করা হয়েছে!');

            // মডাল বন্ধ করার ইভেন্ট ফায়ার
            $this->dispatch('close-edit-modal');
        }
    }

    public function render(): View
    {
        $query = Teacher::query();

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

        // ড্রপডাউনের জন্য ডেটাবেস থেকে ইউনিক সাবজেক্ট এবং কলেজ কোড বের করা
        $subjects = Teacher::select('subject')->distinct()->whereNotNull('subject')->pluck('subject');
        $collegeCodes = Teacher::select('college_code')->distinct()->whereNotNull('college_code')->pluck('college_code');

        return view('livewire.teacher-management', [
            'teachers' => $query->latest()->paginate(8), // পেজিনেশন লিমিট ৮ রাখা হলো (আপনার দেওয়া কোড অনুযায়ী)
            'subjects' => $subjects,
            'collegeCodes' => $collegeCodes,
        ]);
    }
}
