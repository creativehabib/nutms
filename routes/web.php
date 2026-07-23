<?php

use App\Livewire\TeacherManagement;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('/teacher-management', TeacherManagement::class)->name('teachers.manage');
});

require __DIR__.'/settings.php';
