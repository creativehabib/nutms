<div class="mx-auto w-full max-w-7xl space-y-6 p-4 sm:p-6">
    <div><flux:heading size="xl">কলেজ ব্যবস্থাপনা</flux:heading><flux:text>প্রশাসনিক অবস্থান, অধ্যক্ষ এবং একাডেমিক প্রোগ্রামসহ সমৃদ্ধ কলেজ প্রোফাইল।</flux:text></div>
    <div class="grid gap-6 xl:grid-cols-[28rem_1fr]">
        <flux:card>
            <flux:heading size="lg">{{ $editingId ? 'কলেজ সম্পাদনা' : 'নতুন কলেজ' }}</flux:heading>
            <form wire:submit="save" class="mt-5 grid gap-4">
                <div class="grid gap-4 sm:grid-cols-2"><flux:input wire:model="code" label="কলেজ কোড" /><flux:input wire:model="name" label="কলেজের নাম" required /></div>
                <div class="grid gap-4 sm:grid-cols-3">
                    <flux:select wire:model.live="divisionId" label="বিভাগ" required><option value="">নির্বাচন</option>@foreach($divisions as $division)<option value="{{ $division->id }}">{{ $division->bn_name ?: $division->name }}</option>@endforeach</flux:select>
                    <flux:select wire:model.live="districtId" label="জেলা" required><option value="">নির্বাচন</option>@foreach($districts as $district)<option value="{{ $district->id }}">{{ $district->bn_name ?: $district->name }}</option>@endforeach</flux:select>
                    <flux:select wire:model="thanaId" label="থানা" required><option value="">নির্বাচন</option>@foreach($thanas as $thana)<option value="{{ $thana->id }}">{{ $thana->bn_name ?: $thana->name }}</option>@endforeach</flux:select>
                </div>
                @error('divisionId')<flux:text class="text-red-600">{{ $message }}</flux:text>@enderror @error('districtId')<flux:text class="text-red-600">{{ $message }}</flux:text>@enderror @error('thanaId')<flux:text class="text-red-600">{{ $message }}</flux:text>@enderror
                <flux:textarea wire:model="address" label="পূর্ণ ঠিকানা" rows="2" required />
                <flux:input wire:model="principalName" label="কলেজ অধ্যক্ষের নাম" required />
                <flux:select wire:model="collegeType" label="কলেজের ধরন" required><option value="">নির্বাচন</option><option value="government">সরকারি</option><option value="non_government">বেসরকারি</option><option value="other">অন্যান্য</option></flux:select>
                <flux:select wire:model.live="hasComputerLab" label="কম্পিউটার ল্যাব আছে?" required><option value="">নির্বাচন</option><option value="1">হ্যাঁ, ল্যাব আছে</option><option value="0">না, ল্যাব নেই</option></flux:select>
                @if ($hasComputerLab === '1')
                    <flux:select wire:model.live="labEquipmentType" label="ল্যাবে কী ধরনের ডিভাইস আছে?" required><option value="">নির্বাচন</option><option value="desktop">শুধু ডেস্কটপ</option><option value="laptop">শুধু ল্যাপটপ</option><option value="both">ডেস্কটপ ও ল্যাপটপ উভয়ই</option></flux:select>
                    <div class="grid gap-4 sm:grid-cols-2">
                        @if (in_array($labEquipmentType, ['desktop', 'both'], true))<flux:input wire:model="desktopCount" type="number" min="1" label="ডেস্কটপ কম্পিউটার সংখ্যা" required />@endif
                        @if (in_array($labEquipmentType, ['laptop', 'both'], true))<flux:input wire:model="laptopCount" type="number" min="1" label="ল্যাপটপ সংখ্যা" required />@endif
                    </div>
                @endif
                <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                    <div class="flex items-center justify-between gap-3"><flux:heading>কলেজ লেভেল, কোর্স ও বিষয়</flux:heading><flux:button type="button" size="sm" icon="plus" wire:click="addProgram">আরেকটি লেভেল</flux:button></div>
                    <flux:text class="mt-1">লেভেল বেছে নিয়ে কোর্স বা বিষয় লিখুন। Enter চাপলেই সেটি নিচে pill হিসেবে যুক্ত হবে।</flux:text>
                    <div class="mt-3 grid gap-4">
                        @foreach($programs as $index => $program)
                            <div wire:key="college-program-{{ $index }}" class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                                <div class="grid gap-3 sm:grid-cols-[10rem_minmax(0,1fr)_auto] sm:items-end">
                                    <flux:select wire:model.live="programs.{{ $index }}.level" label="কলেজ লেভেল"><option value="degree">ডিগ্রি</option><option value="honours">অনার্স</option><option value="masters">মাস্টার্স</option><option value="professional">প্রফেশনাল</option><option value="other">অন্যান্য</option></flux:select>
                                    <flux:field>
                                        <flux:label>{{ $program['level'] === 'degree' ? 'ডিগ্রি কোর্স' : 'বিষয়ের নাম' }}</flux:label>
                                        <flux:input class="w-full" wire:model="programs.{{ $index }}.new_name" wire:keydown.enter.prevent.stop="addProgramTag({{ $index }})" list="program-suggestions-{{ $index }}" autocomplete="off" :placeholder="$program['level'] === 'degree' ? 'যেমন BA লিখে Enter চাপুন' : 'যেমন বাংলা লিখে Enter চাপুন'" aria-label="কোর্স অথবা বিষয় লিখে Enter চাপুন" />
                                        <flux:description>লিখে Enter চাপুন</flux:description>
                                        <datalist id="program-suggestions-{{ $index }}">@foreach($program['level'] === 'degree' ? $degreeCourseSuggestions : $subjectSuggestions as $suggestion)<option value="{{ $suggestion }}"></option>@endforeach</datalist>
                                    </flux:field>
                                    <flux:button type="button" size="sm" variant="danger" wire:click="removeProgram({{ $index }})">গ্রুপ বাদ</flux:button>
                                </div>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @forelse($program['names'] as $tagIndex => $programName)<flux:badge wire:key="program-tag-{{ $index }}-{{ $tagIndex }}" color="indigo" class="gap-1 py-1">{{ $programName }}<button type="button" wire:click="removeProgramTag({{ $index }}, {{ $tagIndex }})" class="rounded-full px-1 hover:bg-indigo-200 dark:hover:bg-indigo-900" aria-label="{{ $programName }} বাদ দিন">×</button></flux:badge>@empty<flux:text>এখনও কোনো কোর্স বা বিষয় নির্বাচন করা হয়নি।</flux:text>@endforelse
                                </div>
                                @error("programs.$index.level")<flux:text class="mt-2 text-red-600">{{ $message }}</flux:text>@enderror
                                @error("programs.$index.names")<flux:text class="mt-2 text-red-600">{{ $message }}</flux:text>@enderror
                            </div>
                        @endforeach
                    </div>
                </div>
                <flux:switch wire:model="isActive" label="সক্রিয়" />
                <div class="flex justify-end gap-2">@if($editingId)<flux:button type="button" wire:click="cancelEdit">বাতিল</flux:button>@endif<flux:button type="submit" variant="primary">{{ $editingId ? 'আপডেট' : 'সংরক্ষণ' }}</flux:button></div>
            </form>
        </flux:card>
        <flux:card class="overflow-hidden">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"><div><flux:heading size="lg">কলেজ তালিকা</flux:heading><flux:text>মোট {{ $colleges->total() }}টি কলেজ</flux:text></div><flux:input wire:model.live.debounce.300ms="search" type="search" icon="magnifying-glass" placeholder="নাম বা কোড..." /></div>
            <div class="mt-4 overflow-x-auto"><flux:table><flux:table.columns><flux:table.column>কলেজ</flux:table.column><flux:table.column>অবস্থান</flux:table.column><flux:table.column>ধরন ও প্রোগ্রাম</flux:table.column><flux:table.column>ল্যাব</flux:table.column><flux:table.column>অধ্যক্ষ</flux:table.column><flux:table.column></flux:table.column></flux:table.columns><flux:table.rows>
                @forelse($colleges as $college)<flux:table.row wire:key="college-{{ $college->id }}"><flux:table.cell><p class="font-medium">{{ $college->name }}</p><flux:text>{{ $college->code ?: 'কোড নেই' }} · {{ $college->teachers_count }} জন শিক্ষক</flux:text></flux:table.cell><flux:table.cell>{{ $college->thana?->name }}, {{ $college->district?->name }}, {{ $college->division?->name }}<flux:text>{{ $college->address }}</flux:text></flux:table.cell><flux:table.cell>{{ ['government'=>'সরকারি','non_government'=>'বেসরকারি','other'=>'অন্যান্য'][$college->college_type] ?? 'অনির্ধারিত' }}<flux:text>{{ $college->programs->map(fn($program) => $program->level.': '.collect($program->items ?: [$program->name])->implode(', '))->implode(' · ') ?: 'প্রোগ্রাম নেই' }}</flux:text></flux:table.cell><flux:table.cell>@if($college->has_computer_lab)<span class="font-medium text-green-700 dark:text-green-400">ল্যাব আছে</span><flux:text>ডেস্কটপ {{ $college->desktop_count ?? 0 }} · ল্যাপটপ {{ $college->laptop_count ?? 0 }}</flux:text>@elseif($college->has_computer_lab === false)<span class="text-zinc-500">ল্যাব নেই</span>@else<span class="text-zinc-500">অনির্ধারিত</span>@endif</flux:table.cell><flux:table.cell>{{ $college->principal_name ?: 'অনির্ধারিত' }}</flux:table.cell><flux:table.cell><div class="flex justify-end gap-2"><flux:button size="sm" wire:click="edit({{ $college->id }})">সম্পাদনা</flux:button><flux:button size="sm" variant="danger" wire:click="delete({{ $college->id }})" wire:confirm="কলেজটি মুছবেন?">মুছুন</flux:button></div></flux:table.cell></flux:table.row>
                @empty<flux:table.row><flux:table.cell colspan="6" class="py-8 text-center">কোনো কলেজ নেই।</flux:table.cell></flux:table.row>@endforelse
            </flux:table.rows></flux:table></div><div class="mt-4">{{ $colleges->links() }}</div>
        </flux:card>
    </div>
</div>
