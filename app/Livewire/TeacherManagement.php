<?php

namespace App\Livewire;

use App\Models\Teacher;
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
        'name' => '',
        'designation' => '',
        'subject' => '',
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
            'name' => $teacher->name,
            'designation' => $teacher->designation,
            'subject' => $teacher->subject,
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
        $this->validate([
            'editForm.name' => 'required|string|max:255',
            'editForm.designation' => 'nullable|string|max:255',
            'editForm.subject' => 'nullable|string|max:255',
            'editForm.mobile_number' => 'nullable|string|max:50',
            'editForm.email' => 'nullable|email|max:255',
        ]);

        // ডেটাবেসে আপডেট করা
        if ($this->editingId) {
            $teacher = Teacher::findOrFail($this->editingId);
            $teacher->update([
                'name' => $this->editForm['name'],
                'designation' => $this->editForm['designation'],
                'subject' => $this->editForm['subject'],
                'mobile_number' => $this->editForm['mobile_number'],
                'email' => $this->editForm['email'],
            ]);

            session()->flash('message', 'শিক্ষকের তথ্য সফলভাবে আপডেট করা হয়েছে!');

            // মডাল বন্ধ করার ইভেন্ট ফায়ার
            $this->dispatch('close-edit-modal');
        }
    }

    public function render()
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
