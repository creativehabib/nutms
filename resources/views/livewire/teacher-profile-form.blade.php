<div class="mx-auto w-full max-w-6xl space-y-6 p-4 sm:p-6 lg:p-8">

    <!-- Page Header & Action -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                {{ $editingId ? __('Edit Teacher Profile') : __('Create Teacher Profile') }}
            </flux:heading>
            <flux:subheading class="mt-1 text-zinc-500">{{ __('Enter accurate personal, professional, contact, training, and bank details.') }}</flux:subheading>
        </div>
        <flux:button variant="subtle" :href="auth()->user()->hasRole('teacher') ? route('dashboard') : route('teachers.manage')" icon="arrow-left" wire:navigate class="shrink-0">{{ __('Back') }}</flux:button>
    </div>

    <!-- Tab Navigation (Horizontal) -->
    <div class="border-b border-zinc-200 dark:border-zinc-800">
        <nav class="-mb-px flex space-x-6 overflow-x-auto scrollbar-hide" aria-label="Tabs">
            <button type="button" wire:click="goToStep('basic')" @class(['flex items-center gap-2 whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors', 'border-indigo-500 text-indigo-600 dark:text-indigo-400' => $activeStep === 'basic', 'border-transparent text-zinc-500 hover:border-zinc-300 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' => $activeStep !== 'basic'])>
                <flux:icon.building-office-2 class="size-4" />{{ __('Basic Details') }}</button>

            <button type="button" wire:click="goToStep('professional')" @class(['flex items-center gap-2 whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors', 'border-indigo-500 text-indigo-600 dark:text-indigo-400' => $activeStep === 'professional', 'border-transparent text-zinc-500 hover:border-zinc-300 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' => $activeStep !== 'professional'])>
                <flux:icon.briefcase class="size-4" />{{ __('Professional Details') }}</button>

            <button type="button" wire:click="goToStep('contact')" @class(['flex items-center gap-2 whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors', 'border-indigo-500 text-indigo-600 dark:text-indigo-400' => $activeStep === 'contact', 'border-transparent text-zinc-500 hover:border-zinc-300 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' => $activeStep !== 'contact'])>
                <flux:icon.map-pin class="size-4" />{{ __('Contact & Address') }}</button>

            <button type="button" wire:click="goToStep('training')" @class(['flex items-center gap-2 whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors', 'border-indigo-500 text-indigo-600 dark:text-indigo-400' => $activeStep === 'training', 'border-transparent text-zinc-500 hover:border-zinc-300 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' => $activeStep !== 'training'])>
                <flux:icon.academic-cap class="size-4" />{{ __('Training History') }}</button>

            <button type="button" wire:click="goToStep('bank')" @class(['flex items-center gap-2 whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors', 'border-indigo-500 text-indigo-600 dark:text-indigo-400' => $activeStep === 'bank', 'border-transparent text-zinc-500 hover:border-zinc-300 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' => $activeStep !== 'bank'])>
                <flux:icon.banknotes class="size-4" />{{ __('Bank Details') }}</button>
        </nav>
    </div>

    @if($submissionError)
    <flux:callout variant="danger" :heading="__('প্রয়োজনীয় তথ্য পাওয়া যায়নি')">
        {{ __('যে ধাপে প্রয়োজনীয় তথ্য অসম্পূর্ণ আছে সেখানে আপনাকে ফিরিয়ে নেওয়া হয়েছে। লাল রঙে দেখানো তথ্যগুলো পূরণ করে আবার চেষ্টা করুন।') }}
    </flux:callout>
    @endif

    <!-- Main Form -->
    <form wire:submit="submit" class="relative pb-24">

        <!-- Tab 1: Institution & Basic Info -->
        @if($activeStep === 'basic')
        <div>
            <flux:card class="shadow-sm p-6 sm:p-8 border-t-4 border-t-indigo-500">
                <div class="mb-6">
                    <flux:heading size="lg">{{ __('Basic Teacher Details') }}</flux:heading>
                    <flux:text class="text-sm">{{ __('Select the college and enter the teacher identity details.') }}</flux:text>
                </div>
                <div class="grid gap-6 sm:grid-cols-2">
                    <flux:select wire:model="collegeId" :label="__('College')" required>
                        <option value="">{{ __('Select a college') }}</option>
                        @foreach($colleges as $college)
                            <option value="{{ $college->id }}">{{ $college->code ? $college->code.' — ' : '' }}{{ $college->name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:input wire:model="name"  :label="__('Teacher Name')" :placeholder="__('Enter teacher name')" required />

                    <flux:input wire:model="birthDate" type="date"  :label="__('Date of Birth')" />

                    <flux:field >
                        <flux:label>TTIS ID</flux:label>
                        <flux:input wire:model="ttisId" readonly :placeholder="__('Generated after saving')" class="bg-zinc-50 text-zinc-500 dark:bg-zinc-900/50" />
                    </flux:field>

                    <div class="sm:col-span-2 grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Profile Image') }}</flux:label>
                            <div class="mb-3 flex h-24 w-24 items-center justify-center overflow-hidden rounded-full border border-zinc-200 bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800">
                                @if($profileImage)
                                    <img src="{{ $profileImage->temporaryUrl() }}" alt="{{ __('New Profile Image Preview') }}" class="h-full w-full object-cover" />
                                @elseif($currentProfileImage)
                                    <img src="{{ asset('storage/' . $currentProfileImage) }}" alt="{{ __('Current Profile Image') }}" class="h-full w-full object-cover" />
                                @else
                                    <span class="text-2xl font-semibold text-zinc-500">{{ auth()->user()->initials() }}</span>
                                @endif
                            </div>
                            <flux:input wire:model="profileImage" type="file" accept="image/*" />
                            <flux:error name="profileImage" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Digital Signature') }}</flux:label>
                            @if($digitalSignature)
                                <div class="mb-3 inline-block rounded-lg border border-zinc-200 bg-white p-3 dark:border-zinc-700">
                                    <img src="{{ $digitalSignature->temporaryUrl() }}" alt="{{ __('New Signature Preview') }}" class="h-16 object-contain" />
                                </div>
                            @elseif($currentDigitalSignature)
                                <div class="mb-3 inline-block rounded-lg border border-zinc-200 bg-white p-3 dark:border-zinc-700">
                                    <img src="{{ asset('storage/' . $currentDigitalSignature) }}" alt="{{ __('Current Signature') }}" class="h-16 object-contain" />
                                </div>
                            @endif
                            <flux:input wire:model="digitalSignature" type="file" accept="image/*" />
                            <flux:error name="digitalSignature" />
                        </flux:field>
                    </div>
                </div>

                @if(! $editingId && ! auth()->user()->isTeacher())
                    <div class="mt-8 rounded-xl border border-indigo-100 bg-indigo-50/60 p-5 dark:border-indigo-900 dark:bg-indigo-950/30">
                        <flux:heading size="md">{{ __('Teacher Login Account') }}</flux:heading>
                        <flux:text class="mt-1 text-sm">{{ __('Create login credentials for this teacher.') }}</flux:text>
                        <div class="mt-5 grid gap-6 sm:grid-cols-2">
                            <flux:input wire:model="accountEmail" type="email"  :label="__('Account Email')" placeholder="teacher@example.com" required />
                            <flux:input wire:model="accountPassword" type="password"  :label="__('Password')" autocomplete="new-password" required />
                            <flux:input wire:model="accountPassword_confirmation" type="password" :label="__('Confirm Password')" autocomplete="new-password" required />
                        </div>
                    </div>
                @endif
            </flux:card>
        </div>
        @endif

        <!-- Tab 2: Professional Information -->
        @if($activeStep === 'professional')
        <div>
            <flux:card class="shadow-sm p-6 sm:p-8 border-t-4 border-t-indigo-500">
                <div class="mb-6">
                    <flux:heading size="lg">{{ __('Professional Details') }}</flux:heading>
                    <flux:text class="text-sm">{{ __('Add designation, subject, level, and employment type.') }}</flux:text>
                </div>
                <div class="grid gap-6 sm:grid-cols-2">
                    <flux:select wire:model="designation" :label="__('Designation')">
                        <option value="">{{ __('Select') }}</option>
                        @foreach($designations as $item)
                            <option>{{ $item }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="subject" :label="__('Subject')">
                        <option value="">{{ __('Select') }}</option>
                        @foreach($subjects as $item)
                            <option>{{ $item }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="teacherLevel" :label="__('Teacher Level')">
                        <option value="">{{ __('Select') }}</option>
                        @foreach($teacherLevels as $item)
                            <option>{{ $item }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="employmentType" :label="__('Employment Type')">
                        <option value="">{{ __('Select') }}</option>
                        @foreach($employments as $item)
                            <option>{{ $item }}</option>
                        @endforeach
                    </flux:select>
                </div>
            </flux:card>
        </div>
        @endif

        <!-- Tab 3: Contact & Address -->
        @if($activeStep === 'contact')
        <div>
            <flux:card class="shadow-sm p-6 sm:p-8 border-t-4 border-t-indigo-500">
                <div class="mb-6">
                    <flux:heading size="lg">{{ __('Contact & Address') }}</flux:heading>
                    <flux:text class="text-sm">{{ __('Add location, address, mobile, and email details.') }}</flux:text>
                </div>
                <div class="grid gap-6">
                    <div class="grid gap-6 sm:grid-cols-3 bg-zinc-50/50 p-4 rounded-xl border border-zinc-100 dark:bg-zinc-900/30 dark:border-zinc-800">
                        <flux:select wire:model.live="divisionId" :label="__('Division')" required>
                            <option value="">{{ __('Select') }}</option>
                            @foreach($divisions as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:select wire:model.live="districtId" :label="__('District')" required>
                            <option value="">{{ __('Select') }}</option>
                            @foreach($districts as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:select wire:model="thanaId" :label="__('Thana / Upazila')" required>
                            <option value="">{{ __('Select') }}</option>
                            @foreach($thanas as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:textarea wire:model="presentAddress" :label="__('Present Address')" rows="2" :placeholder="__('Enter present address')" required />
                        <flux:textarea wire:model="permanentAddress" :label="__('Permanent Address')" rows="2" :placeholder="__('Enter permanent address')" required />

                        <flux:field>
                            <flux:label>{{ __('Mobile Number') }}</flux:label>
                            <flux:input wire:model="mobileNumber" :placeholder="__('Enter mobile number')" required
                                        :readonly="auth()->user()->hasRole('teacher')"
                                        :class="auth()->user()->hasRole('teacher') ? 'opacity-70 cursor-not-allowed' : ''" />
                            <flux:error name="mobileNumber" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Email Address') }}</flux:label>
                            <flux:input wire:model="email" type="email" placeholder="example@gmail.com"
                                        :readonly="auth()->user()->hasRole('teacher')"
                                        :class="auth()->user()->hasRole('teacher') ? 'opacity-70 cursor-not-allowed' : ''" />
                            <flux:error name="email" />
                        </flux:field>
                    </div>
                </div>
            </flux:card>
        </div>
        @endif

        <!-- Tab 4: Training History -->
        @if($activeStep === 'training')
        <div>
            <flux:card class="shadow-sm p-6 sm:p-8 border-t-4 border-t-indigo-500">
                <div class="mb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                    <div>
                        <flux:heading size="lg">{{ __('Training History') }}</flux:heading>
                        <flux:text class="text-sm">{{ __('Add ICT and professional development training records.') }}</flux:text>
                    </div>
                    <flux:button type="button" size="sm" variant="outline" icon="plus" wire:click="addTrainingEntry" class="text-indigo-600 hover:bg-indigo-50 dark:text-indigo-400 dark:hover:bg-indigo-500/10">{{ __('Add Training') }}</flux:button>
                </div>

                <div class="space-y-6">
                    @foreach($trainingEntries as $index => $entry)
                        <div wire:key="profile-training-{{ $index }}" class="group relative rounded-xl border border-zinc-200 bg-zinc-50/30 p-5 shadow-sm transition-all focus-within:border-indigo-400 focus-within:ring-2 focus-within:ring-indigo-500/20 dark:border-zinc-700 dark:bg-zinc-900/30">

                            <!-- Remove Button -->
                            <div class="absolute right-3 top-3 opacity-0 transition-opacity group-hover:opacity-100 focus-within:opacity-100">
                                <button type="button" wire:click="removeTrainingEntry({{ $index }})" class="rounded-md p-1.5 text-zinc-400 hover:bg-red-50 hover:text-red-500 transition-colors"  :title="__('Remove training entry')">
                                    <flux:icon.trash variant="micro" class="size-4" />
                                </button>
                            </div>

                            <div class="grid gap-5 pr-8 sm:grid-cols-2 lg:grid-cols-4">
                                <flux:select wire:model.live="trainingEntries.{{ $index }}.kind" :label="__('Training Source')">
                                    <option value="catalog">{{ __('Catalog Training') }}</option>
                                    <option value="other">{{ __('Custom Training') }}</option>
                                </flux:select>

                                <flux:select wire:model.live="trainingEntries.{{ $index }}.training_institute_id" :label="__('Training Institute')">
                                    <option value="">{{ $entry['kind'] === 'other' ? __('No institute selected') : __('Select') }}</option>
                                    @foreach($trainingInstitutes as $institute)
                                        <option value="{{ $institute->id }}">{{ $institute->name }}</option>
                                    @endforeach
                                </flux:select>

                                @if($entry['kind'] === 'catalog')
                                    <flux:select wire:model="trainingEntries.{{ $index }}.training_type_id" :label="__('Training Name')" class="lg:col-span-2">
                                        <option value="">{{ __('Select') }}</option>
                                        @foreach($trainingTypes->where('training_institute_id', (int) $entry['training_institute_id']) as $training)
                                            <option value="{{ $training->id }}">{{ $training->name }} ({{ $training->duration_value }} {{ ['hours'=>__('Hours'),'days'=>__('Days'),'weeks'=>__('Weeks'),'months'=>__('Months')][$training->duration_unit] ?? '' }})</option>
                                        @endforeach
                                    </flux:select>
                                @else
                                    <div class="space-y-5 lg:col-span-2">
                                        <flux:input wire:model="trainingEntries.{{ $index }}.name"  :label="__('Training Name')" :placeholder="__('Enter training name')" />

                                        @if(blank($entry['training_institute_id']))
                                            <flux:input wire:model="trainingEntries.{{ $index }}.institute_name"  :label="__('Institute Name')" :placeholder="__('Enter institute name')" />
                                        @endif

                                        <div class="grid grid-cols-2 gap-4">
                                            <flux:input wire:model="trainingEntries.{{ $index }}.duration_value" type="number" min="1"  :label="__('Duration')" :placeholder="__('Enter duration')" />
                                            <flux:select wire:model="trainingEntries.{{ $index }}.duration_unit" :label="__('Duration Unit')">
                                                <option value="hours">{{ __('Hours') }}</option>
                                                <option value="days">{{ __('Days') }}</option>
                                                <option value="weeks">{{ __('Weeks') }}</option>
                                                <option value="months">{{ __('Months') }}</option>
                                            </flux:select>
                                        </div>
                                    </div>
                                @endif

                                <div class="lg:col-span-4 mt-2 border-t border-dashed border-zinc-200 pt-4 dark:border-zinc-700">
                                    <flux:input wire:model="trainingEntries.{{ $index }}.training_year" type="number" min="1950" max="{{ date('Y') + 1 }}" :label="__('Training Year')" :placeholder="__('Enter training year')" class="max-w-[200px]" />
                                </div>
                            </div>

                            @error("trainingEntries.$index.training_type_id")<span class="mt-2 text-xs text-red-600 block">{{ $message }}</span>@enderror
                            @error("trainingEntries.$index.name")<span class="mt-2 text-xs text-red-600 block">{{ $message }}</span>@enderror
                        </div>
                    @endforeach

                    @if(count($trainingEntries) === 0)
                        <div class="rounded-xl border border-dashed border-zinc-300 bg-zinc-50/50 p-8 text-center dark:border-zinc-700 dark:bg-zinc-900">
                            <flux:icon.academic-cap class="mx-auto h-8 w-8 text-zinc-400" />
                            <h3 class="mt-2 text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ __('No training entries yet') }}</h3>
                            <p class="mt-1 text-sm text-zinc-500">{{ __('Add training records to keep this profile complete.') }}</p>
                        </div>
                    @endif
                </div>
            </flux:card>
        </div>
        @endif

        <!-- Tab 5: Bank Information -->
        @if($activeStep === 'bank')
        <div>
            <flux:card class="shadow-sm p-6 sm:p-8 border-t-4 border-t-indigo-500">
                <div class="mb-6">
                    <flux:heading size="lg">{{ __('Bank Details') }}</flux:heading>
                    <flux:text class="text-sm">{{ __('Add optional bank account details for payroll and records.') }}</flux:text>
                </div>
                <div class="grid gap-6 sm:grid-cols-2">
                    <flux:input wire:model="bankName" :label="__('Bank Name')" :placeholder="__('Enter bank name')" />
                    <flux:input wire:model="bankBranchName" :label="__('Branch Name')" :placeholder="__('Enter branch name')" />
                    <flux:input wire:model="bankAccountNumber" :label="__('Account Number')" autocomplete="off" :placeholder="__('Enter account number')" />
                    <flux:input wire:model="bankRoutingNumber" :label="__('Routing Number')" inputmode="numeric" :placeholder="__('Enter routing number')" />
                </div>
            </flux:card>
        </div>
        @endif

        <!-- Fixed Bottom Action Bar -->
        <div class="fixed inset-x-0 bottom-0 z-50 border-t border-zinc-200 bg-white/90 p-4 backdrop-blur-md dark:border-zinc-800 dark:bg-zinc-950/90 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
            <div class="mx-auto flex w-full max-w-4xl items-center justify-between gap-3">

                <!-- Server-backed step navigation -->
                <div class="flex items-center gap-2">
                    @if($activeStep !== 'basic')
                        <flux:button type="button" variant="outline" icon="chevron-left" wire:click="previousStep">{{ __('Previous') }}</flux:button>
                    @endif

                    <!-- Updated Next Button with Loading State -->
                    @if($activeStep !== 'bank')
                        <flux:button type="button" variant="outline" wire:click="nextStep" wire:loading.attr="disabled" wire:target="nextStep">
                            <span wire:loading.remove wire:target="nextStep" class="flex items-center gap-2">{{ __('Next') }} <flux:icon.chevron-right class="size-4" /></span>
                            <span wire:loading wire:target="nextStep">{{ __('Checking...') }}</span>
                        </flux:button>
                    @endif
                </div>

                <!-- Save Button (Only visible on the final step) -->
                @if($activeStep === 'bank')
                <flux:button type="submit" variant="primary" icon="check-circle" class="shadow-sm" wire:loading.attr="disabled" wire:target="submit">
                    <span wire:loading.remove wire:target="submit">{{ $editingId ? __('Update Profile') : (auth()->user()->hasRole('teacher') ? __('Submit Profile') : __('Create Teacher')) }}</span>
                    <span wire:loading wire:target="submit">{{ __('Checking all steps...') }}</span>
                </flux:button>
                @endif
            </div>
        </div>

    </form>
</div>
