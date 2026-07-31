<?php

use App\Http\Controllers\DashboardController;
use App\Livewire\CollegeDetails;
use App\Livewire\CollegeForm;
use App\Livewire\CollegeLabSummary;
use App\Livewire\CollegeManagement;
use App\Livewire\IctTrainingSummary;
use App\Livewire\ReferenceDataManagement;
use App\Livewire\RolePermissionManagement;
use App\Livewire\TeacherDetails;
use App\Livewire\TeacherManagement;
use App\Livewire\TeacherProfileForm;
use App\Livewire\TrainingCatalogManagement;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('/teacher-management', TeacherManagement::class)->middleware('role:admin,principal')->name('teachers.manage');
    Route::get('/teachers/create', TeacherProfileForm::class)->name('teachers.create');
    Route::get('/teachers/{teacher}/edit', TeacherProfileForm::class)->name('teachers.edit');
    Route::get('/teachers/{teacher}', TeacherDetails::class)->name('teachers.show');
    Route::get('/reference-data/{type}', ReferenceDataManagement::class)->middleware('role:admin')
        ->whereIn('type', ['subjects', 'designations', 'teacher-levels', 'employments'])
        ->name('reference-data.manage');
    Route::get('/roles-permissions', RolePermissionManagement::class)->middleware('role:admin')->name('roles-permissions.manage');
    Route::get('/colleges', CollegeManagement::class)->middleware('role:admin,principal')->name('colleges.manage');
    Route::get('/colleges/create', CollegeForm::class)->middleware('role:admin')->name('colleges.create');
    Route::get('/colleges/{college}/edit', CollegeForm::class)->middleware('role:admin,principal')->name('colleges.edit');
    Route::get('/colleges/{college}', CollegeDetails::class)->middleware('role:admin,principal')->name('colleges.show');
    Route::get('/training-catalog', TrainingCatalogManagement::class)->middleware('role:admin')->name('training-catalog.manage');
    Route::get('/lab-summary', CollegeLabSummary::class)->middleware('role:admin')->name('lab.summary');
    Route::get('/ict-training-summary', IctTrainingSummary::class)->middleware('role:admin')->name('ict.summary');
});

require __DIR__.'/settings.php';
