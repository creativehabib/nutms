<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Teacher;
use App\Models\Training;
use App\Services\NationalUniversityNoticeService;
use Illuminate\Contracts\View\View;

class WelcomeController extends Controller
{
    public function __construct(public NationalUniversityNoticeService $noticeService) {}

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
                'trainings' => Training::query()->published()->count(),
                'colleges' => College::query()->where('is_active', true)->count(),
                'registrations' => Training::query()
                    ->published()
                    ->join('training_user', 'trainings.id', '=', 'training_user.training_id')
                    ->count('training_user.id'),
            ],
            'upcomingTraining' => $upcomingTraining,
            'latestTrainings' => Training::query()
                ->published()
                ->latest()
                ->limit(3)
                ->get(),
            'latestNotices' => $this->noticeService->latest(),
            'affiliatedColleges' => College::query()
                ->publiclyVisible()
                ->with(['division:id,name,bn_name', 'district:id,name,bn_name', 'programs:id,college_id,level,name,items'])
                ->orderBy('name')
                ->limit(6)
                ->get(),
        ]);
    }
}
