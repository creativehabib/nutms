<?php

use App\Livewire\CollegeLabSummary;
use App\Livewire\IctTrainingSummary;
use App\Livewire\TeacherManagement;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('/teacher-management', TeacherManagement::class)->name('teachers.manage');
    Route::get('/lab-summary', CollegeLabSummary::class)->name('lab.summary');
    Route::get('/ict-training-summary', IctTrainingSummary::class)->name('ict.summary');
});

require __DIR__.'/settings.php';
