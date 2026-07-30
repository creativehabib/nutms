<div class="mx-auto w-full max-w-7xl p-4 sm:p-6">
    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="border-b border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-950/70 sm:p-5">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <flux:heading size="xl">কলেজ ব্যবস্থাপনা</flux:heading>
                    <flux:text>প্রয়োজনীয় সংক্ষিপ্ত তথ্য দেখুন; বিস্তারিত জানতে “দেখুন” ব্যবহার করুন।</flux:text>
                </div>
                <flux:button variant="primary" icon="plus" :href="route('colleges.create')" wire:navigate>নতুন কলেজ তৈরি</flux:button>
            </div>
            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <flux:input wire:model.live.debounce.300ms="search" type="search" icon="magnifying-glass" placeholder="কলেজের নাম বা কোড দিয়ে খুঁজুন..." class="sm:max-w-md" />
                <flux:badge color="indigo">মোট {{ $colleges->total() }}টি কলেজ</flux:badge>
            </div>
        </div>

        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>কলেজ</flux:table.column>
                    <flux:table.column>অবস্থান</flux:table.column>
                    <flux:table.column>ধরন</flux:table.column>
                    <flux:table.column>ল্যাব</flux:table.column>
                    <flux:table.column>শিক্ষক</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse($colleges as $college)
                        <flux:table.row wire:key="college-{{ $college->id }}">
                            <flux:table.cell><p class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $college->name }}</p><flux:text>কোড: {{ $college->code ?: 'উল্লেখ নেই' }}</flux:text></flux:table.cell>
                            <flux:table.cell>{{ $college->thana?->name ?: '—' }}, {{ $college->district?->name ?: '—' }}</flux:table.cell>
                            <flux:table.cell><flux:badge :color="$college->college_type === 'government' ? 'green' : 'zinc'">{{ ['government'=>'সরকারি','non_government'=>'বেসরকারি','other'=>'অন্যান্য'][$college->college_type] ?? 'অনির্ধারিত' }}</flux:badge></flux:table.cell>
                            <flux:table.cell>@if($college->has_computer_lab)<span class="font-medium text-green-700 dark:text-green-400">আছে</span>@elseif($college->has_computer_lab === false)<span class="text-zinc-500">নেই</span>@else<span class="text-zinc-500">অনির্ধারিত</span>@endif</flux:table.cell>
                            <flux:table.cell>{{ $college->teachers_count }} জন</flux:table.cell>
                            <flux:table.cell><div class="flex justify-end gap-2"><flux:button size="sm" icon="eye" :href="route('colleges.show', $college)" wire:navigate>দেখুন</flux:button><flux:button size="sm" icon="pencil-square" :href="route('colleges.edit', $college)" wire:navigate>সম্পাদনা</flux:button><flux:button size="sm" variant="danger" wire:click="delete({{ $college->id }})" wire:confirm="কলেজটি মুছবেন?">মুছুন</flux:button></div></flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row><flux:table.cell colspan="6" class="py-10 text-center">কোনো কলেজ পাওয়া যায়নি।</flux:table.cell></flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
        <div class="border-t border-zinc-200 p-4 dark:border-zinc-700">{{ $colleges->links() }}</div>
    </div>
</div>
