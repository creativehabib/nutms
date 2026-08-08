<?php

namespace App\Http\Controllers;

use App\Models\TeacherSurvey;
use App\Models\StudentSurvey;
use Illuminate\Http\Request;

class ReportExportController extends Controller
{
    public function printReport()
    {
        $totalTeachers = TeacherSurvey::count();
        $totalStudents = StudentSurvey::count();

        // আজকের তারিখ
        $date = now()->format('d F, Y');

        return view('survey-print-report', compact('totalTeachers', 'totalStudents', 'date'));
    }
}
