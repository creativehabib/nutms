<?php

namespace App\Livewire\Training;

use App\Models\Training;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class MyTrainingCertificates extends Component
{
    use WithPagination;

    public function render(): View
    {
        abort_unless(auth()->user()->hasRole('teacher'), 403);

        return view('livewire.training.my-training-certificates', [
            'trainings' => Training::query()
                ->where('has_certificate', true)
                ->whereHas('participants', fn ($query) => $query
                    ->whereKey(auth()->id())
                    ->where('training_user.status', 'Completed')
                    ->whereNotNull('training_user.certificate_number'))
                ->with(['participants' => fn ($query) => $query
                    ->whereKey(auth()->id())
                    ->where('training_user.status', 'Completed')])
                ->orderByDesc('end_date')
                ->paginate(12),
        ])->layout('layouts.app', ['title' => __('My Training Certificates')]);
    }
}
