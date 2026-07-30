<?php

namespace App\Livewire;

use App\Models\College;
use App\Models\CollegeProgram;
use App\Models\District;
use App\Models\Division;
use App\Models\Thana;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class CollegeManagement extends Component
{
    use WithPagination;

    public ?int $editingId = null;
    public string $search = '';
    public string $code = '';
    public string $name = '';
    public string $divisionId = '';
    public string $districtId = '';
    public string $thanaId = '';
    public string $address = '';
    public string $principalName = '';
    public string $collegeType = '';
    public bool $isActive = true;

    /** @var array<int, array{level: string, name: string}> */
    public array $programs = [];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDivisionId(): void
    {
        $this->reset('districtId', 'thanaId');
    }

    public function updatedDistrictId(): void
    {
        $this->reset('thanaId');
    }

    public function addProgram(): void
    {
        $this->programs[] = ['level' => 'degree', 'name' => ''];
    }

    public function removeProgram(int $index): void
    {
        unset($this->programs[$index]);
        $this->programs = array_values($this->programs);
    }

    public function edit(int $id): void
    {
        $college = College::query()->with('programs')->findOrFail($id);
        $this->editingId = $college->id;
        $this->code = (string) ($college->code ?? '');
        $this->name = $college->name;
        $this->divisionId = (string) ($college->division_id ?? '');
        $this->districtId = (string) ($college->district_id ?? '');
        $this->thanaId = (string) ($college->thana_id ?? '');
        $this->address = (string) ($college->address ?? '');
        $this->principalName = (string) ($college->principal_name ?? '');
        $this->collegeType = (string) ($college->college_type ?? '');
        $this->isActive = $college->is_active;
        $this->programs = $college->programs->map(fn (CollegeProgram $program): array => ['level' => $program->level, 'name' => $program->name])->all();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'code' => ['nullable', 'string', 'max:255', Rule::unique('colleges', 'code')->ignore($this->editingId)],
            'name' => ['required', 'string', 'max:255', Rule::unique('colleges', 'name')->ignore($this->editingId)],
            'divisionId' => ['required', Rule::exists('divisions', 'id')],
            'districtId' => ['required', Rule::exists('districts', 'id')],
            'thanaId' => ['required', Rule::exists('thanas', 'id')],
            'address' => ['required', 'string', 'max:2000'],
            'principalName' => ['required', 'string', 'max:255'],
            'collegeType' => ['required', Rule::in(['government', 'non_government', 'other'])],
            'isActive' => ['boolean'],
            'programs' => ['array'],
            'programs.*.level' => ['required', Rule::in(['degree', 'honours', 'masters', 'professional', 'other'])],
            'programs.*.name' => ['required', 'string', 'max:255'],
        ], [
            'name.required' => 'কলেজের নাম অবশ্যই দিতে হবে।',
            'divisionId.required' => 'বিভাগ নির্বাচন করুন।',
            'districtId.required' => 'জেলা নির্বাচন করুন।',
            'thanaId.required' => 'থানা নির্বাচন করুন।',
            'address.required' => 'কলেজের ঠিকানা দিন।',
            'principalName.required' => 'অধ্যক্ষের নাম দিন।',
            'collegeType.required' => 'কলেজের ধরন নির্বাচন করুন।',
            'programs.*.name.required' => 'কোর্স অথবা বিষয়ের নাম দিন।',
        ]);

        if (! District::query()->whereKey($validated['districtId'])->where('division_id', $validated['divisionId'])->exists()) {
            throw ValidationException::withMessages(['districtId' => 'নির্বাচিত জেলা এই বিভাগের অন্তর্ভুক্ত নয়।']);
        }
        if (! Thana::query()->whereKey($validated['thanaId'])->where('district_id', $validated['districtId'])->exists()) {
            throw ValidationException::withMessages(['thanaId' => 'নির্বাচিত থানা এই জেলার অন্তর্ভুক্ত নয়।']);
        }

        DB::transaction(function () use ($validated): void {
            $college = College::query()->updateOrCreate(['id' => $this->editingId], [
                'code' => blank($validated['code']) ? null : $validated['code'],
                'name' => $validated['name'],
                'division_id' => $validated['divisionId'],
                'district_id' => $validated['districtId'],
                'thana_id' => $validated['thanaId'],
                'address' => $validated['address'],
                'principal_name' => $validated['principalName'],
                'college_type' => $validated['collegeType'],
                'is_active' => $validated['isActive'],
            ]);
            $college->programs()->delete();
            $college->programs()->createMany(collect($validated['programs'])->unique(fn (array $program): string => $program['level'].'|'.mb_strtolower($program['name']))->values()->all());
            DB::table('teachers')->where('college_id', $college->id)->update(['college_code' => $college->code, 'college_name' => $college->name]);
        });

        $this->resetForm();
        Flux::toast(variant: 'success', text: 'কলেজের বিস্তারিত তথ্য সংরক্ষণ করা হয়েছে।');
    }

    public function delete(int $id): void
    {
        $college = College::query()->withCount('teachers')->findOrFail($id);
        if ($college->teachers_count > 0) {
            Flux::toast(variant: 'warning', text: 'শিক্ষকের সাথে যুক্ত থাকায় কলেজটি মুছতে পারবেন না। নিষ্ক্রিয় করুন।');
            return;
        }
        $college->delete();
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
    }

    public function render(): View
    {
        return view('livewire.college-management', [
            'colleges' => College::query()->with(['division:id,name', 'district:id,name', 'thana:id,name', 'programs'])->withCount('teachers')
                ->when($this->search !== '', fn ($query) => $query->where(fn ($query) => $query->where('name', 'like', "%{$this->search}%")->orWhere('code', 'like', "%{$this->search}%")))
                ->orderBy('name')->paginate(10),
            'divisions' => Division::query()->where('status', true)->orderBy('name')->get(['id', 'name', 'bn_name']),
            'districts' => District::query()->where('division_id', $this->divisionId ?: 0)->where('status', true)->orderBy('name')->get(['id', 'name', 'bn_name']),
            'thanas' => Thana::query()->where('district_id', $this->districtId ?: 0)->where('status', true)->orderBy('name')->get(['id', 'name', 'bn_name']),
        ]);
    }

    private function resetForm(): void
    {
        $this->reset('editingId', 'code', 'name', 'divisionId', 'districtId', 'thanaId', 'address', 'principalName', 'collegeType', 'programs');
        $this->isActive = true;
        $this->resetValidation();
    }
}
