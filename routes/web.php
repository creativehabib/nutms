<?php

use App\Http\Controllers\DashboardController;
use App\Livewire\CollegeDetails;
use App\Livewire\CollegeForm;
use App\Livewire\CollegeLabSummary;
use App\Livewire\CollegeManagement;
use App\Livewire\IctTrainingSummary;
use App\Livewire\ReferenceDataManagement;
use App\Livewire\TeacherManagement;
use App\Livewire\TrainingCatalogManagement;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('/teacher-management', TeacherManagement::class)->name('teachers.manage');
    Route::get('/reference-data/{type}', ReferenceDataManagement::class)
        ->whereIn('type', ['subjects', 'designations', 'teacher-levels', 'employments'])
        ->name('reference-data.manage');
    Route::get('/colleges', CollegeManagement::class)->name('colleges.manage');
    Route::get('/colleges/create', CollegeForm::class)->name('colleges.create');
    Route::get('/colleges/{college}/edit', CollegeForm::class)->name('colleges.edit');
    Route::get('/colleges/{college}', CollegeDetails::class)->name('colleges.show');
    Route::get('/training-catalog', TrainingCatalogManagement::class)->name('training-catalog.manage');
    Route::get('/lab-summary', CollegeLabSummary::class)->name('lab.summary');
    Route::get('/ict-training-summary', IctTrainingSummary::class)->name('ict.summary');
});

require __DIR__.'/settings.php';
