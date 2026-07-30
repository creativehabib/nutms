<div class="w-full mx-auto py-6 sm:px-6 lg:px-8"
     x-data="{ showImportModal: false, showEditModal: false }"
     @close-modal.window="showImportModal = false"
     @open-edit-modal.window="showEditModal = true"
     @close-edit-modal.window="showEditModal = false"
     @teacher-selection-updated.window="$el.querySelectorAll('[data-teacher-checkbox]').forEach((checkbox) => checkbox.checked = $event.detail.selected)">

    <div class="bg-white dark:bg-slate-900 shadow-md rounded-lg overflow-hidden">

        <!-- টপবার: সার্চ, ফিল্টার এবং ইম্পোর্ট বাটন -->
        <div class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/80 p-4 sm:p-5">
            <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">শিক্ষক ব্যবস্থাপনা</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">সার্চ ও ফিল্টার ব্যবহার করে প্রয়োজনীয় শিক্ষক খুঁজুন।</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex w-fit items-center rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-1 text-xs font-semibold text-slate-600 dark:text-slate-400 shadow-sm">
                        মোট {{ $teachers->total() }} জন শিক্ষক
                    </span>
                    <span class="inline-flex w-fit items-center rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 shadow-sm">
                        মোট {{ $collegeCount }}টি কলেজ
                    </span>
                </div>
            </div>

            <div class="grid gap-3 lg:grid-cols-[minmax(16rem,1.25fr)_repeat(3,minmax(10rem,0.75fr))_auto] lg:items-end">

                <!-- সার্চ ইনপুট -->
                <div>
                    <label for="teacher-search" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">শিক্ষক খুঁজুন</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 size-5 -translate-y-1/2 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"></path></svg>
                        <input
                            id="teacher-search"
                            type="search"
                            wire:model.live.debounce.300ms="search"
                            placeholder="নাম, TMIS ID বা মোবাইল নম্বর"
                            class="block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 py-2.5 pl-10 pr-3 text-sm text-slate-900 dark:text-slate-100 shadow-sm transition placeholder:text-slate-400 dark:placeholder:text-slate-500 hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        >
                    </div>
                </div>

                <!-- ফিল্টার অপশনস -->
                    <!-- সাবজেক্ট ফিল্টার -->
                    <div>
                        <label for="subject-filter" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">বিষয়</label>
                        <select id="subject-filter" wire:model.live="subjectFilter" class="block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-700 dark:text-slate-300 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                            <option value="">সব বিষয়</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject }}">{{ $subject }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- কলেজ কোড ফিল্টার -->
                    <div>
                        <label for="college-filter" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">কলেজ কোড</label>
                        <select id="college-filter" wire:model.live="collegeCodeFilter" class="block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-700 dark:text-slate-300 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                            <option value="">সব কলেজ কোড</option>
                            @foreach($collegeCodes as $code)
                                <option value="{{ $code }}">{{ $code }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- ল্যাব ফিল্টার -->
                    <div>
                        <label for="lab-filter" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">ল্যাব অবস্থা</label>
                        <select id="lab-filter" wire:model.live="labFilter" class="block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-700 dark:text-slate-300 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                            <option value="">সব অবস্থা</option>
                            <option value="Yes">ল্যাব আছে</option>
                            <option value="No">ল্যাব নেই</option>
                        </select>
                    </div>

                <!-- ইম্পোর্ট বাটন -->
                <div class="flex flex-col gap-2 sm:flex-row">
                    <button
                        type="button"
                        wire:click="toggleTrashed"
                        class="inline-flex w-full items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 shadow-sm transition hover:bg-slate-100 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 lg:w-auto"
                    >
                        {{ $showTrashed ? 'সক্রিয় তথ্য' : 'ট্র্যাশ' }}
                    </button>
                    <button
                        @click="showImportModal = true"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-transparent bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 lg:w-auto"
                    >
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16V4m0 0L7 9m5-5 5 5M5 20h14"></path></svg>
                        ডেটা ইম্পোর্ট
                    </button>
                </div>
            </div>
        </div>

        <!-- ডেটা টেবিল -->
        @if (count($selectedTeacherIds) > 0)
            <div class="flex flex-col gap-3 border-b border-indigo-100 bg-indigo-50 px-4 py-3 dark:border-indigo-900 dark:bg-indigo-950/40 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                <p class="text-sm font-semibold text-indigo-900 dark:text-indigo-200">
                    {{ count($selectedTeacherIds) }} জন শিক্ষক নির্বাচিত
                </p>
                @if ($showTrashed)
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <flux:button variant="primary" size="sm" wire:click="restoreSelectedTeachers">
                            নির্বাচিত তথ্য পুনরুদ্ধার করুন
                        </flux:button>
                        <flux:button variant="danger" size="sm" wire:click="confirmBulkPermanentDeletion">
                            স্থায়ীভাবে মুছুন
                        </flux:button>
                    </div>
                @else
                    <flux:button variant="danger" size="sm" wire:click="confirmBulkTeacherDeletion">
                        নির্বাচিত তথ্য মুছুন
                    </flux:button>
                @endif
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-800 dark:bg-slate-950 text-white">
                <tr>
                    <th class="w-12 px-4 py-3 text-center">
                        <input
                            type="checkbox"
                            wire:click="toggleSelectAllOnPage"
                            data-teacher-checkbox
                            class="size-4 rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500"
                            aria-label="এই পৃষ্ঠার সব শিক্ষক নির্বাচন করুন"
                            @checked($selectAllOnPage)
                        >
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">TMIS ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">কলেজ কোড</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">শিক্ষকের নাম</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">পদবী ও বিষয়</th>
                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider">ল্যাব ও কম্পিউটার</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">যোগাযোগ</th>
                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider">অ্যাকশন</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-200 text-sm">
                @forelse ($teachers as $teacher)
                    <tr wire:key="teacher-row-{{ $teacher->id }}" class="hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                        <td class="px-4 py-4 text-center">
                            <input
                                type="checkbox"
                                wire:key="teacher-select-{{ $teacher->id }}"
                                wire:click="toggleTeacherSelection({{ $teacher->id }})"
                                value="{{ $teacher->id }}"
                                data-teacher-checkbox
                                class="size-4 rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500"
                                aria-label="{{ $teacher->name }} নির্বাচন করুন"
                                @checked(in_array((string) $teacher->id, $selectedTeacherIds, true))
                            >
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900 dark:text-slate-100">
                            {{ $teacher->tmis_id ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-slate-300">
                            {{ $teacher->college_code ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-slate-100">
                            {{ $teacher->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="block text-gray-800 dark:text-slate-200 font-semibold">{{ $teacher->designation }}</span>
                            <span class="block text-gray-500 dark:text-slate-400 text-xs">{{ $teacher->subject }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($teacher->has_computer_lab === 'Yes')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-950/60 dark:text-green-300">
                                    ল্যাব আছে
                                </span>
                                <span class="block text-gray-500 dark:text-slate-400 text-xs mt-1">কম্পিউটার: {{ $teacher->computer_count ?? 0 }}টি</span>
                            @elseif($teacher->has_computer_lab === 'No')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-300">
                                    ল্যাব নেই
                                </span>
                            @else
                                <span class="text-gray-400 dark:text-slate-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="block text-gray-800 dark:text-slate-200">{{ $teacher->mobile_number ?? '-' }}</span>
                            <span class="block text-blue-600 text-xs">{{ $teacher->email ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            @if ($showTrashed)
                                <button wire:click="restoreTeacher({{ $teacher->id }})" class="mr-2 rounded bg-emerald-100 px-3 py-1 text-emerald-700 transition hover:bg-emerald-200 hover:text-emerald-900 dark:bg-emerald-950/60 dark:text-emerald-300 dark:hover:bg-emerald-900">Restore</button>
                                <button wire:click="confirmPermanentTeacherDeletion({{ $teacher->id }})" class="rounded bg-red-100 px-3 py-1 text-red-700 transition hover:bg-red-200 hover:text-red-900 dark:bg-red-950/60 dark:text-red-300 dark:hover:bg-red-900">Permanent Delete</button>
                            @else
                                <button wire:click="editTeacher({{ $teacher->id }})" class="mr-2 rounded bg-indigo-100 px-3 py-1 text-indigo-600 transition hover:bg-indigo-200 hover:text-indigo-900 dark:bg-indigo-950/60 dark:text-indigo-300 dark:hover:bg-indigo-900">
                                    Edit
                                </button>
                                <button wire:click="confirmTeacherDeletion({{ $teacher->id }})" class="rounded bg-red-100 px-3 py-1 text-red-600 transition hover:bg-red-200 hover:text-red-900 dark:bg-red-950/60 dark:text-red-300 dark:hover:bg-red-900">Delete</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500 dark:text-slate-400">
                            কোনো ডেটা পাওয়া যায়নি!
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <!-- পেজিনেশন -->
            <div class="px-4 py-4 bg-white dark:bg-slate-900 border-t border-gray-200 dark:border-slate-700 sm:px-6">
                {{ $teachers->links() }}
            </div>
        </div>
    </div>

        <!-- ইম্পোর্ট মডাল (Alpine.js) -->
        <div
            x-show="showImportModal"
            style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto p-3 sm:p-6"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex min-h-full items-end justify-center sm:items-center">
                <!-- Background overlay -->
                <div
                    x-show="showImportModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-950/55 backdrop-blur-sm"
                    @click="showImportModal = false"
                    aria-hidden="true"
                ></div>

                <!-- Modal panel -->
                <div
                    x-show="showImportModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative w-full max-w-xl transform overflow-hidden rounded-2xl border border-white/60 bg-white dark:bg-slate-900 text-left shadow-2xl transition-all"
                >
                    <!-- Close Button -->
                    <button type="button" @click="showImportModal = false" class="absolute right-4 top-4 z-10 inline-flex size-9 items-center justify-center rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 shadow-sm transition hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-700 dark:hover:text-slate-300" aria-label="ইম্পোর্ট ফরম বন্ধ করুন">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>

                    <div class="bg-white dark:bg-slate-900">
                        <!-- এখানে আগের তৈরি করা ইম্পোর্ট কম্পোনেন্ট কল করা হয়েছে -->
                        <livewire:teacher-data-import />
                    </div>
                </div>
            </div>
        </div>

        <flux:modal name="confirm-teacher-deletion" focusable class="max-w-lg">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">
                        {{ $permanentDeletion ? 'শিক্ষকের তথ্য স্থায়ীভাবে মুছে ফেলবেন?' : 'শিক্ষকের তথ্য ট্র্যাশে পাঠাবেন?' }}
                    </flux:heading>
                    <flux:subheading class="mt-2">
                        @if ($permanentDeletion)
                            <strong>{{ $deletingTeacherName }}</strong>-এর তথ্য স্থায়ীভাবে মুছে যাবে। এই কাজটি আর ফিরিয়ে নেওয়া যাবে না।
                        @else
                            <strong>{{ $deletingTeacherName }}</strong>-এর তথ্য ট্র্যাশে যাবে এবং পরে পুনরুদ্ধার করা যাবে।
                        @endif
                    </flux:subheading>
                </div>

                <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <flux:modal.close>
                        <flux:button variant="filled" wire:click="cancelTeacherDeletion" class="w-full sm:w-auto">বাতিল</flux:button>
                    </flux:modal.close>

                    <flux:button variant="danger" wire:click="deleteTeacher" wire:loading.attr="disabled" wire:target="deleteTeacher" class="w-full sm:w-auto">
                        <span wire:loading.remove wire:target="deleteTeacher">{{ $permanentDeletion ? 'হ্যাঁ, স্থায়ীভাবে মুছুন' : 'হ্যাঁ, ট্র্যাশে পাঠান' }}</span>
                        <span wire:loading wire:target="deleteTeacher">মুছে ফেলা হচ্ছে...</span>
                    </flux:button>
                </div>
            </div>
        </flux:modal>

        <!-- এডিট মডাল (Edit Modal) -->
        <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto p-3 sm:p-6" aria-labelledby="modal-title" role="dialog" aria-modal="true" @keydown.escape.window="showEditModal = false">
            <div class="flex min-h-full items-end justify-center sm:items-center">
                <!-- Background Overlay -->
                <div
                    x-show="showEditModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-950/55 backdrop-blur-sm"
                    @click="showEditModal = false"
                    aria-hidden="true"
                ></div>

                <!-- Modal Panel -->
                <div
                    x-show="showEditModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="translate-y-6 opacity-0 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="translate-y-0 opacity-100 sm:scale-100"
                    x-transition:leave-end="translate-y-6 opacity-0 sm:translate-y-0 sm:scale-95"
                    class="relative flex max-h-[calc(100vh-1.5rem)] w-full max-w-5xl transform flex-col overflow-hidden rounded-2xl border border-white/60 bg-slate-50 dark:bg-slate-950 text-left shadow-2xl transition-all sm:max-h-[calc(100vh-3rem)]"
                >

                    <div class="relative border-b border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-5 py-4 sm:px-7 sm:py-5">
                        <!-- Close Button -->
                        <button type="button" @click="showEditModal = false" class="absolute right-4 top-4 inline-flex size-9 items-center justify-center rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 shadow-sm transition hover:border-slate-300 dark:hover:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-700 dark:hover:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" aria-label="এডিট ফরম বন্ধ করুন">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>

                        <div class="pr-12">
                            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Teacher profile</p>
                            <h3 class="mt-1 text-xl font-bold text-slate-900 dark:text-slate-100 sm:text-2xl" id="modal-title">শিক্ষকের তথ্য আপডেট করুন</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">প্রয়োজনীয় তথ্য পরিবর্তন করে নিচের আপডেট বাটনে ক্লিক করুন।</p>
                        </div>
                    </div>

                        <!-- এডিট ফর্ম -->
                        <form wire:submit.prevent="updateTeacher" class="flex min-h-0 flex-1 flex-col">
                            <div class="min-h-0 flex-1 space-y-5 overflow-y-auto px-4 py-5 sm:px-7 sm:py-6">

                            @if ($errors->any())
                                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 dark:border-red-900 dark:bg-red-950/50 dark:text-red-200" role="alert">
                                    <div class="flex items-start gap-3">
                                        <svg class="mt-0.5 size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.3 3.7 2.6 17a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z"></path></svg>
                                        <div>
                                            <p class="text-sm font-semibold">তথ্য আপডেট করা যায়নি</p>
                                            <p class="mt-0.5 text-xs text-red-700 dark:text-red-300">লাল রঙে দেখানো তথ্যগুলো ঠিক করে আবার চেষ্টা করুন।</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <fieldset class="space-y-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 shadow-sm sm:p-5">
                                <legend class="px-2 text-sm font-semibold text-slate-900 dark:text-slate-100">কলেজ ও আইডি তথ্য</legend>
                                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">কলেজ কোড</label>
                                        <input type="text" wire:model="editForm.college_code" class="mt-1.5 block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                        @error('editForm.college_code') <span class="mt-1.5 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="lg:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">কলেজের নাম</label>
                                        <input type="text" wire:model="editForm.college_name" class="mt-1.5 block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                        @error('editForm.college_name') <span class="mt-1.5 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">TMIS ID</label>
                                        <input type="text" wire:model="editForm.tmis_id" class="mt-1.5 block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                        @error('editForm.tmis_id') <span class="mt-1.5 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">TTIS ID</label>
                                        <input type="text" wire:model="editForm.ttis_id" class="mt-1.5 block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                        @error('editForm.ttis_id') <span class="mt-1.5 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="space-y-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 shadow-sm sm:p-5">
                                <legend class="px-2 text-sm font-semibold text-slate-900 dark:text-slate-100">শিক্ষকের তথ্য</legend>
                                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    <div class="lg:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">শিক্ষকের নাম</label>
                                        <input type="text" wire:model="editForm.name" class="mt-1.5 block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                        @error('editForm.name') <span class="mt-1.5 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">পদবী</label>
                                        <select wire:model="editForm.designation" class="mt-1.5 block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 shadow-sm"><option value="">নির্বাচন করুন</option>@foreach($designations as $designation)<option value="{{ $designation }}">{{ $designation }}</option>@endforeach</select>
                                        @error('editForm.designation') <span class="mt-1.5 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">বিষয়</label>
                                        <select wire:model="editForm.subject" class="mt-1.5 block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 shadow-sm"><option value="">নির্বাচন করুন</option>@foreach($subjects as $subject)<option value="{{ $subject }}">{{ $subject }}</option>@endforeach</select>
                                        @error('editForm.subject') <span class="mt-1.5 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">শিক্ষক স্তর</label>
                                        <select wire:model="editForm.teacher_level" class="mt-1.5 block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 shadow-sm"><option value="">নির্বাচন করুন</option>@foreach($teacherLevels as $level)<option value="{{ $level }}">{{ $level }}</option>@endforeach</select>
                                        @error('editForm.teacher_level') <span class="mt-1.5 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">চাকরির ধরন</label>
                                        <select wire:model="editForm.employment_type" class="mt-1.5 block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 shadow-sm"><option value="">নির্বাচন করুন</option>@foreach($employments as $employment)<option value="{{ $employment }}">{{ $employment }}</option>@endforeach</select>
                                        @error('editForm.employment_type') <span class="mt-1.5 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="space-y-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 shadow-sm sm:p-5">
                                <div class="flex items-center justify-between gap-3">
                                    <legend class="px-2 text-sm font-semibold text-slate-900 dark:text-slate-100">প্রতিষ্ঠানভিত্তিক ট্রেনিং ইতিহাস</legend>
                                    <flux:button type="button" size="sm" wire:click="addTrainingEntry">আরেকটি ট্রেনিং যোগ করুন</flux:button>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400">প্রতিষ্ঠান নির্বাচন করলে শুধু সেই প্রতিষ্ঠানের ট্রেনিং দেখা যাবে। সময়কাল ক্যাটালগ থেকে স্বয়ংক্রিয়ভাবে প্রযোজ্য হবে; এখানে শিক্ষক যে বছরে ট্রেনিং সম্পন্ন করেছেন সেটি দিন।</p>
                                <div class="grid gap-3">
                                    @foreach ($trainingEntries as $index => $entry)
                                        <div wire:key="teacher-training-{{ $index }}" class="grid gap-3 rounded-lg border border-slate-200 p-3 dark:border-slate-700 sm:grid-cols-[1fr_1.5fr_8rem_auto] sm:items-end">
                                            <div><label class="block text-sm font-medium">প্রতিষ্ঠান</label><select wire:model.live="trainingEntries.{{ $index }}.training_institute_id" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900"><option value="">নির্বাচন করুন</option>@foreach ($trainingInstitutes as $institute)<option value="{{ $institute->id }}">{{ $institute->name }}</option>@endforeach</select></div>
                                            <div><label class="block text-sm font-medium">ট্রেনিং টাইপ</label><select wire:model="trainingEntries.{{ $index }}.training_type_id" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900"><option value="">নির্বাচন করুন</option>@foreach ($trainingTypes->where('training_institute_id', (int) $entry['training_institute_id']) as $trainingType)<option value="{{ $trainingType->id }}">{{ $trainingType->name }} — {{ $trainingType->duration_value }} {{ ['hours' => 'ঘণ্টা', 'days' => 'দিন', 'weeks' => 'সপ্তাহ', 'months' => 'মাস'][$trainingType->duration_unit] ?? '' }}</option>@endforeach</select>@error("trainingEntries.$index.training_type_id")<span class="mt-1 block text-xs text-red-600">{{ $message }}</span>@enderror</div>
                                            <div><label class="block text-sm font-medium">সম্পন্নের বছর</label><input type="number" min="1950" max="{{ date('Y') + 1 }}" wire:model="trainingEntries.{{ $index }}.training_year" placeholder="{{ date('Y') }}" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900">@error("trainingEntries.$index.training_year")<span class="mt-1 block text-xs text-red-600">{{ $message }}</span>@enderror</div>
                                            <flux:button type="button" size="sm" variant="danger" wire:click="removeTrainingEntry({{ $index }})">বাদ দিন</flux:button>
                                        </div>
                                    @endforeach
                                </div>
                            </fieldset>

                            <fieldset class="space-y-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 shadow-sm sm:p-5">
                                <legend class="px-2 text-sm font-semibold text-slate-900 dark:text-slate-100">ল্যাব ও যোগাযোগ</legend>
                                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">কম্পিউটার ল্যাব</label>
                                    <select wire:model="editForm.has_computer_lab" class="mt-1.5 block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                        <option value="">নির্বাচন করুন</option>
                                        <option value="Yes">আছে</option>
                                        <option value="No">নেই</option>
                                    </select>
                                    @error('editForm.has_computer_lab') <span class="mt-1.5 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">কম্পিউটার সংখ্যা</label>
                                    <input type="number" min="0" wire:model="editForm.computer_count" class="mt-1.5 block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                    @error('editForm.computer_count') <span class="mt-1.5 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">মোবাইল নম্বর</label>
                                    <input type="text" wire:model="editForm.mobile_number" class="mt-1.5 block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                    @error('editForm.mobile_number') <span class="mt-1.5 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">ইমেইল</label>
                                    <input type="email" wire:model="editForm.email" class="mt-1.5 block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                    @error('editForm.email') <span class="mt-1.5 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                </div>
                                </div>
                            </fieldset>

                            </div>

                            <div class="flex flex-col-reverse gap-3 border-t border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-4 py-4 sm:flex-row sm:justify-end sm:px-7">
                                <button type="button" @click="showEditModal = false" class="inline-flex w-full justify-center rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 shadow-sm transition hover:bg-slate-50 dark:hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 sm:w-auto">
                                    বাতিল
                                </button>
                                <button type="submit" class="inline-flex w-full justify-center rounded-lg border border-transparent bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto" wire:loading.attr="disabled" wire:target="updateTeacher">
                                    <span wire:loading wire:target="updateTeacher" class="mr-2">সেভ হচ্ছে...</span>
                                    আপডেট করুন
                                </button>
                            </div>
                        </form>
                </div>
            </div>
        </div>
</div>
