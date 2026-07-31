<div class="p-4 sm:p-6">
    <!-- Main Card Container -->
    <flux:card class="p-0 sm:p-0 overflow-hidden shadow-sm">

        <!-- Header Section -->
        <div class="border-b border-zinc-200 bg-zinc-50/40 p-4 dark:border-zinc-700/50 dark:bg-zinc-900/40 sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <flux:heading size="xl" class="font-bold tracking-tight">কলেজ ব্যবস্থাপনা</flux:heading>
                    <flux:subheading class="mt-1">প্রয়োজনীয় সংক্ষিপ্ত তথ্য দেখুন; বিস্তারিত জানতে “দেখুন” ব্যবহার করুন।</flux:subheading>
                </div>

                <div class="flex-shrink-0">
                    @if(auth()->user()->isAdmin())
                        <flux:button variant="primary" icon="plus" :href="route('colleges.create')" wire:navigate class="shadow-sm">
                            নতুন কলেজ
                        </flux:button>
                    @elseif(auth()->user()->college_id)
                        <flux:button variant="primary" icon="pencil-square" :href="route('colleges.edit', auth()->user()->college_id)" wire:navigate class="shadow-sm">
                            প্রোফাইল সম্পাদনা
                        </flux:button>
                    @endif
                </div>
            </div>

            <!-- Search & Filter Section -->
            <div class="mt-6 grid gap-3 lg:grid-cols-[minmax(16rem,1.5fr)_minmax(10rem,0.75fr)_minmax(10rem,0.75fr)_auto] lg:items-end">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    icon="magnifying-glass"
                    label="কলেজ খুঁজুন"
                    placeholder="নাম, কোড, অধ্যক্ষ, ঠিকানা বা অবস্থান"
                    class="w-full shadow-sm"
                />

                <flux:select wire:model.live="collegeTypeFilter" label="কলেজের ধরন">
                    <flux:select.option value="">সব ধরন</flux:select.option>
                    <flux:select.option value="government">সরকারি</flux:select.option>
                    <flux:select.option value="non_government">বেসরকারি</flux:select.option>
                    <flux:select.option value="other">অন্যান্য</flux:select.option>
                </flux:select>

                <flux:select wire:model.live="approvalStatusFilter" label="অনুমোদন">
                    <flux:select.option value="">সব অবস্থা</flux:select.option>
                    <flux:select.option value="approved">অনুমোদিত</flux:select.option>
                    <flux:select.option value="pending">পেন্ডিং</flux:select.option>
                    <flux:select.option value="rejected">প্রত্যাখ্যাত</flux:select.option>
                </flux:select>

                <div class="flex flex-wrap items-center gap-2">
                    @if(auth()->user()->isAdmin())
                        <flux:button wire:click="toggleTrashed" :icon="$showTrashed ? 'building-office-2' : 'trash'">
                            {{ $showTrashed ? 'সক্রিয় কলেজ' : 'ট্র্যাশ' }}
                        </flux:button>
                    @endif
                    <flux:badge color="indigo" size="sm" class="font-medium shadow-sm">মোট {{ $colleges->total() }}টি কলেজ</flux:badge>
                </div>
            </div>

            @if(trim($search) !== '' || $collegeTypeFilter !== '' || $approvalStatusFilter !== '')
                <div class="mt-3 flex items-center justify-between gap-3 rounded-lg border border-indigo-100 bg-indigo-50/70 px-3 py-2 dark:border-indigo-900 dark:bg-indigo-950/30">
                    <flux:text class="text-xs text-indigo-700 dark:text-indigo-300">সক্রিয় সার্চ বা ফিল্টার অনুযায়ী ফলাফল দেখানো হচ্ছে।</flux:text>
                    <flux:button size="sm" variant="ghost" wire:click="clearFilters">সব মুছুন</flux:button>
                </div>
            @endif
        </div>

        @if(auth()->user()->isAdmin() && count($selectedCollegeIds) > 0)
            <div class="flex flex-col gap-3 border-b border-indigo-100 bg-indigo-50 px-4 py-3 dark:border-indigo-900 dark:bg-indigo-950/40 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <flux:text class="font-semibold text-indigo-900 dark:text-indigo-200">{{ count($selectedCollegeIds) }}টি কলেজ নির্বাচিত</flux:text>
                <div class="flex flex-wrap gap-2">
                    @if($showTrashed)
                        <flux:button size="sm" variant="primary" wire:click="restoreSelected">নির্বাচিত কলেজ পুনরুদ্ধার</flux:button>
                        <flux:button size="sm" variant="danger" wire:click="confirmBulkPermanentDeletion">স্থায়ীভাবে মুছুন</flux:button>
                    @else
                        <flux:button size="sm" variant="danger" wire:click="confirmBulkDeletion">নির্বাচিত কলেজ মুছুন</flux:button>
                    @endif
                </div>
            </div>
        @endif

        <!-- Table Section (px-4 added here for breathing room) -->
        <div class="overflow-x-auto px-4 pb-4 sm:px-6 pt-2">
            <flux:table>
                <flux:table.columns>
                    @if(auth()->user()->isAdmin())
                        <flux:table.column class="w-12 text-center">
                            <input type="checkbox" wire:click="toggleSelectAllOnPage" @checked($selectAllOnPage) aria-label="এই পৃষ্ঠার সব কলেজ নির্বাচন করুন" class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                        </flux:table.column>
                    @endif
                    <flux:table.column>কলেজ</flux:table.column>
                    <flux:table.column>অবস্থান</flux:table.column>
                    <flux:table.column>ধরন</flux:table.column>
                    <flux:table.column>ল্যাব</flux:table.column>
                    <flux:table.column>অনুমোদন</flux:table.column>
                    <flux:table.column>শিক্ষক</flux:table.column>
                    <flux:table.column class="text-right">অ্যাকশন</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($colleges as $college)
                        <flux:table.row wire:key="college-{{ $college->id }}" class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition-colors duration-200">

                            @if(auth()->user()->isAdmin())
                                <flux:table.cell class="text-center">
                                    <input type="checkbox" wire:model.live="selectedCollegeIds" value="{{ $college->id }}" aria-label="{{ $college->name }} নির্বাচন করুন" class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                                </flux:table.cell>
                            @endif

                            <!-- College Name & Code with Icon -->
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-zinc-200 bg-zinc-100 text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800/50">
                                        <flux:icon.building-office-2 class="size-5" />
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $college->name }}</span>
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">কোড: <span class="font-mono font-medium">{{ $college->code ?: 'N/A' }}</span></span>
                                    </div>
                                </div>
                            </flux:table.cell>

                            <!-- Location with Icon -->
                            <flux:table.cell class="whitespace-nowrap">
                                <div class="flex items-center gap-1.5 text-zinc-600 dark:text-zinc-300">
                                    <flux:icon.map-pin variant="micro" class="text-zinc-400" />
                                    <span>{{ $college->thana?->name ?: '—' }}, {{ $college->district?->name ?: '—' }}</span>
                                </div>
                            </flux:table.cell>

                            <!-- College Type -->
                            <flux:table.cell class="whitespace-nowrap">
                                <flux:badge :color="$college->college_type === 'government' ? 'emerald' : 'zinc'" size="sm">
                                    {{ ['government'=>'সরকারি','non_government'=>'বেসরকারি','other'=>'অন্যান্য'][$college->college_type] ?? 'অনির্ধারিত' }}
                                </flux:badge>
                            </flux:table.cell>

                            <!-- Computer Lab -->
                            <flux:table.cell class="whitespace-nowrap">
                                @if($college->has_computer_lab)
                                    <div class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400">
                                        <flux:icon.check-circle variant="micro" />
                                        <span class="text-sm font-medium">আছে</span>
                                    </div>
                                @elseif($college->has_computer_lab === false)
                                    <div class="flex items-center gap-1.5 text-zinc-400 dark:text-zinc-500">
                                        <flux:icon.x-circle variant="micro" />
                                        <span class="text-sm">নেই</span>
                                    </div>
                                @else
                                    <span class="text-sm text-zinc-400">অনির্ধারিত</span>
                                @endif
                            </flux:table.cell>

                            <!-- Approval Status -->
                            @php($approvalStatus = $college->approval_status)
                            <flux:table.cell class="whitespace-nowrap">
                                <flux:badge size="sm" :color="match($approvalStatus) {
                                    \App\Enums\ApprovalStatus::Approved => 'emerald',
                                    \App\Enums\ApprovalStatus::Rejected => 'rose',
                                    default => 'amber'
                                }">
                                    {{ match($approvalStatus) {
                                        \App\Enums\ApprovalStatus::Approved => 'অনুমোদিত',
                                        \App\Enums\ApprovalStatus::Rejected => 'প্রত্যাখ্যাত',
                                        default => 'পেন্ডিং'
                                    } }}
                                </flux:badge>
                            </flux:table.cell>

                            <!-- Teachers Count with Icon -->
                            <flux:table.cell class="whitespace-nowrap">
                                <div class="flex items-center gap-1.5 text-zinc-700 dark:text-zinc-300">
                                    <flux:icon.users variant="micro" class="text-zinc-400" />
                                    <span class="font-semibold">{{ $college->teachers_count }}</span> <span class="text-xs text-zinc-500">জন</span>
                                </div>
                            </flux:table.cell>

                            <!-- Actions -->
                            <flux:table.cell class="whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    @if($showTrashed)
                                        <flux:button variant="ghost" size="sm" icon="arrow-path" wire:click="restore({{ $college->id }})" title="পুনরুদ্ধার" />
                                        <flux:button variant="danger" size="sm" icon="trash" wire:click="confirmPermanentDeletion({{ $college->id }})" title="স্থায়ীভাবে মুছুন" />
                                    @else
                                        <flux:button variant="ghost" size="sm" icon="eye" :href="route('colleges.show', $college)" wire:navigate title="দেখুন" class="text-zinc-500 hover:text-indigo-600" />
                                        <flux:button variant="ghost" size="sm" icon="pencil-square" :href="route('colleges.edit', $college)" wire:navigate title="সম্পাদনা" class="text-zinc-500 hover:text-indigo-600" />

                                        @if(auth()->user()->isAdmin())
                                        <flux:dropdown position="bottom-end">
                                            <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" class="text-zinc-500" />
                                            <flux:menu>
                                                @if($college->approval_status === \App\Enums\ApprovalStatus::Pending)
                                                    <flux:menu.item icon="check-badge" wire:click="approveCollege({{ $college->id }})">
                                                        এপ্রুভ করুন
                                                    </flux:menu.item>
                                                    <flux:menu.separator />
                                                @endif
                                                <flux:menu.item icon="trash" variant="danger" wire:click="confirmDeletion({{ $college->id }})">
                                                    মুছে ফেলুন
                                                </flux:menu.item>
                                            </flux:menu>
                                        </flux:dropdown>
                                        @endif
                                    @endif
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell :colspan="auth()->user()->isAdmin() ? 8 : 7">
                                <div class="flex flex-col items-center justify-center py-16 text-zinc-500 dark:text-zinc-400">
                                    <div class="rounded-full bg-zinc-100 p-4 dark:bg-zinc-800/50 mb-3">
                                        <flux:icon.building-library class="h-8 w-8 text-zinc-400" />
                                    </div>
                                    <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">কোনো কলেজ পাওয়া যায়নি</p>
                                    <p class="text-sm mt-1">ভিন্ন কি-ওয়ার্ড দিয়ে খোঁজার চেষ্টা করুন অথবা নতুন কলেজ যুক্ত করুন।</p>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        <!-- Pagination -->
        @if($colleges->hasPages())
            <div class="border-t border-zinc-200 p-4 px-4 sm:px-6 dark:border-zinc-700/50 bg-zinc-50/30 dark:bg-zinc-900/30">
                {{ $colleges->links() }}
            </div>
        @endif

    </flux:card>

    <flux:modal name="confirm-college-deletion" focusable class="max-w-md">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $permanentDeletion ? 'কলেজ স্থায়ীভাবে মুছে ফেলবেন?' : 'কলেজ ট্র্যাশে পাঠাবেন?' }}</flux:heading>
                <flux:text class="mt-2">
                    <span class="font-semibold text-zinc-900 dark:text-white">{{ $deletingCollegeName }}</span>
                    {{ $permanentDeletion ? ' স্থায়ীভাবে মুছে যাবে এবং এটি আর পুনরুদ্ধার করা যাবে না।' : ' ট্র্যাশে যাবে এবং পরে পুনরুদ্ধার করা যাবে।' }}
                </flux:text>
            </div>
            <div class="flex justify-end gap-2">
                <flux:modal.close><flux:button wire:click="cancelDeletion">বাতিল</flux:button></flux:modal.close>
                <flux:button variant="danger" wire:click="deleteConfirmed" wire:loading.attr="disabled" wire:target="deleteConfirmed">
                    <span wire:loading.remove wire:target="deleteConfirmed">{{ $permanentDeletion ? 'স্থায়ীভাবে মুছুন' : 'ট্র্যাশে পাঠান' }}</span>
                    <span wire:loading wire:target="deleteConfirmed">মুছে ফেলা হচ্ছে...</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
