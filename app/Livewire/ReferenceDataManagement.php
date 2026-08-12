<?php

namespace App\Livewire;

use App\Models\CollegeProgram;
use App\Models\Course;
use App\Models\Designation;
use App\Models\Employment;
use App\Models\ProgramLevel;
use App\Models\Subject;
use App\Models\TeacherLevel;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
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
    public string $level = '';
    public bool $isActive = true;
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;
    public string $deletingName = '';

    /** @var array<string, array{model: class-string<Model>, title: string}> */
    private const TYPES = [
        'subjects' => ['model' => Subject::class, 'title' => 'সাবজেক্ট'],
        'courses' => ['model' => Course::class, 'title' => 'কোর্স'],
        'designations' => ['model' => Designation::class, 'title' => 'পদবি'],
        'teacher-levels' => ['model' => TeacherLevel::class, 'title' => 'শিক্ষক স্তর'],
        'employments' => ['model' => Employment::class, 'title' => 'চাকরির ধরন'],
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
        $this->code = (string) ($record->getAttribute('subject_code') ?? '');
        $this->level = (string) ($record->getAttribute('level') ?? '');
        $this->isActive = (bool) $record->getAttribute('is_active');
        $this->showModal = true;
    }

    public function save(): void
    {
        $modelClass = $this->configuration()['model'];
        $table = (new $modelClass)->getTable();
        $nameRule = Rule::unique($table, 'name')->ignore($this->editingId);
        if ($this->isCourse()) {
            $nameRule->where('level', $this->level);
        }

        $rules = [
            'name' => ['required', 'string', 'max:255', $nameRule],
            'isActive' => ['boolean'],
        ];
        if ($this->isCourse()) {
            $rules['level'] = ['required', Rule::exists((new ProgramLevel)->getTable(), 'slug')->where(
                fn (QueryBuilder $query): QueryBuilder => $query->where('is_active', true)->whereIn('slug', ['degree', 'professional']),
            )];
        }
        if ($this->isSubject()) {
            $rules['code'] = ['nullable', 'string', 'max:255', Rule::unique($table, 'subject_code')->ignore($this->editingId)];
        }
        $validated = $this->validate($rules, [
            'name.required' => 'নাম অবশ্যই দিতে হবে।',
            'name.unique' => 'এই নামটি ইতোমধ্যে আছে।',
            'code.unique' => 'এই সাবজেক্ট কোডটি ইতোমধ্যে আছে।',
        ]);

        if ($this->isCourse() && $this->editingId !== null) {
            $course = $this->modelQuery()->findOrFail($this->editingId);
            if ($course->getAttribute('level') !== $validated['level'] && $this->usageCount($course) > 0) {
                $this->addError('level', 'কলেজের সাথে অধিভুক্ত কোর্সের প্রোগ্রাম লেভেল পরিবর্তন করা যাবে না।');

                return;
            }
        }

        DB::transaction(function () use ($validated, $modelClass): void {
            $record = $this->editingId === null ? new $modelClass : $this->modelQuery()->findOrFail($this->editingId);
            $previousName = (string) $record->getAttribute('name');
            $previousLevel = (string) $record->getAttribute('level');
            $record->fill([
                'name' => $validated['name'],
                'is_active' => $validated['isActive'],
                ...($this->isCourse() ? ['level' => $validated['level']] : []),
                ...($this->isSubject() ? ['subject_code' => filled($validated['code']) ? $validated['code'] : null] : []),
            ]);
            $record->save();

            if ($this->isCourse() && $record->wasChanged(['name', 'level'])) {
                $this->synchronizeCourseAffiliations($previousName, $previousLevel, $validated['name']);
            }
        });

        $this->resetForm();
        $this->showModal = false;
        Flux::toast(variant: 'success', text: 'তথ্য সফলভাবে সংরক্ষণ করা হয়েছে।');
    }

    public function confirmDelete(int $id): void
    {
        $record = $this->modelQuery()->findOrFail($id);
        if ($this->usageCount($record) > 0) {
            Flux::toast(variant: 'warning', text: $this->isCourse() ? 'কলেজের সাথে অধিভুক্ত থাকায় কোর্সটি মুছতে পারবেন না। নিষ্ক্রিয় করতে পারেন।' : 'শিক্ষকের সাথে যুক্ত থাকায় তথ্যটি মুছতে পারবেন না। নিষ্ক্রিয় করতে পারেন।');
            return;
        }

        $this->deletingId = $record->getKey();
        $this->deletingName = (string) $record->getAttribute('name');
        $this->showDeleteModal = true;
    }

    public function deleteConfirmed(): void
    {
        if ($this->deletingId === null) {
            return;
        }

        $record = $this->modelQuery()->findOrFail($this->deletingId);
        if ($this->usageCount($record) > 0) {
            $this->cancelDelete();
            Flux::toast(variant: 'warning', text: $this->isCourse() ? 'কলেজের সাথে অধিভুক্ত থাকায় কোর্সটি মুছতে পারবেন না। নিষ্ক্রিয় করতে পারেন।' : 'শিক্ষকের সাথে যুক্ত থাকায় তথ্যটি মুছতে পারবেন না। নিষ্ক্রিয় করতে পারেন।');
            return;
        }

        $record->delete();
        $this->cancelDelete();
        Flux::toast(variant: 'success', text: 'তথ্য সফলভাবে মুছে ফেলা হয়েছে।');
    }

    public function cancelDelete(): void
    {
        $this->reset('showDeleteModal', 'deletingId', 'deletingName');
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
        $this->showModal = false;
    }

    public function render(): View
    {
        $records = $this->modelQuery()
            ->when($this->search !== '', function (Builder $query): void {
                $query->where(function (Builder $query): void {
                    $query->where('name', 'like', "%{$this->search}%")
                        ->when($this->isSubject(), fn (Builder $query): Builder => $query->orWhere('subject_code', 'like', "%{$this->search}%"));
                });
            })
            ->orderBy('name')->paginate(10);

        return view('livewire.reference-data-management', [
            'records' => $records,
            'title' => $this->configuration()['title'],
            'isCollege' => false,
            'isSubject' => $this->isSubject(),
            'isCourse' => $this->isCourse(),
            'programLevels' => $this->isCourse() ? ProgramLevel::query()->where('is_active', true)->whereIn('slug', ['degree', 'professional'])->orderBy('sort_order')->get(['name', 'slug']) : collect(),
            'usageCounts' => $records->getCollection()->mapWithKeys(fn (Model $record): array => [$record->getKey() => $this->usageCount($record)]),
        ]);
    }

    /** @return array{model: class-string<Model>, title: string} */
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
        $this->reset('editingId', 'name', 'code', 'level');
        $this->isActive = true;
        $this->resetValidation();
    }

    private function isCourse(): bool
    {
        return $this->type === 'courses';
    }

    private function isSubject(): bool
    {
        return $this->type === 'subjects';
    }

    private function usageCount(Model $record): int
    {
        if (! $this->isCourse()) {
            return $record->teachers()->count();
        }

        return CollegeProgram::query()->where('level', $record->getAttribute('level'))->get(['items'])
            ->filter(fn (CollegeProgram $program): bool => collect($program->items)->containsStrict($record->getAttribute('name')))
            ->count();
    }

    private function synchronizeCourseAffiliations(string $previousName, string $previousLevel, string $name): void
    {
        CollegeProgram::query()->where('level', $previousLevel)->get()->each(function (CollegeProgram $program) use ($previousName, $name): void {
            $items = collect($program->items);
            if (! $items->containsStrict($previousName)) {
                return;
            }

            $program->update([
                'name' => $program->name === $previousName ? $name : $program->name,
                'items' => $items->map(fn (string $item): string => $item === $previousName ? $name : $item)->values()->all(),
            ]);
        });
    }
}
