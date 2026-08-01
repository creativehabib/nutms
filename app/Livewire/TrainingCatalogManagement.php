<?php

namespace App\Livewire;

use App\Models\TrainingInstitute;
use App\Models\TrainingType;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TrainingCatalogManagement extends Component
{
    use WithPagination;

    public ?int $editingInstituteId = null;
    public string $instituteName = '';
    public bool $instituteIsActive = true;
    public ?int $editingTrainingTypeId = null;
    public string $trainingTypeName = '';
    public string $trainingInstituteId = '';
    public string $durationValue = '';
    public string $durationUnit = 'days';
    public bool $trainingTypeIsActive = true;
    public string $search = '';
    public bool $showDeleteModal = false;
    public ?string $deletingType = null;
    public ?int $deletingId = null;
    public string $deletingName = '';

    public function saveInstitute(): void
    {
        $validated = $this->validate([
            'instituteName' => ['required', 'string', 'max:255', Rule::unique('training_institutes', 'name')->ignore($this->editingInstituteId)],
            'instituteIsActive' => ['boolean'],
        ], ['instituteName.required' => 'প্রতিষ্ঠানের নাম অবশ্যই দিতে হবে।', 'instituteName.unique' => 'এই প্রতিষ্ঠানটি ইতোমধ্যে আছে।']);

        TrainingInstitute::query()->updateOrCreate(['id' => $this->editingInstituteId], [
            'name' => $validated['instituteName'],
            'is_active' => $validated['instituteIsActive'],
        ]);
        $this->resetInstituteForm();
        Flux::toast(variant: 'success', text: 'ট্রেনিং প্রতিষ্ঠান সংরক্ষণ করা হয়েছে।');
    }

    public function editInstitute(int $id): void
    {
        $institute = TrainingInstitute::query()->findOrFail($id);
        $this->editingInstituteId = $institute->id;
        $this->instituteName = $institute->name;
        $this->instituteIsActive = $institute->is_active;
    }

    public function confirmDeleteInstitute(int $id): void
    {
        $institute = TrainingInstitute::query()->withCount('trainingTypes')->findOrFail($id);
        if ($institute->training_types_count > 0) {
            Flux::toast(variant: 'warning', text: 'এই প্রতিষ্ঠানের অধীনে ট্রেনিং আছে। মুছে না দিয়ে নিষ্ক্রিয় করুন।');
            return;
        }

        $this->deletingType = 'institute';
        $this->deletingId = $institute->id;
        $this->deletingName = $institute->name;
        $this->showDeleteModal = true;
    }

    public function saveTrainingType(): void
    {
        $validated = $this->validate([
            'trainingInstituteId' => ['required', Rule::exists('training_institutes', 'id')],
            'trainingTypeName' => ['required', 'string', 'max:255', Rule::unique('training_types', 'name')->where('training_institute_id', $this->trainingInstituteId)->ignore($this->editingTrainingTypeId)],
            'durationValue' => ['required', 'integer', 'min:1', 'max:999'],
            'durationUnit' => ['required', Rule::in(['hours', 'days', 'weeks', 'months'])],
            'trainingTypeIsActive' => ['boolean'],
        ], [
            'trainingInstituteId.required' => 'প্রতিষ্ঠান নির্বাচন করুন।',
            'trainingTypeName.required' => 'ট্রেনিংয়ের নাম অবশ্যই দিতে হবে।',
            'trainingTypeName.unique' => 'এই প্রতিষ্ঠানে একই নামের ট্রেনিং ইতোমধ্যে আছে।',
            'durationValue.required' => 'ট্রেনিংয়ের সময়কাল দিন।',
        ]);

        TrainingType::query()->updateOrCreate(['id' => $this->editingTrainingTypeId], [
            'training_institute_id' => $validated['trainingInstituteId'],
            'name' => $validated['trainingTypeName'],
            'duration_value' => $validated['durationValue'],
            'duration_unit' => $validated['durationUnit'],
            'is_active' => $validated['trainingTypeIsActive'],
        ]);
        $this->resetTrainingTypeForm();
        Flux::toast(variant: 'success', text: 'ট্রেনিং টাইপ ও সময়কাল সংরক্ষণ করা হয়েছে।');
    }

    public function editTrainingType(int $id): void
    {
        $trainingType = TrainingType::query()->findOrFail($id);
        $this->editingTrainingTypeId = $trainingType->id;
        $this->trainingInstituteId = (string) $trainingType->training_institute_id;
        $this->trainingTypeName = $trainingType->name;
        $this->durationValue = (string) $trainingType->duration_value;
        $this->durationUnit = $trainingType->duration_unit ?? 'days';
        $this->trainingTypeIsActive = $trainingType->is_active;
    }

    public function confirmDeleteTrainingType(int $id): void
    {
        $trainingType = TrainingType::query()->withCount('teachers')->findOrFail($id);
        if ($trainingType->teachers_count > 0) {
            Flux::toast(variant: 'warning', text: 'শিক্ষকের সাথে যুক্ত থাকায় ট্রেনিংটি মুছতে পারবেন না। নিষ্ক্রিয় করুন।');
            return;
        }

        $this->deletingType = 'training-type';
        $this->deletingId = $trainingType->id;
        $this->deletingName = $trainingType->name;
        $this->showDeleteModal = true;
    }

    public function deleteConfirmed(): void
    {
        if ($this->deletingId === null || $this->deletingType === null) {
            return;
        }

        if ($this->deletingType === 'institute') {
            $institute = TrainingInstitute::query()->withCount('trainingTypes')->findOrFail($this->deletingId);
            if ($institute->training_types_count > 0) {
                $this->cancelDelete();
                Flux::toast(variant: 'warning', text: 'এই প্রতিষ্ঠানের অধীনে ট্রেনিং আছে। মুছে না দিয়ে নিষ্ক্রিয় করুন।');
                return;
            }

            $institute->delete();
        }

        if ($this->deletingType === 'training-type') {
            $trainingType = TrainingType::query()->withCount('teachers')->findOrFail($this->deletingId);
            if ($trainingType->teachers_count > 0) {
                $this->cancelDelete();
                Flux::toast(variant: 'warning', text: 'শিক্ষকের সাথে যুক্ত থাকায় ট্রেনিংটি মুছতে পারবেন না। নিষ্ক্রিয় করুন।');
                return;
            }

            $trainingType->delete();
        }

        $this->cancelDelete();
        Flux::toast(variant: 'success', text: 'তথ্য সফলভাবে মুছে ফেলা হয়েছে।');
    }

    public function cancelDelete(): void
    {
        $this->reset('showDeleteModal', 'deletingType', 'deletingId', 'deletingName');
    }

    public function cancelInstituteEdit(): void
    {
        $this->resetInstituteForm();
    }

    public function cancelTrainingTypeEdit(): void
    {
        $this->resetTrainingTypeForm();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.training-catalog-management', [
            'institutes' => TrainingInstitute::query()->withCount('trainingTypes')->orderBy('name')->get(),
            'trainingTypes' => TrainingType::query()->with(['trainingInstitute:id,name'])->withCount('teachers')
                ->when($this->search !== '', fn ($query) => $query->where('name', 'like', "%{$this->search}%"))
                ->latest()->paginate(10),
        ]);
    }

    private function resetInstituteForm(): void
    {
        $this->reset('editingInstituteId', 'instituteName');
        $this->instituteIsActive = true;
        $this->resetValidation();
    }

    private function resetTrainingTypeForm(): void
    {
        $this->reset('editingTrainingTypeId', 'trainingTypeName', 'trainingInstituteId', 'durationValue');
        $this->durationUnit = 'days';
        $this->trainingTypeIsActive = true;
        $this->resetValidation();
    }
}
