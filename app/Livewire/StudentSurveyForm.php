<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\StudentSurvey;
use Illuminate\Support\Facades\Cookie;

class StudentSurveyForm extends Component
{
    public $sq1 = [], $sq1_other_text;
    public $sq2 = [], $sq2_other_text;
    public $sq3, $sq4;
    public $sq5 = [], $sq5_other_text;
    public $sq6 = [], $sq6_other_text;
    public $sq7 = [];

    public $successMessage = '';
    public $hasSubmitted = false; // নতুন ভেরিয়েবল

    public function mount()
    {
        if (Cookie::has('student_survey_completed')) {
            $this->hasSubmitted = true;
        }
    }

    public function submit()
    {
        StudentSurvey::create([
            'sq1' => $this->processArray($this->sq1, $this->sq1_other_text),
            'sq2' => $this->processArray($this->sq2, $this->sq2_other_text),
            'sq3' => $this->sq3,
            'sq4' => $this->sq4,
            'sq5' => $this->processArray($this->sq5, $this->sq5_other_text),
            'sq6' => $this->processArray($this->sq6, $this->sq6_other_text),
            'sq7' => $this->sq7,
        ]);

        // শিক্ষার্থীর জন্য আলাদা কুকি
        Cookie::queue('student_survey_completed', true, 525600);

        $this->hasSubmitted = true;
        $this->successMessage = 'শিক্ষার্থী মতামত জরিপটি সফলভাবে জমা হয়েছে। ধন্যবাদ!';
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
        return view('livewire.student-survey-form')->layout('layouts.app');
    }
}
