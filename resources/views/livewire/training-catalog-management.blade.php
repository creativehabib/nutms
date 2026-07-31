<div class="mx-auto w-full max-w-7xl space-y-6 p-4 sm:p-6">
    <div>
        <flux:heading size="xl">ট্রেনিং ক্যাটালগ</flux:heading>
        <flux:text>প্রতিষ্ঠানভিত্তিক ট্রেনিং টাইপ ও নির্ধারিত সময়কাল পরিচালনা করুন।</flux:text>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <flux:card>
            <flux:heading size="lg">{{ $editingInstituteId ? 'প্রতিষ্ঠান সম্পাদনা' : 'নতুন ট্রেনিং প্রতিষ্ঠান' }}</flux:heading>
            <form wire:submit="saveInstitute" class="mt-4 grid gap-4 sm:grid-cols-[1fr_auto] sm:items-end">
                <flux:input wire:model="instituteName" label="প্রতিষ্ঠানের নাম" required />
                <flux:button type="submit" variant="primary">{{ $editingInstituteId ? 'আপডেট' : 'সংরক্ষণ' }}</flux:button>
                <flux:switch wire:model="instituteIsActive" label="সক্রিয়" />
                @if ($editingInstituteId)<flux:button type="button" wire:click="cancelInstituteEdit">বাতিল</flux:button>@endif
            </form>
            @error('instituteName')<flux:text class="mt-2 text-red-600">{{ $message }}</flux:text>@enderror
            <div class="mt-5 divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($institutes as $institute)
                    <div wire:key="institute-{{ $institute->id }}" class="flex items-center justify-between gap-3 py-3">
                        <div><p class="font-medium">{{ $institute->name }}</p><flux:text>{{ $institute->training_types_count }}টি ট্রেনিং · {{ $institute->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}</flux:text></div>
                        <div class="flex gap-2"><flux:button size="sm" wire:click="editInstitute({{ $institute->id }})">সম্পাদনা</flux:button><flux:button size="sm" variant="danger" wire:click="deleteInstitute({{ $institute->id }})" wire:confirm="প্রতিষ্ঠানটি মুছবেন?">মুছুন</flux:button></div>
                    </div>
                @empty<flux:text class="py-6 text-center">কোনো প্রতিষ্ঠান নেই।</flux:text>@endforelse
            </div>
        </flux:card>

        <flux:card>
            <flux:heading size="lg">{{ $editingTrainingTypeId ? 'ট্রেনিং টাইপ সম্পাদনা' : 'নতুন ট্রেনিং টাইপ' }}</flux:heading>
            <form wire:submit="saveTrainingType" class="mt-4 grid gap-4 sm:grid-cols-2">
                <flux:select wire:model="trainingInstituteId" label="প্রতিষ্ঠান" required>
                    <option value="">নির্বাচন করুন</option>
                    @foreach ($institutes as $institute)<option value="{{ $institute->id }}">{{ $institute->name }}{{ $institute->is_active ? '' : ' (নিষ্ক্রিয়)' }}</option>@endforeach
                </flux:select>
                <flux:input wire:model="trainingTypeName" label="ট্রেনিংয়ের নাম" required />
                <flux:input wire:model="durationValue" type="number" min="1" max="999" label="সময়কাল" required />
                <flux:select wire:model="durationUnit" label="সময়কালের একক"><option value="hours">ঘণ্টা</option><option value="days">দিন</option><option value="weeks">সপ্তাহ</option><option value="months">মাস</option></flux:select>
                <flux:switch wire:model="trainingTypeIsActive" label="সক্রিয়" />
                <div class="flex justify-end gap-2"><flux:button type="submit" variant="primary">{{ $editingTrainingTypeId ? 'আপডেট' : 'সংরক্ষণ' }}</flux:button>@if ($editingTrainingTypeId)<flux:button type="button" wire:click="cancelTrainingTypeEdit">বাতিল</flux:button>@endif</div>
            </form>
            @error('trainingInstituteId')<flux:text class="mt-2 text-red-600">{{ $message }}</flux:text>@enderror
            @error('trainingTypeName')<flux:text class="mt-2 text-red-600">{{ $message }}</flux:text>@enderror
            @error('durationValue')<flux:text class="mt-2 text-red-600">{{ $message }}</flux:text>@enderror
        </flux:card>
    </div>

    <flux:card>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"><flux:heading size="lg">ট্রেনিং টাইপসমূহ</flux:heading><flux:input wire:model.live.debounce.300ms="search" type="search" icon="magnifying-glass" placeholder="ট্রেনিং খুঁজুন..." /></div>
        <div class="mt-4 overflow-x-auto"><flux:table><flux:table.columns><flux:table.column>প্রতিষ্ঠান</flux:table.column><flux:table.column>ট্রেনিং</flux:table.column><flux:table.column>সময়কাল</flux:table.column><flux:table.column>শিক্ষক</flux:table.column><flux:table.column>অবস্থা</flux:table.column><flux:table.column></flux:table.column></flux:table.columns><flux:table.rows>
            @forelse ($trainingTypes as $trainingType)<flux:table.row wire:key="training-type-{{ $trainingType->id }}"><flux:table.cell>{{ $trainingType->trainingInstitute->name }}</flux:table.cell><flux:table.cell class="font-medium">{{ $trainingType->name }}</flux:table.cell><flux:table.cell>{{ $trainingType->duration_value }} {{ ['hours' => 'ঘণ্টা', 'days' => 'দিন', 'weeks' => 'সপ্তাহ', 'months' => 'মাস'][$trainingType->duration_unit] ?? '' }}</flux:table.cell><flux:table.cell>{{ $trainingType->teachers_count }} জন</flux:table.cell><flux:table.cell>{{ $trainingType->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}</flux:table.cell><flux:table.cell><div class="flex justify-end gap-2"><flux:button size="sm" wire:click="editTrainingType({{ $trainingType->id }})">সম্পাদনা</flux:button><flux:button size="sm" variant="danger" wire:click="deleteTrainingType({{ $trainingType->id }})" wire:confirm="ট্রেনিংটি মুছবেন?">মুছুন</flux:button></div></flux:table.cell></flux:table.row>
            @empty<flux:table.row><flux:table.cell colspan="6" class="py-8 text-center">কোনো ট্রেনিং টাইপ নেই।</flux:table.cell></flux:table.row>@endforelse
        </flux:table.rows></flux:table></div><div class="mt-4">{{ $trainingTypes->links() }}</div>
    </flux:card>
</div>
