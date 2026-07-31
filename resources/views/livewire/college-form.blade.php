<div class="mx-auto w-full max-w-4xl space-y-6 p-4 sm:p-6">
    <div class="flex items-start justify-between gap-4"><div><flux:heading size="xl">{{ $editingId ? 'কলেজ সম্পাদনা' : 'নতুন কলেজ তৈরি' }}</flux:heading><flux:text>কলেজের পরিচিতি, অবস্থান, প্রোগ্রাম ও ল্যাবের তথ্য পূরণ করুন।</flux:text></div><flux:button :href="route('colleges.manage')" icon="arrow-left" wire:navigate>তালিকায় ফিরুন</flux:button></div>
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
                                        <div data-program-pillbox class="flex min-h-11 w-full flex-wrap items-center gap-1.5 rounded-lg border border-zinc-300 bg-white px-2.5 py-2 shadow-sm transition focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 dark:border-zinc-600 dark:bg-zinc-900">
                                            @foreach($program['names'] as $tagIndex => $programName)
                                                <flux:badge wire:key="program-tag-{{ $index }}-{{ $tagIndex }}" color="indigo" class="gap-1 py-1">{{ $programName }}<button type="button" wire:click="removeProgramTag({{ $index }}, {{ $tagIndex }})" class="rounded-full px-1 hover:bg-indigo-200 dark:hover:bg-indigo-900" aria-label="{{ $programName }} বাদ দিন">×</button></flux:badge>
                                            @endforeach
                                            <input class="min-w-32 flex-1 border-0 bg-transparent px-1 py-0.5 text-sm text-zinc-900 outline-none placeholder:text-zinc-400 focus:ring-0 dark:text-zinc-100" wire:model="programs.{{ $index }}.new_name" wire:keydown.enter.prevent.stop="addProgramTag({{ $index }})" list="program-suggestions-{{ $index }}" autocomplete="off" placeholder="{{ $program['names'] === [] ? ($program['level'] === 'degree' ? 'BA লিখে Enter চাপুন' : 'বাংলা লিখে Enter চাপুন') : 'আরও যোগ করুন...' }}" aria-label="কোর্স অথবা বিষয় লিখে Enter চাপুন">
                                        </div>
                                        <flux:description>লিখে Enter চাপুন</flux:description>
                                        <datalist id="program-suggestions-{{ $index }}">@foreach($program['level'] === 'degree' ? $degreeCourseSuggestions : $subjectSuggestions as $suggestion)<option value="{{ $suggestion }}"></option>@endforeach</datalist>
                                    </flux:field>
                                    <flux:button type="button" size="sm" variant="danger" wire:click="removeProgram({{ $index }})">গ্রুপ বাদ</flux:button>
                                </div>
                                @error("programs.$index.level")<flux:text class="mt-2 text-red-600">{{ $message }}</flux:text>@enderror
                                @error("programs.$index.names")<flux:text class="mt-2 text-red-600">{{ $message }}</flux:text>@enderror
                            </div>
                        @endforeach
                    </div>
                </div>
                <flux:switch wire:model="isActive" label="সক্রিয়" />
                <div class="flex justify-end gap-2"><flux:button :href="route('colleges.manage')" wire:navigate>বাতিল</flux:button><flux:button type="submit" variant="primary">{{ $editingId ? 'আপডেট করুন' : 'কলেজ তৈরি করুন' }}</flux:button></div>
            </form>
        </flux:card>
</div>
