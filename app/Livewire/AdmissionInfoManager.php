<?php

namespace App\Livewire;

use App\Models\AdmissionInfo;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Imports\AdmissionInfoImport;
use Maatwebsite\Excel\Facades\Excel;
use Flux\Flux;

class AdmissionInfoManager extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';

    // ফিল্টার প্রোপার্টি
    public $division = '';
    public $district = '';
    public $category = ''; // নতুন ক্যাটাগরি ফিল্টার

    // File Upload properties
    public $file;
    public $duplicateMessage = '';

    // Edit properties
    public $editingId = null;
    public $college_code, $college_name, $subject_id, $subject_name;
    public $sess_21_22, $sess_22_23, $sess_23_24, $sess_24_25;

    // Delete property
    public $deletingId = null;

    // ফিল্টার চেঞ্জ হলে পেজ ১ এ চলে যাবে
    public function updatingSearch() { $this->resetPage(); }
    public function updatingDivision() { $this->resetPage(); $this->district = ''; }
    public function updatingDistrict() { $this->resetPage(); }
    public function updatingCategory() { $this->resetPage(); } // ক্যাটাগরি পাল্টানো হলেও পেজ রিসেট হবে

    // ==========================================
    // Import Logic
    // ==========================================
    public function importData()
    {
        $this->validate([
            'file' => ['required', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        try {
            $countBefore = AdmissionInfo::count();
            Excel::import(new AdmissionInfoImport, $this->file);
            $countAfter = AdmissionInfo::count();

            if ($countBefore === $countAfter && $countAfter > 0) {
                $this->duplicateMessage = 'এই ফাইলের ডেটা ইতোমধ্যেই ইম্পোর্ট করা হয়েছে। বিদ্যমান তথ্যগুলো আপডেট করা হয়েছে।';
                $this->reset('file');
            } else {
                Flux::toast(variant: 'success', text: __('Admission data imported successfully!'));
                $this->reset(['file', 'duplicateMessage']);
                Flux::modal('import-admission-modal')->close();
            }
        } catch (\Exception $e) {
            Flux::toast(variant: 'danger', text: __('Error importing file: ') . $e->getMessage());
        }
    }

    public function resetImportState()
    {
        $this->reset(['file', 'duplicateMessage']);
    }

    // ==========================================
    // Edit Logic
    // ==========================================
    public function edit($id)
    {
        $record = AdmissionInfo::findOrFail($id);
        $this->editingId = $record->id;
        $this->college_code = $record->college_code;
        $this->college_name = $record->college_name;
        $this->subject_id = $record->subject_id;
        $this->subject_name = $record->subject_name;
        $this->sess_21_22 = $record->sess_21_22_total_admited;
        $this->sess_22_23 = $record->sess_22_23_total_admited;
        $this->sess_23_24 = $record->sess_23_24_total_admited;
        $this->sess_24_25 = $record->sess_24_25_total_admited;

        Flux::modal('edit-admission-modal')->show();
    }

    public function update()
    {
        $this->validate([
            'college_code' => 'required|string',
            'college_name' => 'required|string',
            'subject_id'   => 'required|string',
            'subject_name' => 'required|string',
            'sess_21_22'   => 'required|integer|min:0',
            'sess_22_23'   => 'required|integer|min:0',
            'sess_23_24'   => 'required|integer|min:0',
            'sess_24_25'   => 'required|integer|min:0',
        ]);

        $record = AdmissionInfo::findOrFail($this->editingId);
        $record->update([
            'college_code'             => $this->college_code,
            'college_name'             => $this->college_name,
            'subject_id'               => $this->subject_id,
            'subject_name'             => $this->subject_name,
            'sess_21_22_total_admited' => $this->sess_21_22,
            'sess_22_23_total_admited' => $this->sess_22_23,
            'sess_23_24_total_admited' => $this->sess_23_24,
            'sess_24_25_total_admited' => $this->sess_24_25,
        ]);

        Flux::toast(variant: 'success', text: __('Record updated successfully.'));
        Flux::modal('edit-admission-modal')->close();
        $this->resetEditForm();
    }

    public function cancelEdit()
    {
        $this->resetEditForm();
        Flux::modal('edit-admission-modal')->close();
    }

    private function resetEditForm()
    {
        $this->reset(['editingId', 'college_code', 'college_name', 'subject_id', 'subject_name', 'sess_21_22', 'sess_22_23', 'sess_23_24', 'sess_24_25']);
    }

    // ==========================================
    // Delete Logic
    // ==========================================
    public function confirmDelete($id)
    {
        $this->deletingId = $id;
        Flux::modal('delete-admission-modal')->show();
    }

    public function delete()
    {
        if ($this->deletingId) {
            AdmissionInfo::destroy($this->deletingId);
            Flux::toast(variant: 'success', text: __('Record deleted successfully.'));
            $this->deletingId = null;
            Flux::modal('delete-admission-modal')->close();
        }
    }

    public function render()
    {
        // ড্রপডাউনের জন্য ইউনিক Division, District এবং Category ডেটা আনা
        $divisions = AdmissionInfo::select('division')->whereNotNull('division')->where('division', '!=', '')->distinct()->orderBy('division')->pluck('division');

        $districts = AdmissionInfo::select('district')
            ->whereNotNull('district')->where('district', '!=', '')
            ->when($this->division, function($q) {
                $q->where('division', $this->division);
            })
            ->distinct()->orderBy('district')->pluck('district');

        $categories = AdmissionInfo::select('category')->whereNotNull('category')->where('category', '!=', '')->distinct()->orderBy('category')->pluck('category');

        // ১. সার্চ এবং গ্রুপিং লজিক
        $collegesQuery = AdmissionInfo::query()
            ->select('college_code', 'college_name')
            ->selectRaw('COUNT(id) as total_subjects')
            ->selectRaw('SUM(sess_21_22_total_admited) as sum_21_22')
            ->selectRaw('SUM(sess_22_23_total_admited) as sum_22_23')
            ->selectRaw('SUM(sess_23_24_total_admited) as sum_23_24')
            ->selectRaw('SUM(sess_24_25_total_admited) as sum_24_25')
            ->when($this->division, fn($q) => $q->where('division', $this->division))
            ->when($this->district, fn($q) => $q->where('district', $this->district))
            ->when($this->category, fn($q) => $q->where('category', $this->category)) // ক্যাটাগরি ফিল্টার যুক্ত
            ->groupBy('college_code', 'college_name');

        if ($this->search) {
            $collegesQuery->where(function ($q) {
                $q->where('college_name', 'like', '%' . $this->search . '%')
                    ->orWhere('college_code', 'like', '%' . $this->search . '%')
                    ->orWhere('subject_name', 'like', '%' . $this->search . '%');
            });
        }

        // পেজিনেশন
        $colleges = $collegesQuery->orderBy('college_name')->paginate(10);

        // ২. শুধুমাত্র বর্তমান পেজের কলেজগুলোর সাবজেক্ট ডেটা নিয়ে আসা
        $collegeCodes = $colleges->pluck('college_code');

        $subjectsQuery = AdmissionInfo::whereIn('college_code', $collegeCodes)
            ->when($this->division, fn($q) => $q->where('division', $this->division))
            ->when($this->district, fn($q) => $q->where('district', $this->district))
            ->when($this->category, fn($q) => $q->where('category', $this->category)); // ক্যাটাগরি ফিল্টার যুক্ত

        if ($this->search) {
            $subjectsQuery->where(function ($q) {
                $q->where('college_name', 'like', '%' . $this->search . '%')
                    ->orWhere('college_code', 'like', '%' . $this->search . '%')
                    ->orWhere('subject_name', 'like', '%' . $this->search . '%');
            });
        }

        $subjects = $subjectsQuery->get()->groupBy('college_code');

        return view('livewire.admission-info-manager', compact('colleges', 'subjects', 'divisions', 'districts', 'categories'))->layout('layouts.app',['title'=> 'Admission Info Manager']);
    }
}
