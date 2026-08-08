<?php

namespace App\Livewire;

use App\Models\SystemSetting;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class SystemSettings extends Component
{
    public int $retirementAge = 59;

    public function mount(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $this->retirementAge = SystemSetting::retirementAge();
    }

    public function save(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $validated = $this->validate(['retirementAge' => ['required', 'integer', 'min:50', 'max:70']]);

        SystemSetting::query()->updateOrCreate(
            ['key' => SystemSetting::RETIREMENT_AGE],
            ['value' => (string) $validated['retirementAge']],
        );

        Flux::toast(variant: 'success', text: 'শিক্ষকদের অবসর বয়স সংরক্ষণ করা হয়েছে।');
    }

    public function render(): View
    {
        return view('livewire.system-settings')->layout('layouts.app', ['title' => 'System Settings']);
    }
}
