<?php

namespace App\Livewire;

use App\Imports\TeachersImport;
use App\Models\Teacher;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class TeacherDataImport extends Component
{
    use WithFileUploads;

    public $file;

    public $message = '';

    public string $messageType = 'success';

    public function import(): void
    {
        $this->validate([
            'file' => ['required', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        try {
            $originalName = $this->file->getClientOriginalName();
            $parts = explode(' - ', $originalName);
            $collegeCode = trim($parts[0]);
            $collegeName = isset($parts[1]) ? trim($parts[1]) : null;

            $collegeAlreadyImported = Teacher::query()
                ->withTrashed()
                ->whereHas('college', fn ($query) => $query
                    ->when($collegeCode !== '', fn ($query) => $query->where('college_code', $collegeCode))
                    ->when($collegeCode === '' && $collegeName, fn ($query) => $query->where('name', $collegeName)))
                ->exists();

            if ($collegeAlreadyImported) {
                $collegeIdentifier = $collegeName
                    ? "{$collegeName} ({$collegeCode})"
                    : $collegeCode;

                $this->messageType = 'warning';
                $this->message = "{$collegeIdentifier} কলেজের শিক্ষক তথ্য ইতোমধ্যে ইম্পোর্ট করা হয়েছে।";
                $this->reset('file');
                Flux::toast(variant: 'warning', text: $this->message);

                return;
            }

            Excel::import(new TeachersImport($collegeName), $this->file->getRealPath());

            $this->messageType = 'success';
            $this->message = 'ডেটা সফলভাবে ইম্পোর্ট এবং প্রসেস করা হয়েছে!';
            $this->reset('file');
            Flux::toast(variant: 'success', text: $this->message);
            $this->dispatch('close-modal');
        } catch (\Exception $e) {
            $this->messageType = 'error';
            $this->message = 'এরর: ' . $e->getMessage();
            Flux::toast(variant: 'danger', text: 'ডেটা ইম্পোর্ট করা যায়নি। ফাইলটি পরীক্ষা করে আবার চেষ্টা করুন।');
        }
    }

    public function render(): View
    {
        return view('livewire.teacher-data-import');
    }
}
