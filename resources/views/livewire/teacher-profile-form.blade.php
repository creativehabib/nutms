<div x-data="{
        activeTab: 'basic',
        tabs: ['basic', 'professional', 'contact', 'training', 'bank'],
        goToNext() {
            let currentIndex = this.tabs.indexOf(this.activeTab);
            if (currentIndex < this.tabs.length - 1) this.activeTab = this.tabs[currentIndex + 1];
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        goToPrev() {
            let currentIndex = this.tabs.indexOf(this.activeTab);
            if (currentIndex > 0) this.activeTab = this.tabs[currentIndex - 1];
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }"
     class="mx-auto w-full max-w-6xl p-4 sm:p-6 lg:p-8 space-y-6"
>

    <!-- Page Header & Action -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                {{ $editingId ? 'শিক্ষক প্রোফাইল সম্পাদনা' : 'নতুন শিক্ষক তৈরি করুন' }}
            </flux:heading>
            <flux:subheading class="mt-1 text-zinc-500">
                পেশাগত, যোগাযোগ ও ব্যাংক তথ্য সংরক্ষণ করুন। নিচের ট্যাবগুলো ব্যবহার করে নেভিগেট করুন।
            </flux:subheading>
        </div>
        <flux:button variant="subtle" :href="auth()->user()->role === \App\Enums\UserRole::Teacher ? route('dashboard') : route('teachers.manage')" icon="arrow-left" wire:navigate class="shrink-0">
            ফিরে যান
        </flux:button>
    </div>

    <!-- Tab Navigation (Horizontal) -->
    <div class="border-b border-zinc-200 dark:border-zinc-800">
        <nav class="-mb-px flex space-x-6 overflow-x-auto scrollbar-hide" aria-label="Tabs">
            <!-- Basic Info Tab -->
            <button @click="activeTab = 'basic'" :class="activeTab === 'basic' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:border-zinc-300 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300'" class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors flex items-center gap-2">
                <flux:icon.building-office-2 class="size-4" />
                পরিচিতি
            </button>

            <!-- Professional Tab -->
            <button @click="activeTab = 'professional'" :class="activeTab === 'professional' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:border-zinc-300 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300'" class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors flex items-center gap-2">
                <flux:icon.briefcase class="size-4" />
                পেশাগত তথ্য
            </button>

            <!-- Contact Tab -->
            <button @click="activeTab = 'contact'" :class="activeTab === 'contact' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:border-zinc-300 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300'" class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors flex items-center gap-2">
                <flux:icon.map-pin class="size-4" />
                যোগাযোগ
            </button>

            <!-- Training Tab -->
            <button @click="activeTab = 'training'" :class="activeTab === 'training' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:border-zinc-300 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300'" class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors flex items-center gap-2">
                <flux:icon.academic-cap class="size-4" />
                ট্রেনিং ইতিহাস
            </button>

            <!-- Bank Tab -->
            <button @click="activeTab = 'bank'" :class="activeTab === 'bank' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:border-zinc-300 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300'" class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors flex items-center gap-2">
                <flux:icon.banknotes class="size-4" />
                ব্যাংক তথ্য
            </button>
        </nav>
    </div>

    <!-- Main Form -->
    <form wire:submit="save" class="relative pb-24">

        <!-- Tab 1: Institution & Basic Info -->
        <div x-show="activeTab === 'basic'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
            <flux:card class="shadow-sm p-6 sm:p-8 border-t-4 border-t-indigo-500">
                <div class="mb-6">
                    <flux:heading size="lg">প্রতিষ্ঠান ও পরিচিতি</flux:heading>
                    <flux:text class="text-sm">শিক্ষকের প্রাথমিক তথ্য এবং কলেজের অন্তর্ভুক্তি নিশ্চিত করুন।</flux:text>
                </div>
                <div class="grid gap-6 sm:grid-cols-2">
                    <flux:select wire:model="collegeId" label="কলেজ" required>
                        <option value="">কলেজ নির্বাচন করুন</option>
                        @foreach($colleges as $college)
                            <option value="{{ $college->id }}">{{ $college->code ? $college->code.' — ' : '' }}{{ $college->name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:input wire:model="name" label="শিক্ষকের নাম" placeholder="সম্পূর্ণ নাম লিখুন" required />

                    <flux:input wire:model="birthDate" type="date" label="জন্ম তারিখ" />
                    <flux:input wire:model="tmisId" label="TMIS ID" placeholder="যেমন: 12345" />

                    <flux:field class="sm:col-span-2">
                        <flux:label>TTIS ID</flux:label>
                        <flux:input wire:model="ttisId" readonly placeholder="সংরক্ষণের পর ৬ সংখ্যার ইউনিক নাম্বার তৈরি হবে" class="bg-zinc-50 text-zinc-500 dark:bg-zinc-900/50" />
                        <flux:description>Teachers Training Information System ID সিস্টেম থেকে স্বয়ংক্রিয়ভাবে নির্ধারিত হবে।</flux:description>
                    </flux:field>
                </div>

                @if(! $editingId && auth()->user()->role !== \App\Enums\UserRole::Teacher)
                    <div class="mt-8 rounded-xl border border-indigo-100 bg-indigo-50/60 p-5 dark:border-indigo-900 dark:bg-indigo-950/30">
                        <flux:heading size="md">লগইন অ্যাকাউন্ট</flux:heading>
                        <flux:text class="mt-1 text-sm">শুধু ইমেইল ও পাসওয়ার্ড দিয়ে শিক্ষকের অ্যাকাউন্ট তৈরি হবে। শিক্ষক পরে লগইন করে বাকি তথ্য পূরণ করতে পারবেন।</flux:text>
                        <div class="mt-5 grid gap-6 sm:grid-cols-2">
                            <flux:input wire:model="accountEmail" type="email" label="লগইন ইমেইল" placeholder="teacher@example.com" required />
                            <flux:input wire:model="accountPassword" type="password" label="অস্থায়ী পাসওয়ার্ড" autocomplete="new-password" required />
                            <flux:input wire:model="accountPassword_confirmation" type="password" label="পাসওয়ার্ড নিশ্চিত করুন" autocomplete="new-password" required />
                        </div>
                    </div>
                @endif
            </flux:card>
        </div>

        <!-- Tab 2: Professional Information -->
        <div x-show="activeTab === 'professional'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
            <flux:card class="shadow-sm p-6 sm:p-8 border-t-4 border-t-indigo-500">
                <div class="mb-6">
                    <flux:heading size="lg">পেশাগত তথ্য</flux:heading>
                    <flux:text class="text-sm">শিক্ষকের বর্তমান পদবি, বিষয় এবং চাকরির ধরন।</flux:text>
                </div>
                <div class="grid gap-6 sm:grid-cols-2">
                    <flux:select wire:model="designation" label="পদবি">
                        <option value="">নির্বাচন করুন</option>
                        @foreach($designations as $item)
                            <option>{{ $item }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="subject" label="বিষয়">
                        <option value="">নির্বাচন করুন</option>
                        @foreach($subjects as $item)
                            <option>{{ $item }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="teacherLevel" label="শিক্ষক স্তর">
                        <option value="">নির্বাচন করুন</option>
                        @foreach($teacherLevels as $item)
                            <option>{{ $item }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="employmentType" label="চাকরির ধরন">
                        <option value="">নির্বাচন করুন</option>
                        @foreach($employments as $item)
                            <option>{{ $item }}</option>
                        @endforeach
                    </flux:select>
                </div>
            </flux:card>
        </div>

        <!-- Tab 3: Contact & Address -->
        <div x-show="activeTab === 'contact'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
            <flux:card class="shadow-sm p-6 sm:p-8 border-t-4 border-t-indigo-500">
                <div class="mb-6">
                    <flux:heading size="lg">যোগাযোগ ও ঠিকানা</flux:heading>
                    <flux:text class="text-sm">শিক্ষকের স্থায়ী, বর্তমান ঠিকানা এবং যোগাযোগের মাধ্যম।</flux:text>
                </div>
                <div class="grid gap-6">
                    <div class="grid gap-6 sm:grid-cols-3 bg-zinc-50/50 p-4 rounded-xl border border-zinc-100 dark:bg-zinc-900/30 dark:border-zinc-800">
                        <flux:select wire:model.live="divisionId" label="বিভাগ" required>
                            <option value="">নির্বাচন করুন</option>
                            @foreach($divisions as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:select wire:model.live="districtId" label="জেলা" required>
                            <option value="">নির্বাচন করুন</option>
                            @foreach($districts as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:select wire:model="thanaId" label="উপজেলা / থানা" required>
                            <option value="">নির্বাচন করুন</option>
                            @foreach($thanas as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:textarea wire:model="presentAddress" label="বর্তমান ঠিকানা" rows="2" placeholder="বিস্তারিত লিখুন..." required />
                        <flux:textarea wire:model="permanentAddress" label="স্থায়ী ঠিকানা" rows="2" placeholder="বিস্তারিত লিখুন..." required />
                        <flux:input wire:model="mobileNumber" label="মোবাইল নম্বর" placeholder="যেমন: 017XXXXXXXX" required />

                        <flux:field>
                            <flux:label>ইমেইল ঠিকানা</flux:label>
                            <flux:input wire:model="email" type="email" placeholder="example@gmail.com" :readonly="auth()->user()->role === \App\Enums\UserRole::Teacher" />
                            <flux:error name="email" />
                        </flux:field>
                    </div>
                </div>
            </flux:card>
        </div>

        <!-- Tab 4: Training History -->
        <div x-show="activeTab === 'training'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
            <flux:card class="shadow-sm p-6 sm:p-8 border-t-4 border-t-indigo-500">
                <div class="mb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                    <div>
                        <flux:heading size="lg">প্রতিষ্ঠানভিত্তিক ট্রেনিং ইতিহাস</flux:heading>
                        <flux:text class="text-sm">নির্ধারিত অথবা অন্যান্য একাধিক ট্রেনিং যোগ করুন।</flux:text>
                    </div>
                    <flux:button type="button" size="sm" variant="outline" icon="plus" wire:click="addTrainingEntry" class="text-indigo-600 hover:bg-indigo-50 dark:text-indigo-400 dark:hover:bg-indigo-500/10">
                        নতুন ট্রেনিং যোগ করুন
                    </flux:button>
                </div>

                <div class="space-y-6">
                    @foreach($trainingEntries as $index => $entry)
                        <div wire:key="profile-training-{{ $index }}" class="group relative rounded-xl border border-zinc-200 bg-zinc-50/30 p-5 shadow-sm transition-all focus-within:border-indigo-400 focus-within:ring-2 focus-within:ring-indigo-500/20 dark:border-zinc-700 dark:bg-zinc-900/30">

                            <!-- Remove Button -->
                            <div class="absolute right-3 top-3 opacity-0 transition-opacity group-hover:opacity-100 focus-within:opacity-100">
                                <button type="button" wire:click="removeTrainingEntry({{ $index }})" class="rounded-md p-1.5 text-zinc-400 hover:bg-red-50 hover:text-red-500 transition-colors" title="এই ট্রেনিংটি মুছে ফেলুন">
                                    <flux:icon.trash variant="micro" class="size-4" />
                                </button>
                            </div>

                            <div class="grid gap-5 pr-8 sm:grid-cols-2 lg:grid-cols-4">
                                <flux:select wire:model.live="trainingEntries.{{ $index }}.kind" label="ট্রেনিং ধরন">
                                    <option value="catalog">নির্ধারিত (Catalog)</option>
                                    <option value="other">অন্যান্য (Custom)</option>
                                </flux:select>

                                <flux:select wire:model.live="trainingEntries.{{ $index }}.training_institute_id" label="ট্রেনিং প্রতিষ্ঠান">
                                    <option value="">{{ $entry['kind'] === 'other' ? 'তালিকার বাইরে (Type Below)' : 'নির্বাচন করুন' }}</option>
                                    @foreach($trainingInstitutes as $institute)
                                        <option value="{{ $institute->id }}">{{ $institute->name }}</option>
                                    @endforeach
                                </flux:select>

                                @if($entry['kind'] === 'catalog')
                                    <flux:select wire:model="trainingEntries.{{ $index }}.training_type_id" label="ট্রেনিংয়ের নাম" class="lg:col-span-2">
                                        <option value="">নির্বাচন করুন</option>
                                        @foreach($trainingTypes->where('training_institute_id', (int) $entry['training_institute_id']) as $training)
                                            <option value="{{ $training->id }}">{{ $training->name }} ({{ $training->duration_value }} {{ ['hours'=>'ঘণ্টা','days'=>'দিন','weeks'=>'সপ্তাহ','months'=>'মাস'][$training->duration_unit] ?? '' }})</option>
                                        @endforeach
                                    </flux:select>
                                @else
                                    <div class="space-y-5 lg:col-span-2">
                                        <flux:input wire:model="trainingEntries.{{ $index }}.name" label="অন্যান্য ট্রেনিংয়ের নাম" placeholder="ট্রেনিংয়ের নাম লিখুন" />

                                        @if(blank($entry['training_institute_id']))
                                            <flux:input wire:model="trainingEntries.{{ $index }}.institute_name" label="প্রতিষ্ঠানের নাম" placeholder="যেখান থেকে ট্রেনিং করেছেন" />
                                        @endif

                                        <div class="grid grid-cols-2 gap-4">
                                            <flux:input wire:model="trainingEntries.{{ $index }}.duration_value" type="number" min="1" label="সময়কাল" placeholder="যেমন: 14" />
                                            <flux:select wire:model="trainingEntries.{{ $index }}.duration_unit" label="একক">
                                                <option value="hours">ঘণ্টা</option>
                                                <option value="days">দিন</option>
                                                <option value="weeks">সপ্তাহ</option>
                                                <option value="months">মাস</option>
                                            </flux:select>
                                        </div>
                                    </div>
                                @endif

                                <div class="lg:col-span-4 mt-2 border-t border-dashed border-zinc-200 pt-4 dark:border-zinc-700">
                                    <flux:input wire:model="trainingEntries.{{ $index }}.training_year" type="number" min="1950" max="{{ date('Y') + 1 }}" label="সম্পন্নের বছর" placeholder="যেমন: 2024" class="max-w-[200px]" />
                                </div>
                            </div>

                            @error("trainingEntries.$index.training_type_id")<span class="mt-2 text-xs text-red-600 block">{{ $message }}</span>@enderror
                            @error("trainingEntries.$index.name")<span class="mt-2 text-xs text-red-600 block">{{ $message }}</span>@enderror
                        </div>
                    @endforeach

                    @if(count($trainingEntries) === 0)
                        <div class="rounded-xl border border-dashed border-zinc-300 bg-zinc-50/50 p-8 text-center dark:border-zinc-700 dark:bg-zinc-900">
                            <flux:icon.academic-cap class="mx-auto h-8 w-8 text-zinc-400" />
                            <h3 class="mt-2 text-sm font-semibold text-zinc-900 dark:text-zinc-100">কোনো ট্রেনিং যুক্ত করা হয়নি</h3>
                            <p class="mt-1 text-sm text-zinc-500">আপনার যদি কোনো পেশাগত ট্রেনিং থাকে, তবে উপরের বাটনে ক্লিক করে যুক্ত করুন।</p>
                        </div>
                    @endif
                </div>
            </flux:card>
        </div>

        <!-- Tab 5: Bank Information -->
        <div x-show="activeTab === 'bank'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
            <flux:card class="shadow-sm p-6 sm:p-8 border-t-4 border-t-indigo-500">
                <div class="mb-6">
                    <flux:heading size="lg">ব্যাংক তথ্য</flux:heading>
                    <flux:text class="text-sm">বেতন ও অন্যান্য আর্থিক লেনদেনের জন্য সঠিক ব্যাংক তথ্য প্রদান করুন।</flux:text>
                </div>
                <div class="grid gap-6 sm:grid-cols-2">
                    <flux:input wire:model="bankName" label="ব্যাংকের নাম" placeholder="যেমন: Sonali Bank PLC" />
                    <flux:input wire:model="bankBranchName" label="শাখার নাম" placeholder="যেমন: Dhanmondi Branch" />
                    <flux:input wire:model="bankAccountNumber" label="ব্যাংক অ্যাকাউন্ট নম্বর" autocomplete="off" placeholder="অ্যাকাউন্ট নম্বর লিখুন" />
                    <flux:input wire:model="bankRoutingNumber" label="রাউটিং নম্বর" inputmode="numeric" placeholder="৯ ডিজিটের রাউটিং নম্বর" />
                </div>
            </flux:card>
        </div>

        <!-- Fixed Bottom Action Bar -->
        <div class="fixed inset-x-0 bottom-0 z-50 border-t border-zinc-200 bg-white/90 p-4 backdrop-blur-md dark:border-zinc-800 dark:bg-zinc-950/90 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
            <div class="mx-auto flex w-full max-w-4xl items-center justify-between gap-3">

                <!-- Prev/Next Navigation (Handled by Alpine.js) -->
                <div class="flex items-center gap-2">
                    <flux:button type="button" variant="outline" icon="chevron-left" @click="goToPrev()" x-show="activeTab !== 'basic'">
                        পূর্ববর্তী
                    </flux:button>
                    <flux:button type="button" variant="outline" icon-trailing="chevron-right" @click="goToNext()" x-show="activeTab !== 'bank'">
                        পরবর্তী ধাপ
                    </flux:button>
                </div>

                <!-- Save Button (Always visible) -->
                <flux:button type="submit" variant="primary" icon="check-circle" class="shadow-sm">
                    {{ $editingId ? 'পরিবর্তন সেভ করুন' : (auth()->user()->role === \App\Enums\UserRole::Teacher ? 'প্রোফাইল তৈরি করুন' : 'অ্যাকাউন্ট তৈরি করুন') }}
                </flux:button>
            </div>
        </div>

    </form>
</div>
