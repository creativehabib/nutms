<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TeachersImport;

class TeacherDataImport extends Component
{
    use WithFileUploads;

    public $file;
    public $message = '';

    public function import()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            // ফাইলের অরিজিনাল নাম নেওয়া
            // উদাহরণ: "126 - FAKIRHAT GOVT. COLLEGE - 30.xlsx - Sheet1.csv"
            $originalName = $this->file->getClientOriginalName();

            // ' - ' (স্পেস-হাইফেন-স্পেস) দিয়ে নামটিকে ভাগ করা
            $parts = explode(' - ', $originalName);

            $collegeName = null;
            // যদি ২য় অংশটি থাকে, তবে সেটিই কলেজের নাম
            if (isset($parts[1])) {
                $collegeName = trim($parts[1]);
            }

            // TeachersImport ক্লাসে কলেজের নামটি পাস করা
            Excel::import(new TeachersImport($collegeName), $this->file->getRealPath());

            $this->message = 'ডেটা সফলভাবে ইম্পোর্ট এবং প্রসেস করা হয়েছে!';
            $this->reset('file');

            // আপলোড শেষ হলে মডাল বন্ধ করার জন্য ইভেন্ট ফায়ার
            $this->dispatch('close-modal');

        } catch (\Exception $e) {
            $this->message = 'এরর: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.teacher-data-import');
    }
}
