<?php

namespace App\Livewire;

use App\Enums\ApprovalStatus;
use App\Models\College;
use App\Models\CollegeProgram;
use App\Models\Course;
use App\Models\District;
use App\Models\Division;
use App\Models\ProgramLevel;
use App\Models\Subject;
use App\Models\Thana;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class CollegeForm extends Component
{
    public ?int $editingId = null;
    public string $college_code = '';
    public string $name = '';
    public string $divisionId = '';
    public string $districtId = '';
    public string $thanaId = '';
    public string $address = '';
    public string $collegeEmail = '';
    public string $collegeWebsite = '';
    public string $collegeType = '';
    public string $hasComputerLab = '';
    public string $labEquipmentType = '';
    public string $desktopCount = '';
    public string $laptopCount = '';
    public bool $isActive = true;

    /** @var array<int, array{level: string, names: array<int, string>, new_name: string}> */
    public array $programs = [];

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
        $level = ProgramLevel::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->value('slug');

        if ($level !== null) {
            $this->programs[] = ['level' => $level, 'names' => [], 'new_name' => ''];
        }
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

    public function mount(?College $college = null): void
    {
        abort_unless(auth()->user()->hasRole('admin') || auth()->user()->hasRole('principal'), 403);
        if ($college?->exists && auth()->user()->hasRole('principal')) {
            abort_unless($college->id === auth()->user()->college_id && auth()->user()->isApproved(), 403);
        }
        if ($college !== null && $college->exists) {
            $this->loadCollege($college);
        }
    }

    private function loadCollege(College $college): void
    {
        $college->load('programs');
        $this->editingId = $college->id;
        $this->college_code = (string) ($college->college_code ?? '');
        $this->name = $college->name;
        $this->divisionId = (string) ($college->division_id ?? '');
        $this->districtId = (string) ($college->district_id ?? '');
        $this->thanaId = (string) ($college->thana_id ?? '');
        $this->address = (string) ($college->address ?? '');
        $this->collegeEmail = (string) ($college->college_email ?? '');
        $this->collegeWebsite = (string) ($college->college_website ?? '');
        $this->collegeType = (string) ($college->college_type ?? '');
        $this->hasComputerLab = $college->has_computer_lab === null ? '' : ($college->has_computer_lab ? '1' : '0');
        $this->labEquipmentType = (string) ($college->lab_equipment_type ?? $this->inferLabEquipmentType($college->desktop_count, $college->laptop_count));
        $this->desktopCount = (string) ($college->desktop_count ?? '');
        $this->laptopCount = (string) ($college->laptop_count ?? '');
        $this->isActive = $college->is_active;
        $this->programs = $college->programs->groupBy('level')->map(
            fn (Collection $programs, string $level): array => [
                'level' => $level,
                'names' => $programs->flatMap(fn (CollegeProgram $program): array => $program->items ?: [$program->name])->filter()->unique()->values()->all(),
                'new_name' => '',
            ],
        )->values()->all();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'college_code' => ['nullable', 'string', 'max:255', Rule::unique('colleges', 'college_code')->ignore($this->editingId)],
            'name' => ['required', 'string', 'max:255', Rule::unique('colleges', 'name')->ignore($this->editingId)],
            'divisionId' => ['required', Rule::exists('divisions', 'id')],
            'districtId' => ['required', Rule::exists('districts', 'id')],
            'thanaId' => ['required', Rule::exists('thanas', 'id')],
            'address' => ['required', 'string', 'max:2000'],
            'collegeEmail' => ['nullable', 'email:rfc', 'max:255'],
            'collegeWebsite' => ['nullable', 'url', 'max:255'],
            'collegeType' => ['required', Rule::in(['government', 'non_government', 'other'])],
            'hasComputerLab' => ['required', Rule::in(['0', '1'])],
            'labEquipmentType' => [Rule::requiredIf($this->hasComputerLab === '1'), 'nullable', Rule::in(['desktop', 'laptop', 'both'])],
            'desktopCount' => [Rule::requiredIf($this->hasComputerLab === '1' && in_array($this->labEquipmentType, ['desktop', 'both'], true)), 'nullable', 'integer', 'min:1', 'max:100000'],
            'laptopCount' => [Rule::requiredIf($this->hasComputerLab === '1' && in_array($this->labEquipmentType, ['laptop', 'both'], true)), 'nullable', 'integer', 'min:1', 'max:100000'],
            'isActive' => ['boolean'],
            'programs' => ['array'],
            'programs.*.level' => ['required', 'distinct', Rule::exists('program_levels', 'slug')->where('is_active', true)],
            'programs.*.names' => ['required', 'array', 'min:1'],
            'programs.*.names.*' => ['required', 'string', 'max:255'],
            'programs.*.new_name' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'কলেজের নাম অবশ্যই দিতে হবে।',
            'divisionId.required' => 'বিভাগ নির্বাচন করুন।',
            'districtId.required' => 'জেলা নির্বাচন করুন।',
            'thanaId.required' => 'থানা নির্বাচন করুন।',
            'address.required' => 'কলেজের ঠিকানা দিন।',
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

        $this->validateAcademicAffiliations($validated['programs']);

        DB::transaction(function () use ($validated): void {
            $user = auth()->user();
            $college = College::query()->updateOrCreate(['id' => $this->editingId], [
                'college_code' => blank($validated['college_code']) ? null : $validated['college_code'],
                'name' => $validated['name'],
                'division_id' => $validated['divisionId'],
                'district_id' => $validated['districtId'],
                'thana_id' => $validated['thanaId'],
                'address' => $validated['address'],
                'college_email' => blank($validated['collegeEmail']) ? null : $validated['collegeEmail'],
                'college_website' => blank($validated['collegeWebsite']) ? null : $validated['collegeWebsite'],
                'college_type' => $validated['collegeType'],
                'has_computer_lab' => $validated['hasComputerLab'] === '1',
                'lab_equipment_type' => $validated['hasComputerLab'] === '1' ? $validated['labEquipmentType'] : null,
                'desktop_count' => $validated['hasComputerLab'] === '1' && in_array($validated['labEquipmentType'], ['desktop', 'both'], true) ? ($validated['desktopCount'] ?? null) : null,
                'laptop_count' => $validated['hasComputerLab'] === '1' && in_array($validated['labEquipmentType'], ['laptop', 'both'], true) ? ($validated['laptopCount'] ?? null) : null,
                'is_active' => $validated['isActive'],
                'submitted_by' => $user->hasRole('principal')
                    ? (College::query()->whereKey($this->editingId)->value('submitted_by') ?: $user->id)
                    : College::query()->whereKey($this->editingId)->value('submitted_by'),
                'approval_status' => $user->isAdmin() ? ApprovalStatus::Approved : (College::query()->whereKey($this->editingId)->value('approval_status') ?: ApprovalStatus::Approved),
                'approved_by' => $user->isAdmin() ? $user->id : College::query()->whereKey($this->editingId)->value('approved_by'),
                'approved_at' => $user->isAdmin() ? now() : College::query()->whereKey($this->editingId)->value('approved_at'),
            ]);
            if ($user->hasRole('principal')) {
                $user->update(['college_id' => $college->id]);
            }
            $college->programs()->delete();
            $programs = collect($validated['programs'])->map(function (array $group): array {
                $items = collect($group['names'])->map(fn (string $name): string => trim($name))
                    ->unique(fn (string $name): string => mb_strtolower($name))->values()->all();

                return ['level' => $group['level'], 'name' => $items[0], 'items' => $items];
            })->values()->all();
            $college->programs()->createMany($programs);
        });

        Flux::toast(variant: 'success', text: 'কলেজের বিস্তারিত তথ্য সংরক্ষণ করা হয়েছে।');
        $this->redirectRoute('colleges.manage', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.college-form', [
            'divisions' => Division::query()->where('status', true)->orderBy('name')->get(['id', 'name', 'bn_name']),
            'districts' => District::query()->where('division_id', $this->divisionId ?: 0)->where('status', true)->orderBy('name')->get(['id', 'name', 'bn_name']),
            'thanas' => Thana::query()->where('district_id', $this->districtId ?: 0)->where('status', true)->orderBy('name')->get(['id', 'name', 'bn_name']),
            'subjectSuggestions' => Subject::query()->where('is_active', true)->orderBy('name')->pluck('name'),
            'courseSuggestions' => Course::query()->where('is_active', true)->orderBy('name')->get(['name', 'level'])->groupBy('level')->map->pluck('name'),
            'programLevels' => ProgramLevel::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'slug']),
        ]);
    }

    /** @param array<int, array{level: string, names: array<int, string>, new_name: string}> $programs */
    private function validateAcademicAffiliations(array $programs): void
    {
        $activeSubjects = Subject::query()->where('is_active', true)->pluck('name')->map(fn (string $name): string => mb_strtolower($name));
        $activeCourses = Course::query()->where('is_active', true)->get(['name', 'level'])->groupBy('level')
            ->map(fn (Collection $courses): Collection => $courses->pluck('name')->map(fn (string $name): string => mb_strtolower($name)));

        foreach ($programs as $index => $program) {
            $allowedNames = in_array($program['level'], ['degree', 'professional'], true)
                ? $activeCourses->get($program['level'], collect())
                : $activeSubjects;

            foreach ($program['names'] as $name) {
                if (! $allowedNames->contains(mb_strtolower(trim($name)))) {
                    throw ValidationException::withMessages([
                        "programs.{$index}.names" => 'শুধু সক্রিয় মাস্টার তালিকা থেকে অধিভুক্ত কোর্স অথবা বিষয় নির্বাচন করুন।',
                    ]);
                }
            }
        }
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
