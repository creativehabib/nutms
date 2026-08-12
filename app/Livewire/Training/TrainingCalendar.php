<?php

namespace App\Livewire\Training;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Training; // আপনার Training মডেল

class TrainingCalendar extends Component
{
    public $currentYear;
    public $currentMonth;

    public function mount()
    {
        $this->currentYear = Carbon::now()->year;
        $this->currentMonth = Carbon::now()->month;
    }

    public function nextMonth()
    {
        $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentYear = $date->year;
        $this->currentMonth = $date->month;
    }

    public function prevMonth()
    {
        $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentYear = $date->year;
        $this->currentMonth = $date->month;
    }

    public function render()
    {
        $currentDate = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1);
        $daysInMonth = $currentDate->daysInMonth;

        // মাসের প্রথম দিনটি সপ্তাহের কোন দিনে পড়েছে (0 = Sunday, 6 = Saturday)
        $firstDayOfWeek = $currentDate->dayOfWeek;

        // ডেটাবেজ থেকে এই মাসের ট্রেনিংগুলো নিয়ে আসা
        // (আপনার টেবিলে start_date নামে কলাম আছে ধরে নেওয়া হলো)
        /*
        $trainings = Training::whereMonth('start_date', $this->currentMonth)
            ->whereYear('start_date', $this->currentYear)
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->start_date)->format('Y-m-d');
            });
        */

        // ডেমো ডেটা (টেস্টিংয়ের জন্য, আপনি উপরের আসল কোয়েরিটি ব্যবহার করবেন)
        $trainings = [
            $this->currentYear . '-' . sprintf('%02d', $this->currentMonth) . '-12' => [
                ['title' => 'Digital Pedagogy', 'type' => 'Online', 'time' => '10:00 AM']
            ],
            $this->currentYear . '-' . sprintf('%02d', $this->currentMonth) . '-24' => [
                ['title' => 'Curriculum Workshop', 'type' => 'Offline', 'time' => '02:00 PM']
            ],
        ];

        // Upcoming Trainings (আজকের পরের যেকোনো ট্রেনিং)
        /*
        $upcomingTrainings = Training::where('start_date', '>=', Carbon::today())
            ->orderBy('start_date', 'asc')
            ->take(4)
            ->get();
        */

        $upcomingTrainings = []; // ডেমো পারপাস

        return view('livewire.training.training-calendar', [
            'currentDate' => $currentDate,
            'daysInMonth' => $daysInMonth,
            'firstDayOfWeek' => $firstDayOfWeek,
            'trainings' => collect($trainings),
            'upcomingTrainings' => $upcomingTrainings
        ])->layout('layouts.app', ['title' => 'Training Calendar']);
    }
}
