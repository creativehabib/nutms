<?php

namespace App\Livewire;

use App\Models\Designation;
use App\Models\Employment;
use App\Models\Subject;
use App\Models\TeacherLevel;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class ReferenceDataManagement extends Component
{
    use WithPagination;

    #[Locked]
    public string $type;

    public string $search = '';
    public ?int $editingId = null;
    public string $name = '';
    public string $code = '';
    public bool $isActive = true;
    public bool $showModal = false;

    /** @var array<string, array{model: class-string<Model>, title: string, legacy: string, foreign_key: string}> */
    private const TYPES = [
        'subjects' => ['model' => Subject::class, 'title' => 'সাবজেক্ট', 'legacy' => 'subject', 'foreign_key' => 'subject_id'],
        'designations' => ['model' => Designation::class, 'title' => 'পদবি', 'legacy' => 'designation', 'foreign_key' => 'designation_id'],
        'teacher-levels' => ['model' => TeacherLevel::class, 'title' => 'শিক্ষক স্তর', 'legacy' => 'teacher_level', 'foreign_key' => 'teacher_level_id'],
        'employments' => ['model' => Employment::class, 'title' => 'চাকরির ধরন', 'legacy' => 'employment_type', 'foreign_key' => 'employment_id'],
    ];

    public function mount(string $type): void
    {
        abort_unless(array_key_exists($type, self::TYPES), 404);
        $this->type = $type;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $record = $this->modelQuery()->findOrFail($id);
        $this->editingId = $record->getKey();
        $this->name = (string) $record->getAttribute('name');
        $this->code = (string) ($record->getAttribute('code') ?? '');
        $this->isActive = (bool) $record->getAttribute('is_active');
        $this->showModal = true;
    }

    public function save(): void
    {
        $modelClass = $this->configuration()['model'];
        $table = (new $modelClass)->getTable();
        $rules = [
            'name' => ['required', 'string', 'max:255', Rule::unique($table, 'name')->ignore($this->editingId)],
            'isActive' => ['boolean'],
        ];
        $validated = $this->validate($rules, [
            'name.required' => 'নাম অবশ্যই দিতে হবে।',
            'name.unique' => 'এই নামটি ইতোমধ্যে আছে।',
            'code.unique' => 'এই কলেজ কোডটি ইতোমধ্যে আছে।',
        ]);

        DB::transaction(function () use ($validated, $modelClass): void {
            $record = $this->editingId === null ? new $modelClass : $this->modelQuery()->findOrFail($this->editingId);
            $oldName = $record->exists ? $record->getAttribute('name') : null;
            $record->fill(['name' => $validated['name'], 'is_active' => $validated['isActive']]);
            $record->save();

            if ($oldName !== null) {
                $teacherUpdates = [$this->configuration()['legacy'] => $record->getAttribute('name')];
                DB::table('teacher_profiles')->where($this->configuration()['foreign_key'], $record->getKey())->update($teacherUpdates);
            }
        });

        $this->resetForm();
        $this->showModal = false;
        Flux::toast(variant: 'success', text: 'তথ্য সফলভাবে সংরক্ষণ করা হয়েছে।');
    }

    public function delete(int $id): void
    {
        $record = $this->modelQuery()->withCount('teachers')->findOrFail($id);
        if ($record->getAttribute('teachers_count') > 0) {
            Flux::toast(variant: 'warning', text: 'শিক্ষকের সাথে যুক্ত থাকায় তথ্যটি মুছতে পারবেন না। নিষ্ক্রিয় করতে পারেন।');
            return;
        }
        $record->delete();
        Flux::toast(variant: 'success', text: 'তথ্য সফলভাবে মুছে ফেলা হয়েছে।');
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
        $this->showModal = false;
    }

    public function render(): View
    {
        $records = $this->modelQuery()->withCount('teachers')
            ->when($this->search !== '', fn ($query) => $query->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')->paginate(10);

        return view('livewire.reference-data-management', [
            'records' => $records,
            'title' => $this->configuration()['title'],
            'isCollege' => false,
        ]);
    }

    /** @return array{model: class-string<Model>, title: string, legacy: string, foreign_key: string} */
    private function configuration(): array
    {
        return self::TYPES[$this->type];
    }

    /** @return Builder<Model> */
    private function modelQuery(): Builder
    {
        $modelClass = $this->configuration()['model'];
        return $modelClass::query();
    }

    private function resetForm(): void
    {
        $this->reset('editingId', 'name', 'code');
        $this->isActive = true;
        $this->resetValidation();
    }
}
