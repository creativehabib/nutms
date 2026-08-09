<?php

namespace App\Livewire;

use App\Models\AdmissionInfo;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Imports\AdmissionInfoImport;
use Maatwebsite\Excel\Facades\Excel;
use Flux\Flux;

class AdmissionSummary extends Component
{
    use WithFileUploads;

    public $selectedCollege = '';
    public $collegeType = '';
    public $file;

    // ডুপ্লিকেট মেসেজ শো করার জন্য নতুন প্রোপার্টি
    public $duplicateMessage = '';

    public function importData()
    {
        $this->validate([
            'file' => ['required', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        try {
            // ইম্পোর্ট করার আগে মোট ডেটার সংখ্যা
            $countBefore = AdmissionInfo::count();

            Excel::import(new AdmissionInfoImport, $this->file);

            // ইম্পোর্ট করার পরে মোট ডেটার সংখ্যা
            $countAfter = AdmissionInfo::count();

            // যদি নতুন কোনো ডেটা ইনসার্ট না হয়ে থাকে (অর্থাৎ ডুপ্লিকেট আপডেট হয়েছে)
            if ($countBefore === $countAfter && $countAfter > 0) {
                $this->duplicateMessage = 'এই ফাইলের ডেটা ইতোমধ্যেই ইম্পোর্ট করা হয়েছে। বিদ্যমান তথ্যগুলো আপডেট করা হয়েছে।';
                $this->reset('file'); // শুধু ফাইল রিসেট হবে, মডাল খোলা থাকবে
            } else {
                // নতুন ডেটা ইম্পোর্ট হলে
                Flux::toast(variant: 'success', text: __('Admission data imported successfully!'));
                $this->reset(['file', 'duplicateMessage']);
                $this->dispatch('modal-close', name: 'import-admission-modal');
            }

        } catch (\Exception $e) {
            Flux::toast(variant: 'danger', text: __('Error importing file: ') . $e->getMessage());
        }
    }

    // মডাল বন্ধ হলে ওয়ার্নিং মেসেজ মুছে ফেলার জন্য
    public function resetImportState()
    {
        $this->reset(['file', 'duplicateMessage']);
    }

    public function render()
    {
        $colleges = AdmissionInfo::select('college_code', 'college_name')
            ->distinct()
            ->orderBy('college_name')
            ->get();

        $totalColleges = $colleges->count();
        $summaryData = [];
        $totalStudents = 0;

        if ($this->selectedCollege) {
            $summaryData = AdmissionInfo::where('college_code', $this->selectedCollege)
                ->select('subject_name', 'sess_24_25_total_admited')
                ->orderBy('subject_name')
                ->get();

            $totalStudents = $summaryData->sum('sess_24_25_total_admited');
        }

        return view('livewire.admission-summary', compact('colleges', 'summaryData', 'totalStudents', 'totalColleges'))->layout('layouts.app',['title'=> 'Admission Summary']);
    }
}
