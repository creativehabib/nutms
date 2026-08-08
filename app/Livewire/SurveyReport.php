<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TeacherSurvey;
use App\Models\StudentSurvey;
use Illuminate\Support\Facades\DB;

class SurveyReport extends Component
{
    public $totalTeachers;
    public $totalStudents;

    public $tSatisfactionLabels = [];
    public $tSatisfactionData = [];

    public $sConfidenceLabels = [];
    public $sConfidenceData = [];

    public $sFutureCareerLabels = [];
    public $sFutureCareerData = [];

    public function mount()
    {
        $this->totalTeachers = TeacherSurvey::count();
        $this->totalStudents = StudentSurvey::count();

        // 1. Teacher Satisfaction (Q6)
        $tSatisfaction = TeacherSurvey::select('q6', DB::raw('count(*) as total'))
            ->whereNotNull('q6')
            ->groupBy('q6')
            ->pluck('total', 'q6')->toArray();

        $this->tSatisfactionLabels = array_keys($tSatisfaction);
        $this->tSatisfactionData = array_values($tSatisfaction);

        // 2. Student Confidence (SQ3)
        $sConfidence = StudentSurvey::select('sq3', DB::raw('count(*) as total'))
            ->whereNotNull('sq3')
            ->groupBy('sq3')
            ->pluck('total', 'sq3')->toArray();

        $this->sConfidenceLabels = array_keys($sConfidence);
        $this->sConfidenceData = array_values($sConfidence);

        // 3. Student Future Career (SQ4)
        $sFutureCareer = StudentSurvey::select('sq4', DB::raw('count(*) as total'))
            ->whereNotNull('sq4')
            ->groupBy('sq4')
            ->pluck('total', 'sq4')->toArray();

        $this->sFutureCareerLabels = array_keys($sFutureCareer);
        $this->sFutureCareerData = array_values($sFutureCareer);
    }

    public function render()
    {
        // ভিউ ফাইলের নতুন নাম
        return view('livewire.survey-report')->layout('layouts.app');
    }
}
