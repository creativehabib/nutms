<?php

namespace App\Livewire;

use App\Models\College;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class CollegeManagement extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $college = College::query()->withCount('teachers')->findOrFail($id);
        if ($college->teachers_count > 0) {
            Flux::toast(variant: 'warning', text: 'শিক্ষকের সাথে যুক্ত থাকায় কলেজটি মুছতে পারবেন না। নিষ্ক্রিয় করুন।');

            return;
        }

        $college->delete();
        Flux::toast(variant: 'success', text: 'কলেজটি মুছে ফেলা হয়েছে।');
    }

    public function render(): View
    {
        return view('livewire.college-management', [
            'colleges' => College::query()->with(['district:id,name', 'thana:id,name'])->withCount('teachers')
                ->when($this->search !== '', fn ($query) => $query->where(fn ($query) => $query->where('name', 'like', "%{$this->search}%")->orWhere('code', 'like', "%{$this->search}%")))
                ->orderBy('name')->paginate(10),
        ]);
    }
}
