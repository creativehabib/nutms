<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NationalUniversityNoticeController;
use App\Http\Controllers\PublicCollegeController;
use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\TrainingCertificateController;
use App\Http\Controllers\WelcomeController;
use App\Livewire\Admin\LanguageManager;
use App\Livewire\AdmissionInfoManager;
use App\Livewire\AdmissionSummary;
use App\Livewire\CollegeDetails;
use App\Livewire\CollegeForm;
use App\Livewire\CollegeLabSummary;
use App\Livewire\CollegeManagement;
use App\Livewire\DocumentEditor\DocumentEditor;
use App\Livewire\Frontend\AffiliatedCollegeDetails;
use App\Livewire\Frontend\AffiliatedCollegeDirectory;
use App\Livewire\IctTrainingSummary;
use App\Livewire\ReferenceDataManagement;
use App\Livewire\RolePermissionManagement;
use App\Livewire\StudentSurveyForm;
use App\Livewire\SurveyReport;
use App\Livewire\System\CacheManager;
use App\Livewire\SystemSettings;
use App\Livewire\TeacherDetails;
use App\Livewire\TeacherManagement;
use App\Livewire\TeacherProfileForm;
use App\Livewire\TeacherSurveyForm;
use App\Livewire\Training\MyTrainingCertificates;
use App\Livewire\Training\TrainingCalendar;
use App\Livewire\Training\TrainingManagement;
use App\Livewire\Training\TrainingRegistrationManagement;
use App\Livewire\TrainingCatalogManagement;
use App\Models\AdmissionInfo;
use Illuminate\Support\Facades\Route;

Route::get('/', WelcomeController::class)->name('home');
Route::get('/notices', NationalUniversityNoticeController::class)->name('notices.index');
Route::view('/tools', 'tools.index')->name('tools.index');
Route::view('/tools/image-signature-compressor', 'tools.image-signature-compressor')->name('tools.image-signature-compressor');
Route::view('/tools/age-retirement-calculator', 'tools.age-retirement-calculator')->name('tools.age-retirement-calculator');
Route::view('/tools/cgpa-sgpa-calculator', 'tools.cgpa-sgpa-calculator')->name('tools.cgpa-sgpa-calculator');
Route::get('/tools/document-editor', DocumentEditor::class)->name('tools.document-editor');
Route::view('/unicode-to-bijoy-converter', 'unicode-to-bijoy-converter')->name('unicode-to-bijoy-converter');
Route::get('/affiliated-colleges', AffiliatedCollegeDirectory::class)->name('public.colleges.index');
Route::get('/affiliated-colleges/{college}/{slug}', AffiliatedCollegeDetails::class)->name('public.colleges.show');
Route::get('/affiliated-colleges/{college}', fn (\App\Models\College $college) => redirect()->to($college->publicProfileUrl(), 301))
    ->name('public.colleges.legacy-show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('/admin/language-settings', LanguageManager::class)->name('admin.language_settings');
    Route::get('/teacher-management', TeacherManagement::class)->middleware(['role:admin,principal', 'permission:teachers.view'])->name('teachers.manage');
    Route::get('/teachers/create', TeacherProfileForm::class)->middleware('permission:teachers.create')->name('teachers.create');
    Route::get('/teachers/{teacher}/resubmit', TeacherProfileForm::class)->middleware(['role:teacher', 'permission:teachers.create'])->name('teachers.resubmit');
    Route::get('/teachers/{teacher}/edit', TeacherProfileForm::class)->middleware('permission:teachers.update')->name('teachers.edit');
    Route::get('/teachers/{teacher}', TeacherDetails::class)->middleware('permission:teachers.view')->name('teachers.show');
    Route::get('/reference-data/{type}', ReferenceDataManagement::class)->middleware(['role:admin', 'permission:reference-data.manage'])
        ->whereIn('type', ['subjects', 'courses', 'program-levels', 'designations', 'teacher-levels', 'employments'])
        ->name('reference-data.manage');
    Route::get('/roles-permissions', RolePermissionManagement::class)->middleware(['role:admin', 'permission:roles.manage'])->name('roles-permissions.manage');
    Route::get('/system-settings', SystemSettings::class)->middleware(['role:admin', 'permission:roles.manage'])->name('system-settings.manage');
    Route::get('/colleges', CollegeManagement::class)->middleware(['role:admin', 'permission:colleges.view'])->name('colleges.manage');
    Route::get('/colleges/create', CollegeForm::class)->middleware(['role:admin', 'permission:colleges.create'])->name('colleges.create');
    Route::get('/colleges/{college}/edit', CollegeForm::class)->middleware(['role:admin,principal', 'permission:colleges.update'])->name('colleges.edit');
    Route::get('/colleges/{college}', CollegeDetails::class)->middleware(['role:admin,principal', 'permission:colleges.view'])->name('colleges.show');
    Route::get('/training-catalog', TrainingCatalogManagement::class)->middleware(['role:admin', 'permission:training-catalog.manage'])->name('training-catalog.manage');
    Route::get('/training-calendar', TrainingCalendar::class)->name('training.calendar');
    Route::get('/training-management', TrainingManagement::class)->middleware('permission:training-catalog.manage')->name('training.manage');
    Route::get('/training-registrations', TrainingRegistrationManagement::class)->middleware('permission:training-catalog.manage')->name('training.registrations');
    Route::get('/my-training-certificates', MyTrainingCertificates::class)->middleware('role:teacher')->name('training.certificates');
    Route::get('/trainings/{training}/certificate', TrainingCertificateController::class)->name('trainings.certificate');
    Route::get('/lab-summary', CollegeLabSummary::class)->middleware(['role:admin', 'permission:reports.view'])->name('lab.summary');
    Route::get('/ict-training-summary', IctTrainingSummary::class)->middleware(['role:admin', 'permission:reports.view'])->name('ict.summary');
    Route::get('admin/survey/report', SurveyReport::class)->name('admin.survey.report');

    Route::get('/system/cache-manager', CacheManager::class)->name('cache.manager');
});

// Survey Route
Route::get('/survey/teacher', TeacherSurveyForm::class)->name('survey.teacher');
Route::get('/survey/student', StudentSurveyForm::class)->name('survey.student');
Route::get('/survey/report/print', [ReportExportController::class, 'printReport'])->name('survey.report.print');

Route::get('/admission-manage', AdmissionInfoManager::class)->name('admission.manage');
Route::get('/admission/summary', AdmissionSummary::class)->name('admission.summary');
// প্রিন্ট করার রাউট
Route::get('/admission/print/{college_code}', function ($college_code) {
    $records = AdmissionInfo::where('college_code', $college_code)
        ->orderBy('subject_name')
        ->get();

    if ($records->isEmpty()) {
        abort(404, 'No data found for this college.');
    }

    $collegeInfo = $records->first();
    $totalStudents = $records->sum('sess_24_25_total_admited');

    return view('pages.admission-print', compact('records', 'collegeInfo', 'totalStudents'));
})->name('admission.print');

require __DIR__.'/settings.php';
