<div class="mx-auto w-full max-w-6xl p-4 sm:p-6 lg:p-8">

    <!-- Page Header & Action -->
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                {{ $editingId ? 'কলেজ প্রোফাইল সম্পাদনা' : 'নতুন কলেজ তৈরি করুন' }}
            </flux:heading>
            <flux:subheading class="mt-1 text-zinc-500">
                নিচের কার্ডগুলোতে ধাপে ধাপে কলেজের সঠিক তথ্যগুলো পূরণ করুন।
            </flux:subheading>
        </div>
        <flux:button variant="subtle" :href="route('colleges.manage')" icon="arrow-left" wire:navigate class="shrink-0">
            ফিরে যান
        </flux:button>
    </div>

    <!-- Main Form Wrapper -->
    <form wire:submit="save" class="space-y-8 pb-24">

        <!-- Card 1: Basic Information -->
        <flux:card class="overflow-hidden p-0 shadow-sm transition-shadow hover:shadow-md">
            <div class="border-b border-zinc-100 bg-zinc-50/80 px-6 py-4 dark:border-zinc-800 dark:bg-zinc-900/50">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400">
                        <flux:icon.building-office-2 class="size-4" />
                    </div>
                    <flux:heading size="lg" class="font-semibold">প্রাথমিক তথ্য</flux:heading>
                </div>
            </div>

            <div class="p-6">
                <div class="grid gap-6 sm:grid-cols-2">
                    <flux:input wire:model="code" label="কলেজ কোড (EIIN/NU)" placeholder="যেমন: 1234" />
                    <flux:input wire:model="name" label="কলেজের নাম" placeholder="কলেজের সম্পূর্ণ নাম লিখুন" required />
                    <flux:input wire:model="principalName" label="অধ্যক্ষের নাম" placeholder="অধ্যক্ষের নাম লিখুন" required />
                    <flux:select wire:model="collegeType" label="কলেজের ধরন" required>
                        <option value="">নির্বাচন করুন...</option>
                        <option value="government">সরকারি</option>
                        <option value="non_government">বেসরকারি</option>
                        <option value="other">অন্যান্য</option>
                    </flux:select>
                </div>
            </div>
        </flux:card>

        <!-- Card 2: Location -->
        <flux:card class="overflow-hidden p-0 shadow-sm transition-shadow hover:shadow-md">
            <div class="border-b border-zinc-100 bg-zinc-50/80 px-6 py-4 dark:border-zinc-800 dark:bg-zinc-900/50">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400">
                        <flux:icon.map-pin class="size-4" />
                    </div>
                    <flux:heading size="lg" class="font-semibold">অবস্থান ও ঠিকানা</flux:heading>
                </div>
            </div>

            <div class="p-6">
                <div class="grid gap-6">
                    <div class="grid gap-6 sm:grid-cols-3">
                        <flux:select wire:model.live="divisionId" label="বিভাগ" required>
                            <option value="">নির্বাচন করুন</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}">{{ $division->name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:select wire:model.live="districtId" label="জেলা" required>
                            <option value="">নির্বাচন করুন</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}">{{ $district->name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:select wire:model="thanaId" label="উপজেলা / থানা" required>
                            <option value="">নির্বাচন করুন</option>
                            @foreach($thanas as $thana)
                                <option value="{{ $thana->id }}">{{ $thana->name }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div class="flex flex-col gap-1 -mt-3">
                        @error('divisionId')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
                        @error('districtId')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
                        @error('thanaId')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
                    </div>

                    <flux:textarea wire:model="address" label="পূর্ণ ঠিকানা" rows="2" placeholder="রাস্তা, এলাকা বা ল্যান্ডমার্কসহ ঠিকানা লিখুন..." required />
                </div>
            </div>
        </flux:card>

        <!-- Card 3: Programs and Courses -->
        <flux:card class="overflow-hidden p-0 shadow-sm transition-shadow hover:shadow-md border-amber-200/50 dark:border-amber-900/30">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-zinc-100 bg-zinc-50/80 px-6 py-4 dark:border-zinc-800 dark:bg-zinc-900/50">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-amber-100 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400">
                        <flux:icon.academic-cap class="size-4" />
                    </div>
                    <flux:heading size="lg" class="font-semibold">প্রোগ্রাম ও কোর্সসমূহ</flux:heading>
                </div>
                <flux:button type="button" size="sm" variant="subtle" icon="plus" wire:click="addProgram" class="text-amber-700 hover:bg-amber-50 dark:text-amber-400 dark:hover:bg-amber-500/10">
                    নতুন লেভেল
                </flux:button>
            </div>

            <div class="p-6 bg-zinc-50/30 dark:bg-zinc-900/20">
                <div class="space-y-5">
                    @foreach($programs as $index => $program)
                        <div wire:key="college-program-{{ $index }}" class="group relative rounded-xl border border-zinc-200 bg-white p-5 shadow-sm transition-all focus-within:border-indigo-400 focus-within:ring-2 focus-within:ring-indigo-500/20 dark:border-zinc-700 dark:bg-zinc-900">

                            <!-- Remove Button -->
                            <div class="absolute right-3 top-3 opacity-0 transition-opacity group-hover:opacity-100 focus-within:opacity-100">
                                <button type="button" wire:click="removeProgram({{ $index }})" class="rounded-md p-1.5 text-zinc-400 hover:bg-red-50 hover:text-red-500 transition-colors" title="এই গ্রুপটি মুছে ফেলুন">
                                    <flux:icon.trash variant="micro" class="size-4" />
                                </button>
                            </div>

                            <div class="grid gap-5 pr-8 sm:grid-cols-[10rem_minmax(0,1fr)] sm:items-start">
                                <flux:select wire:model.live="programs.{{ $index }}.level" label="কলেজ লেভেল">
                                    <option value="degree">ডিগ্রি</option>
                                    <option value="honours">অনার্স</option>
                                    <option value="masters">মাস্টার্স</option>
                                    <option value="professional">প্রফেশনাল</option>
                                    <option value="other">অন্যান্য</option>
                                </flux:select>

                                <div class="flex flex-col gap-1.5">
                                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                        {{ $program['level'] === 'degree' ? 'ডিগ্রি কোর্সের নাম' : 'পঠিত বিষয়ের নাম' }}
                                    </label>

                                    <!-- Elevated Pillbox Input -->
                                    <div class="flex min-h-[42px] w-full flex-wrap items-center gap-1.5 rounded-lg border border-zinc-300 bg-white px-2.5 py-1.5 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-900">
                                        @foreach($program['names'] as $tagIndex => $programName)
                                            <span wire:key="program-tag-{{ $index }}-{{ $tagIndex }}" class="inline-flex items-center gap-1 rounded-md bg-zinc-100 px-2 py-1 text-xs font-medium text-zinc-800 border border-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-700 shadow-sm">
                                                {{ $programName }}
                                                <button type="button" wire:click="removeProgramTag({{ $index }}, {{ $tagIndex }})" class="group -mr-1 rounded hover:bg-red-50 dark:hover:bg-red-900/30" aria-label="বাদ দিন">
                                                    <flux:icon.x-mark variant="micro" class="text-zinc-500 group-hover:text-red-500 dark:group-hover:text-red-400" />
                                                </button>
                                            </span>
                                        @endforeach

                                        <input
                                            class="min-w-[140px] flex-1 border-0 bg-transparent px-1 py-0.5 text-sm text-zinc-900 outline-none placeholder:text-zinc-400 focus:ring-0 dark:text-zinc-100"
                                            wire:model="programs.{{ $index }}.new_name"
                                            wire:keydown.enter.prevent.stop="addProgramTag({{ $index }})"
                                            list="program-suggestions-{{ $index }}"
                                            autocomplete="off"
                                            placeholder="{{ $program['names'] === [] ? 'নাম লিখে Enter চাপুন' : 'আরও যোগ করুন...' }}"
                                        >
                                    </div>
                                    <datalist id="program-suggestions-{{ $index }}">
                                        @foreach($program['level'] === 'degree' ? $degreeCourseSuggestions : $subjectSuggestions as $suggestion)
                                            <option value="{{ $suggestion }}"></option>
                                        @endforeach
                                    </datalist>
                                    @error("programs.$index.names")<span class="text-xs text-red-600">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            @error("programs.$index.level")<span class="mt-2 text-xs text-red-600 block">{{ $message }}</span>@enderror
                        </div>
                    @endforeach
                </div>
            </div>
        </flux:card>

        <!-- Card 4: Computer Lab -->
        <flux:card class="overflow-hidden p-0 shadow-sm transition-shadow hover:shadow-md">
            <div class="border-b border-zinc-100 bg-zinc-50/80 px-6 py-4 dark:border-zinc-800 dark:bg-zinc-900/50">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-cyan-100 text-cyan-600 dark:bg-cyan-500/20 dark:text-cyan-400">
                        <flux:icon.computer-desktop class="size-4" />
                    </div>
                    <flux:heading size="lg" class="font-semibold">ল্যাব ও প্রযুক্তি</flux:heading>
                </div>
            </div>

            <div class="p-6">
                <div class="grid gap-6 sm:grid-cols-2">
                    <flux:select wire:model.live="hasComputerLab" label="কম্পিউটার ল্যাব আছে?" required>
                        <option value="">নির্বাচন করুন</option>
                        <option value="1">হ্যাঁ, ল্যাব আছে</option>
                        <option value="0">না, ল্যাব নেই</option>
                    </flux:select>

                    @if ($hasComputerLab === '1')
                        <flux:select wire:model.live="labEquipmentType" label="ল্যাবে কী ধরনের ডিভাইস আছে?" required>
                            <option value="">নির্বাচন করুন</option>
                            <option value="desktop">শুধু ডেস্কটপ</option>
                            <option value="laptop">শুধু ল্যাপটপ</option>
                            <option value="both">ডেস্কটপ ও ল্যাপটপ উভয়ই</option>
                        </flux:select>
                    @endif
                </div>

                @if ($hasComputerLab === '1')
                    <div class="mt-6 grid gap-6 sm:grid-cols-2 rounded-xl border border-dashed border-cyan-200 bg-cyan-50/30 p-5 dark:border-cyan-900/30 dark:bg-cyan-900/10">
                        @if (in_array($labEquipmentType, ['desktop', 'both'], true))
                            <flux:input wire:model="desktopCount" type="number" min="1" label="ডেস্কটপ কম্পিউটারের সংখ্যা" placeholder="যেমন: 20" required />
                        @endif

                        @if (in_array($labEquipmentType, ['laptop', 'both'], true))
                            <flux:input wire:model="laptopCount" type="number" min="1" label="ল্যাপটপের সংখ্যা" placeholder="যেমন: 10" required />
                        @endif
                    </div>
                @endif
            </div>
        </flux:card>

        <!-- Fixed Action Bar (Sticky at the bottom of the screen) -->
        <div class="fixed inset-x-0 bottom-0 z-50 border-t border-zinc-200 bg-white/80 p-4 backdrop-blur-md dark:border-zinc-800 dark:bg-zinc-950/80">
            <div class="mx-auto flex w-full max-w-4xl items-center justify-between">
                <div class="flex items-center">
                    <flux:switch wire:model="isActive" label="কলেজটি সক্রিয় রাখুন" />
                </div>

                <div class="flex items-center gap-3">
                    <flux:button :href="route('colleges.manage')" wire:navigate class="hidden sm:flex">
                        বাতিল
                    </flux:button>
                    <flux:button type="submit" variant="primary" icon="check-circle" class="shadow-sm">
                        {{ $editingId ? 'পরিবর্তন সেভ করুন' : 'কলেজ তৈরি করুন' }}
                    </flux:button>
                </div>
            </div>
        </div>

    </form>
</div>
