<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TeacherSurvey;
use Illuminate\Support\Facades\Cookie;

class TeacherSurveyForm extends Component
{
    public $q1 = [], $q1_other_text;
    public $q2 = [];
    public $q3 = [], $q3_other_text;
    public $q4, $q5, $q6;
    public $q7 = [];
    public $q8 = [], $q8_other_text;

    public $successMessage = '';
    public $hasSubmitted = false; // নতুন ভেরিয়েবল

    public function mount()
    {
        // পেজ লোড হওয়ার সময় চেক করবে কুকি আছে কিনা
        if (Cookie::has('teacher_survey_completed')) {
            $this->hasSubmitted = true;
        }
    }

    public function submit()
    {
        TeacherSurvey::create([
            'q1' => $this->processArray($this->q1, $this->q1_other_text),
            'q2' => $this->q2,
            'q3' => $this->processArray($this->q3, $this->q3_other_text),
            'q4' => $this->q4,
            'q5' => $this->q5,
            'q6' => $this->q6,
            'q7' => $this->q7,
            'q8' => $this->processArray($this->q8, $this->q8_other_text),
        ]);

        // সাবমিট হওয়ার পর ব্রাউজারে ১ বছরের (525600 মিনিট) জন্য কুকি সেট করা হলো
        Cookie::queue('teacher_survey_completed', true, 525600);

        $this->hasSubmitted = true;
        $this->successMessage = 'শিক্ষক মতামত জরিপটি সফলভাবে জমা হয়েছে। ধন্যবাদ!';
    }

    private function processArray($choices, $otherText)
    {
        $result = is_array($choices) ? $choices : [];
        if (($key = array_search('other', $result)) !== false) {
            unset($result[$key]);
            if ($otherText) $result[] = 'অন্যান্য: ' . $otherText;
        }
        return array_values($result);
    }

    public function render()
    {
        return view('livewire.teacher-survey-form')->layout('layouts.frontend');
    }
}
