<div class="mx-auto w-full max-w-6xl p-4 sm:p-6">
    <div class="grid gap-6 lg:grid-cols-[22rem_1fr]">
        <flux:card>
            <flux:heading size="lg">{{ $editingId ? $title.' সম্পাদনা' : 'নতুন '.$title }}</flux:heading>
            <form wire:submit="save" class="mt-5 grid gap-4">
                @if ($isCollege)
                    <flux:input wire:model="code" label="কলেজ কোড" placeholder="যেমন: ১০১০" />
                    @error('code') <flux:text class="text-red-600">{{ $message }}</flux:text> @enderror
                @endif
                <flux:input wire:model="name" :label="$title.' নাম'" placeholder="নাম লিখুন" required />
                @error('name') <flux:text class="text-red-600">{{ $message }}</flux:text> @enderror
                <flux:switch wire:model="isActive" label="সক্রিয়" />
                <div class="flex gap-2">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled">{{ $editingId ? 'আপডেট করুন' : 'সংরক্ষণ করুন' }}</flux:button>
                    @if ($editingId)<flux:button type="button" wire:click="cancelEdit">বাতিল</flux:button>@endif
                </div>
            </form>
        </flux:card>

        <flux:card class="overflow-hidden">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div><flux:heading size="lg">{{ $title }} ব্যবস্থাপনা</flux:heading><flux:text>মোট {{ $records->total() }}টি তথ্য</flux:text></div>
                <flux:input wire:model.live.debounce.300ms="search" type="search" placeholder="খুঁজুন..." icon="magnifying-glass" class="sm:max-w-xs" />
            </div>
            <div class="mt-5 overflow-x-auto">
                <flux:table>
                    <flux:table.columns>
                        @if ($isCollege)<flux:table.column>কোড</flux:table.column>@endif
                        <flux:table.column>নাম</flux:table.column>
                        <flux:table.column>ব্যবহৃত</flux:table.column>
                        <flux:table.column>অবস্থা</flux:table.column>
                        <flux:table.column></flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse ($records as $record)
                            <flux:table.row wire:key="reference-{{ $type }}-{{ $record->id }}">
                                @if ($isCollege)<flux:table.cell>{{ $record->code ?: '—' }}</flux:table.cell>@endif
                                <flux:table.cell class="font-medium">{{ $record->name }}</flux:table.cell>
                                <flux:table.cell>{{ $record->teachers_count }} জন শিক্ষক</flux:table.cell>
                                <flux:table.cell><flux:badge :color="$record->is_active ? 'green' : 'zinc'">{{ $record->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}</flux:badge></flux:table.cell>
                                <flux:table.cell><div class="flex justify-end gap-2"><flux:button size="sm" wire:click="edit({{ $record->id }})">সম্পাদনা</flux:button><flux:button size="sm" variant="danger" wire:click="delete({{ $record->id }})" wire:confirm="তথ্যটি মুছে ফেলবেন?">মুছুন</flux:button></div></flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row><flux:table.cell colspan="5" class="py-10 text-center">কোনো তথ্য পাওয়া যায়নি।</flux:table.cell></flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>
            <div class="mt-4">{{ $records->links() }}</div>
        </flux:card>
    </div>
</div>
