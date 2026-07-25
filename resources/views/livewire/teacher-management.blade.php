<div class="w-full mx-auto py-6 sm:px-6 lg:px-8"
     x-data="{ showImportModal: false, showEditModal: false }"
     @close-modal.window="showImportModal = false"
     @open-edit-modal.window="showEditModal = true"
     @close-edit-modal.window="showEditModal = false">

    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow-sm">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">

        <!-- টপবার: সার্চ, ফিল্টার এবং ইম্পোর্ট বাটন -->
        <div class="p-4 bg-gray-50 border-b">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">

                <!-- সার্চ ইনপুট -->
                <div class="w-full lg:w-1/4">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="খুঁজুন (নাম, TMIS ID, মোবাইল)..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                    >
                </div>

                <!-- ফিল্টার অপশনস -->
                <div class="flex flex-col sm:flex-row w-full lg:w-2/4 gap-2">
                    <!-- সাবজেক্ট ফিল্টার -->
                    <select wire:model.live="subjectFilter" class="w-full sm:w-1/3 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">সব বিষয়</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject }}">{{ $subject }}</option>
                        @endforeach
                    </select>

                    <!-- কলেজ কোড ফিল্টার -->
                    <select wire:model.live="collegeCodeFilter" class="w-full sm:w-1/3 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">সব কলেজ কোড</option>
                        @foreach($collegeCodes as $code)
                            <option value="{{ $code }}">{{ $code }}</option>
                        @endforeach
                    </select>

                    <!-- ল্যাব ফিল্টার -->
                    <select wire:model.live="labFilter" class="w-full sm:w-1/3 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">কম্পিউটার ল্যাব?</option>
                        <option value="Yes">ল্যাব আছে</option>
                        <option value="No">ল্যাব নেই</option>
                    </select>
                </div>

                <!-- ইম্পোর্ট বাটন -->
                <div class="w-full lg:w-auto flex justify-end">
                    <button
                        @click="showImportModal = true"
                        class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700 transition shadow-sm w-full sm:w-auto"
                    >
                        ডেটা ইম্পোর্ট
                    </button>
                </div>
            </div>
        </div>

        <!-- ডেটা টেবিল -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">TMIS ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">কলেজ কোড</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">শিক্ষকের নাম</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">পদবী ও বিষয়</th>
                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider">ল্যাব ও কম্পিউটার</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">যোগাযোগ</th>
                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider">অ্যাকশন</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-sm">
                @forelse ($teachers as $teacher)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                            {{ $teacher->tmis_id ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                            {{ $teacher->college_code ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $teacher->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="block text-gray-800 font-semibold">{{ $teacher->designation }}</span>
                            <span class="block text-gray-500 text-xs">{{ $teacher->subject }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($teacher->has_computer_lab === 'Yes')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    ল্যাব আছে
                                </span>
                                <span class="block text-gray-500 text-xs mt-1">কম্পিউটার: {{ $teacher->computer_count ?? 0 }}টি</span>
                            @elseif($teacher->has_computer_lab === 'No')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    ল্যাব নেই
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="block text-gray-800">{{ $teacher->mobile_number ?? '-' }}</span>
                            <span class="block text-blue-600 text-xs">{{ $teacher->email ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <button wire:click="editTeacher({{ $teacher->id }})" class="text-indigo-600 hover:text-indigo-900 bg-indigo-100 hover:bg-indigo-200 px-3 py-1 rounded transition mr-2">
                                Edit
                            </button>
                            <button wire:click="deleteTeacher({{ $teacher->id }})" wire:confirm="আপনি কি নিশ্চিত?" class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-3 py-1 rounded transition">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            কোনো ডেটা পাওয়া যায়নি!
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <!-- পেজিনেশন -->
            <div class="px-4 py-4 bg-white border-t border-gray-200 sm:px-6">
                {{ $teachers->links() }}
            </div>
        </div>
    </div>

        <!-- ইম্পোর্ট মডাল (Alpine.js) -->
        <div
            x-show="showImportModal"
            style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div
                    x-show="showImportModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    @click="showImportModal = false"
                    aria-hidden="true"
                ></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal panel -->
                <div
                    x-show="showImportModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative"
                >
                    <!-- Close Button -->
                    <button @click="showImportModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <!-- এখানে আগের তৈরি করা ইম্পোর্ট কম্পোনেন্ট কল করা হয়েছে -->
                        <livewire:teacher-data-import />
                    </div>
                </div>
            </div>
        </div>

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
                    class="relative flex max-h-[calc(100vh-1.5rem)] w-full max-w-5xl transform flex-col overflow-hidden rounded-2xl border border-white/60 bg-slate-50 text-left shadow-2xl transition-all sm:max-h-[calc(100vh-3rem)]"
                >

                    <div class="relative border-b border-slate-200 bg-white px-5 py-4 sm:px-7 sm:py-5">
                        <!-- Close Button -->
                        <button type="button" @click="showEditModal = false" class="absolute right-4 top-4 inline-flex size-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm transition hover:border-slate-300 hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" aria-label="এডিট ফরম বন্ধ করুন">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>

                        <div class="pr-12">
                            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Teacher profile</p>
                            <h3 class="mt-1 text-xl font-bold text-slate-900 sm:text-2xl" id="modal-title">শিক্ষকের তথ্য আপডেট করুন</h3>
                            <p class="mt-1 text-sm text-slate-500">প্রয়োজনীয় তথ্য পরিবর্তন করে নিচের আপডেট বাটনে ক্লিক করুন।</p>
                        </div>
                    </div>

                        <!-- এডিট ফর্ম -->
                        <form wire:submit.prevent="updateTeacher" class="flex min-h-0 flex-1 flex-col">
                            <div class="min-h-0 flex-1 space-y-5 overflow-y-auto px-4 py-5 sm:px-7 sm:py-6">

                            <fieldset class="space-y-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                <legend class="px-2 text-sm font-semibold text-slate-900">কলেজ ও আইডি তথ্য</legend>
                                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">কলেজ কোড</label>
                                        <input type="text" wire:model="editForm.college_code" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                    </div>
                                    <div class="lg:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700">কলেজের নাম</label>
                                        <input type="text" wire:model="editForm.college_name" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">TMIS ID</label>
                                        <input type="text" wire:model="editForm.tmis_id" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                        @error('editForm.tmis_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">TTIS ID</label>
                                        <input type="text" wire:model="editForm.ttis_id" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="space-y-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                <legend class="px-2 text-sm font-semibold text-slate-900">শিক্ষকের তথ্য</legend>
                                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    <div class="lg:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">শিক্ষকের নাম</label>
                                        <input type="text" wire:model="editForm.name" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                        @error('editForm.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">পদবী</label>
                                        <input type="text" wire:model="editForm.designation" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">বিষয়</label>
                                        <input type="text" wire:model="editForm.subject" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">শিক্ষক স্তর</label>
                                        <input type="text" wire:model="editForm.teacher_level" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">চাকরির ধরন</label>
                                        <input type="text" wire:model="editForm.employment_type" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="space-y-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                <legend class="px-2 text-sm font-semibold text-slate-900">প্রশিক্ষণের তথ্য</legend>
                                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">প্রশিক্ষণ আছে?</label>
                                        <input type="text" wire:model="editForm.has_training" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">প্রশিক্ষণ প্রতিষ্ঠান</label>
                                        <input type="text" wire:model="editForm.training_institute" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">প্রশিক্ষণের বছর</label>
                                        <input type="text" wire:model="editForm.training_year" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">ICT প্রশিক্ষণের নাম</label>
                                        <textarea wire:model="editForm.ict_training_name" rows="2" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">ICT প্রশিক্ষণের মেয়াদ</label>
                                        <textarea wire:model="editForm.ict_training_duration" rows="2" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">অন্যান্য প্রশিক্ষণের নাম</label>
                                        <textarea wire:model="editForm.other_training_name" rows="2" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">অন্যান্য প্রশিক্ষণের মেয়াদ</label>
                                        <textarea wire:model="editForm.other_training_duration" rows="2" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"></textarea>
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="space-y-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                <legend class="px-2 text-sm font-semibold text-slate-900">ল্যাব ও যোগাযোগ</legend>
                                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">কম্পিউটার ল্যাব</label>
                                    <select wire:model="editForm.has_computer_lab" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                        <option value="">নির্বাচন করুন</option>
                                        <option value="Yes">আছে</option>
                                        <option value="No">নেই</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">কম্পিউটার সংখ্যা</label>
                                    <input type="number" min="0" wire:model="editForm.computer_count" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">মোবাইল নম্বর</label>
                                    <input type="text" wire:model="editForm.mobile_number" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">ইমেইল</label>
                                    <input type="email" wire:model="editForm.email" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                    @error('editForm.email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                </div>
                            </fieldset>

                            </div>

                            <div class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-white px-4 py-4 sm:flex-row sm:justify-end sm:px-7">
                                <button type="button" @click="showEditModal = false" class="inline-flex w-full justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 sm:w-auto">
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
