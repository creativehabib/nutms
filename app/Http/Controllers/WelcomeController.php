<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Teacher;
use App\Models\Training;
use Illuminate\Contracts\View\View;

class WelcomeController extends Controller
{
    public function __invoke(): View
    {
        $upcomingTraining = Training::query()
            ->whereIn('status', ['Upcoming', 'Ongoing'])
            ->where('end_date', '>=', now())
            ->withCount('participants')
            ->orderBy('start_date')
            ->first();

        return view('welcome', [
            'statistics' => [
                'teachers' => Teacher::query()->count(),
                'trainings' => Training::query()->where('status', '!=', 'Draft')->count(),
                'colleges' => College::query()->where('is_active', true)->count(),
                'registrations' => Training::query()->withCount('participants')->get()->sum('participants_count'),
            ],
            'upcomingTraining' => $upcomingTraining,
            'latestTrainings' => Training::query()
                ->where('status', '!=', 'Draft')
                ->latest()
                ->limit(3)
                ->get(),
        ]);
    }
}
