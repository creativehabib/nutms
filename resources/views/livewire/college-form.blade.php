<div class="mx-auto w-full max-w-6xl p-4 sm:p-6 lg:p-8">

    <!-- Page Header & Action -->
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                {{ $editingId ? __('Edit College') : __('Create College') }}
            </flux:heading>
            <flux:subheading class="mt-1 text-zinc-500">{{ __('College Profile') }}</flux:subheading>
        </div>
        <flux:button variant="subtle" :href="route('colleges.manage')" icon="arrow-left" wire:navigate class="shrink-0">{{ __('Back') }}</flux:button>
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
                    <flux:heading size="lg" class="font-semibold">{{ __('Basic Information') }}</flux:heading>
                </div>
            </div>

            <div class="p-6">
                <div class="grid gap-6 sm:grid-cols-2">
                    <flux:input wire:model="college_code"  :label="__('College Code')" :placeholder="__('Enter college code')" />
                    <flux:input wire:model="name"  :label="__('College Name')" :placeholder="__('Enter college name')" required />
                    <flux:input wire:model="principalName" :label="__('Principal Name')" :placeholder="__('Enter principal name')" required />
                    <flux:input wire:model="collegeEmail" type="email" :label="__('College Email')" :placeholder="__('college@example.com')" />
                    <flux:input wire:model="collegeWebsite" type="url" :label="__('College Website')" :placeholder="__('https://example.edu')" />
                    <flux:select wire:model="collegeType" :label="__('College Type')" required>
                        <option value="">{{ __('Select...') }}</option>
                        <option value="government">{{ __('Government') }}</option>
                        <option value="non_government">{{ __('Non-government') }}</option>
                        <option value="other">{{ __('Other') }}</option>
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
                    <flux:heading size="lg" class="font-semibold">{{ __('Location Details') }}</flux:heading>
                </div>
            </div>

            <div class="p-6">
                <div class="grid gap-6">
                    <div class="grid gap-6 sm:grid-cols-3">
                        <flux:select wire:model.live="divisionId" :label="__('Division')" required>
                            <option value="">{{ __('Select') }}</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}">{{ $division->name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:select wire:model.live="districtId" :label="__('District')" required>
                            <option value="">{{ __('Select') }}</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}">{{ $district->name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:select wire:model="thanaId" :label="__('Thana / Upazila')" required>
                            <option value="">{{ __('Select') }}</option>
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

                    <flux:textarea wire:model="address" :label="__('Full Address')" rows="2"  :placeholder="__('Enter full address')" required />
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
                    <flux:heading size="lg" class="font-semibold">{{ __('Programs and Courses') }}</flux:heading>
                </div>
                <flux:button type="button" size="sm" variant="subtle" icon="plus" wire:click="addProgram" class="text-amber-700 hover:bg-amber-50 dark:text-amber-400 dark:hover:bg-amber-500/10">{{ __('Add Program') }}</flux:button>
            </div>

            <div class="p-6 bg-zinc-50/30 dark:bg-zinc-900/20">
                <div class="space-y-5">
                    @foreach($programs as $index => $program)
                        <div wire:key="college-program-{{ $index }}" class="group relative rounded-xl border border-zinc-200 bg-white p-5 shadow-sm transition-all focus-within:border-indigo-400 focus-within:ring-2 focus-within:ring-indigo-500/20 dark:border-zinc-700 dark:bg-zinc-900">

                            <!-- Remove Button -->
                            <div class="absolute right-3 top-3 opacity-0 transition-opacity group-hover:opacity-100 focus-within:opacity-100">
                                <button type="button" wire:click="removeProgram({{ $index }})" class="rounded-md p-1.5 text-zinc-400 hover:bg-red-50 hover:text-red-500 transition-colors" :title="__('Remove item')">
                                    <flux:icon.trash variant="micro" class="size-4" />
                                </button>
                            </div>

                            <div class="grid gap-5 pr-8 sm:grid-cols-[10rem_minmax(0,1fr)] sm:items-start">
                                <flux:select wire:model.live="programs.{{ $index }}.level" :label="__('Program Level')">
                                    @foreach($programLevels as $programLevel)
                                        <option wire:key="program-level-{{ $programLevel->id }}" value="{{ $programLevel->slug }}">{{ $programLevel->name }}</option>
                                    @endforeach
                                </flux:select>

                                <div class="flex flex-col gap-1.5">
                                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                        {{ $program['level'] === 'degree' ? __('Degree Courses') : __('Subjects / Programs') }}
                                    </label>

                                    <!-- Elevated Pillbox Input -->
                                    <div class="flex min-h-[42px] w-full flex-wrap items-center gap-1.5 rounded-lg border border-zinc-300 bg-white px-2.5 py-1.5 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-900">
                                        @foreach($program['names'] as $tagIndex => $programName)
                                            <span wire:key="program-tag-{{ $index }}-{{ $tagIndex }}" class="inline-flex items-center gap-1 rounded-md bg-zinc-100 px-2 py-1 text-xs font-medium text-zinc-800 border border-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-700 shadow-sm">
                                                {{ $programName }}
                                                <button type="button" wire:click="removeProgramTag({{ $index }}, {{ $tagIndex }})" class="group -mr-1 rounded hover:bg-red-50 dark:hover:bg-red-900/30"  :aria-label="__('Remove program name')">
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
                                             :placeholder="__('Type a program name and press Enter')"
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
                    <flux:heading size="lg" class="font-semibold">{{ __('Computer Lab Details') }}</flux:heading>
                </div>
            </div>

            <div class="p-6">
                <div class="grid gap-6 sm:grid-cols-2">
                    <flux:select wire:model.live="hasComputerLab" :label="__('Has Computer Lab?')" required>
                        <option value="">{{ __('Select') }}</option>
                        <option value="1">{{ __('Yes, has a computer lab') }}</option>
                        <option value="0">{{ __('No computer lab') }}</option>
                    </flux:select>

                    @if ($hasComputerLab === '1')
                        <flux:select wire:model.live="labEquipmentType" :label="__('Lab Equipment Type')" required>
                            <option value="">{{ __('Select') }}</option>
                            <option value="desktop">{{ __('Desktop only') }}</option>
                            <option value="laptop">{{ __('Laptop only') }}</option>
                            <option value="both">{{ __('Both desktop and laptop') }}</option>
                        </flux:select>
                    @endif
                </div>

                @if ($hasComputerLab === '1')
                    <div class="mt-6 grid gap-6 sm:grid-cols-2 rounded-xl border border-dashed border-cyan-200 bg-cyan-50/30 p-5 dark:border-cyan-900/30 dark:bg-cyan-900/10">
                        @if (in_array($labEquipmentType, ['desktop', 'both'], true))
                            <flux:input wire:model="desktopCount" type="number" min="1" :label="__('Desktop Count')" :placeholder="__('Enter desktop count')" required />
                        @endif

                        @if (in_array($labEquipmentType, ['laptop', 'both'], true))
                            <flux:input wire:model="laptopCount" type="number" min="1" :label="__('Laptop Count')" :placeholder="__('Enter laptop count')" required />
                        @endif
                    </div>
                @endif
            </div>
        </flux:card>

        <!-- Fixed Action Bar (Sticky at the bottom of the screen) -->
        <div class="fixed inset-x-0 bottom-0 z-50 border-t border-zinc-200 bg-white/80 p-4 backdrop-blur-md dark:border-zinc-800 dark:bg-zinc-950/80">
            <div class="mx-auto flex w-full max-w-4xl items-center justify-between">
                <div class="flex items-center">
                    <flux:switch wire:model="isActive" :label="__('Keep college active')" />
                </div>

                <div class="flex items-center gap-3">
                    <flux:button :href="route('colleges.manage')" wire:navigate class="hidden sm:flex">{{ __('Cancel') }}</flux:button>
                    <flux:button type="submit" variant="primary" icon="check-circle" class="shadow-sm">
                        {{ $editingId ? __('Update College') : __('Create College') }}
                    </flux:button>
                </div>
            </div>
        </div>

    </form>
</div>
