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
        <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background Overlay -->
                <div x-show="showEditModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEditModal = false" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Panel -->
                <div x-show="showEditModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 relative">
                        <!-- Close Button -->
                        <button @click="showEditModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>

                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">তথ্য আপডেট করুন</h3>

                        <!-- এডিট ফর্ম -->
                        <form wire:submit.prevent="updateTeacher" class="space-y-4">

                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">শিক্ষকের নাম</label>
                                <input type="text" wire:model="editForm.name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @error('editForm.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <!-- Designation -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">পদবী</label>
                                    <input type="text" wire:model="editForm.designation" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>

                                <!-- Subject -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">বিষয়</label>
                                    <input type="text" wire:model="editForm.subject" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <!-- Mobile -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">মোবাইল নম্বর</label>
                                    <input type="text" wire:model="editForm.mobile_number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">ইমেইল</label>
                                    <input type="email" wire:model="editForm.email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                            </div>

                            <div class="mt-5 sm:mt-6 flex gap-2 justify-end">
                                <button type="button" @click="showEditModal = false" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:w-auto sm:text-sm">
                                    বাতিল
                                </button>
                                <button type="submit" class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:w-auto sm:text-sm">
                                    <span wire:loading wire:target="updateTeacher" class="mr-2">সেভ হচ্ছে...</span>
                                    আপডেট করুন
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
</div>
