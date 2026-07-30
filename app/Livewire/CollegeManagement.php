<?php

namespace App\Livewire;

use App\Models\College;
use App\Models\District;
use App\Models\Division;
use App\Models\Thana;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
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
    public string $hasComputerLab = '';
    public string $labEquipmentType = '';
    public string $desktopCount = '';
    public string $laptopCount = '';
    public bool $isActive = true;

    /** @var array<int, array{level: string, names: array<int, string>, new_name: string}> */
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

    public function updatedHasComputerLab(string $value): void
    {
        if ($value === '0') {
            $this->reset('labEquipmentType', 'desktopCount', 'laptopCount');
        }
    }

    public function updatedLabEquipmentType(string $value): void
    {
        if ($value === 'desktop') {
            $this->reset('laptopCount');
        } elseif ($value === 'laptop') {
            $this->reset('desktopCount');
        }
    }

    public function addProgram(): void
    {
        $this->programs[] = ['level' => 'degree', 'names' => [], 'new_name' => ''];
    }

    public function removeProgram(int $index): void
    {
        unset($this->programs[$index]);
        $this->programs = array_values($this->programs);
    }

    public function addProgramTag(int $index): void
    {
        $name = trim($this->programs[$index]['new_name'] ?? '');
        if ($name === '') {
            return;
        }

        $existingNames = collect($this->programs[$index]['names'])
            ->map(fn (string $existingName): string => mb_strtolower($existingName));
        if (! $existingNames->contains(mb_strtolower($name))) {
            $this->programs[$index]['names'][] = $name;
        }
        $this->programs[$index]['new_name'] = '';
        $this->resetValidation("programs.{$index}.names");
    }

    public function removeProgramTag(int $groupIndex, int $tagIndex): void
    {
        unset($this->programs[$groupIndex]['names'][$tagIndex]);
        $this->programs[$groupIndex]['names'] = array_values($this->programs[$groupIndex]['names']);
    }

    public function updatedPrograms(mixed $value, ?string $key = null): void
    {
        if ($key !== null && preg_match('/^(\d+)\.level$/', $key, $matches) === 1) {
            $index = (int) $matches[1];
            $this->programs[$index]['names'] = [];
            $this->programs[$index]['new_name'] = '';
        }
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
        $this->hasComputerLab = $college->has_computer_lab === null ? '' : ($college->has_computer_lab ? '1' : '0');
        $this->labEquipmentType = (string) ($college->lab_equipment_type ?? $this->inferLabEquipmentType($college->desktop_count, $college->laptop_count));
        $this->desktopCount = (string) ($college->desktop_count ?? '');
        $this->laptopCount = (string) ($college->laptop_count ?? '');
        $this->isActive = $college->is_active;
        $this->programs = $college->programs->groupBy('level')->map(
            fn (Collection $programs, string $level): array => ['level' => $level, 'names' => $programs->pluck('name')->values()->all(), 'new_name' => ''],
        )->values()->all();
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
            'hasComputerLab' => ['required', Rule::in(['0', '1'])],
            'labEquipmentType' => [Rule::requiredIf($this->hasComputerLab === '1'), 'nullable', Rule::in(['desktop', 'laptop', 'both'])],
            'desktopCount' => [Rule::requiredIf($this->hasComputerLab === '1' && in_array($this->labEquipmentType, ['desktop', 'both'], true)), 'nullable', 'integer', 'min:1', 'max:100000'],
            'laptopCount' => [Rule::requiredIf($this->hasComputerLab === '1' && in_array($this->labEquipmentType, ['laptop', 'both'], true)), 'nullable', 'integer', 'min:1', 'max:100000'],
            'isActive' => ['boolean'],
            'programs' => ['array'],
            'programs.*.level' => ['required', 'distinct', Rule::in(['degree', 'honours', 'masters', 'professional', 'other'])],
            'programs.*.names' => ['required', 'array', 'min:1'],
            'programs.*.names.*' => ['required', 'string', 'max:255'],
            'programs.*.new_name' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'কলেজের নাম অবশ্যই দিতে হবে।',
            'divisionId.required' => 'বিভাগ নির্বাচন করুন।',
            'districtId.required' => 'জেলা নির্বাচন করুন।',
            'thanaId.required' => 'থানা নির্বাচন করুন।',
            'address.required' => 'কলেজের ঠিকানা দিন।',
            'principalName.required' => 'অধ্যক্ষের নাম দিন।',
            'collegeType.required' => 'কলেজের ধরন নির্বাচন করুন।',
            'hasComputerLab.required' => 'কলেজে কম্পিউটার ল্যাব আছে কি না নির্বাচন করুন।',
            'labEquipmentType.required' => 'ল্যাবে ডেস্কটপ, ল্যাপটপ অথবা উভয় আছে কি না নির্বাচন করুন।',
            'desktopCount.required' => 'ল্যাবে ডেস্কটপ কম্পিউটারের সংখ্যা দিন।',
            'laptopCount.required' => 'ল্যাবে ল্যাপটপের সংখ্যা দিন।',
            'programs.*.names.required' => 'অন্তত একটি কোর্স অথবা বিষয় ট্যাগ যোগ করুন।',
            'programs.*.names.min' => 'অন্তত একটি কোর্স অথবা বিষয় ট্যাগ যোগ করুন।',
            'programs.*.level.distinct' => 'একই কলেজ লেভেল একাধিকবার যোগ করা যাবে না; একই গ্রুপে সব ট্যাগ দিন।',
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
                'has_computer_lab' => $validated['hasComputerLab'] === '1',
                'lab_equipment_type' => $validated['hasComputerLab'] === '1' ? $validated['labEquipmentType'] : null,
                'desktop_count' => $validated['hasComputerLab'] === '1' && in_array($validated['labEquipmentType'], ['desktop', 'both'], true) ? ($validated['desktopCount'] ?? null) : null,
                'laptop_count' => $validated['hasComputerLab'] === '1' && in_array($validated['labEquipmentType'], ['laptop', 'both'], true) ? ($validated['laptopCount'] ?? null) : null,
                'is_active' => $validated['isActive'],
            ]);
            $college->programs()->delete();
            $programs = collect($validated['programs'])->flatMap(fn (array $group): array => collect($group['names'])
                ->map(fn (string $name): array => ['level' => $group['level'], 'name' => trim($name)])->all())
                ->unique(fn (array $program): string => $program['level'].'|'.mb_strtolower($program['name']))
                ->values()->all();
            $college->programs()->createMany($programs);
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
        $this->reset('editingId', 'code', 'name', 'divisionId', 'districtId', 'thanaId', 'address', 'principalName', 'collegeType', 'hasComputerLab', 'labEquipmentType', 'desktopCount', 'laptopCount', 'programs');
        $this->isActive = true;
        $this->resetValidation();
    }

    private function inferLabEquipmentType(?int $desktopCount, ?int $laptopCount): string
    {
        return match (true) {
            $desktopCount !== null && $laptopCount !== null => 'both',
            $desktopCount !== null => 'desktop',
            $laptopCount !== null => 'laptop',
            default => '',
        };
    }
}
