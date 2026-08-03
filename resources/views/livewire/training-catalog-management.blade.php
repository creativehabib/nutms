<div class="space-y-6 p-4 sm:p-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-2">
        <flux:heading size="xl" class="font-bold tracking-tight">{{ __('Training Catalog') }}</flux:heading>
        <flux:subheading>{{ __('Training information') }}</flux:subheading>
    </div>

    <!-- Forms Section (Grid) -->
    <div class="grid gap-6 lg:grid-cols-2">

        <!-- Institute Form Card -->
        <flux:card class="flex flex-col gap-6">
            <div class="flex items-center gap-3 border-b border-zinc-100 pb-4 dark:border-zinc-800">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400">
                    <flux:icon.building-office-2 class="size-5" />
                </div>
                <flux:heading size="lg">{{ $editingInstituteId ? __('Edit Institute') : __('New Training Institute') }}</flux:heading>
            </div>

            <form wire:submit="saveInstitute" class="space-y-5">
                <flux:input
                    wire:model="instituteName"
                    :label="__('Institute Name')"
                    :placeholder="__('Information')"
                    required
                />

                <div class="flex items-center justify-between">
                    <flux:switch wire:model="instituteIsActive" :label="__('Keep active')" />

                    <div class="flex items-center gap-2">
                        @if ($editingInstituteId)
                            <flux:button type="button" variant="ghost" wire:click="cancelInstituteEdit">{{ __('Cancel') }}</flux:button>
                        @endif
                        <flux:button type="submit" variant="primary">
                            {{ $editingInstituteId ? __('Update') : __('Save') }}
                        </flux:button>
                    </div>
                </div>
            </form>
            @error('instituteName')<flux:text class="text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>@enderror

            <!-- Institutes List -->
            <div class="mt-2 divide-y divide-zinc-100 rounded-lg border border-zinc-200 bg-zinc-50/50 px-4 dark:divide-zinc-800 dark:border-zinc-700/50 dark:bg-zinc-900/50">
                <flux:heading size="sm" class="py-3 text-zinc-500">{{ __('Information') }}</flux:heading>
                @forelse ($institutes as $institute)
                    <div wire:key="institute-{{ $institute->id }}" class="flex items-center justify-between gap-4 py-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <p class="truncate font-medium text-zinc-900 dark:text-zinc-100">{{ $institute->name }}</p>
                                @if(!$institute->is_active)
                                    <flux:badge size="sm" color="amber">{{ __('Inactive') }}</flux:badge>
                                @endif
                            </div>
                            <flux:text class="mt-0.5 text-xs">{{ $institute->training_types_count }}items training types added</flux:text>
                        </div>
                        <div class="flex shrink-0 gap-1">
                            <flux:button variant="ghost" size="sm" icon="pencil-square" wire:click="editInstitute({{ $institute->id }})" :title="__('Edit')" />
                            <flux:button variant="ghost" size="sm" icon="trash" class="text-red-500 hover:text-red-600" wire:click="confirmDeleteInstitute({{ $institute->id }})" :title="__('Delete')" />
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-sm text-zinc-500">{{ __('Information') }}</div>
                @endforelse
            </div>
        </flux:card>

        <!-- Training Type Form Card -->
        <flux:card class="flex flex-col gap-6">
            <div class="flex items-center gap-3 border-b border-zinc-100 pb-4 dark:border-zinc-800">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-teal-50 text-teal-600 dark:bg-teal-500/20 dark:text-teal-400">
                    <flux:icon.academic-cap class="size-5" />
                </div>
                <flux:heading size="lg">{{ $editingTrainingTypeId ? __('Edit Training Type') : __('New Training Type') }}</flux:heading>
            </div>

            <form wire:submit="saveTrainingType" class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <flux:select wire:model="trainingInstituteId" :label="__('Select Institute')" :placeholder="__('Information')" required>
                        @foreach ($institutes as $institute)
                            <option value="{{ $institute->id }}">{{ $institute->name }}{{ $institute->is_active ? '' : __('Information') }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="sm:col-span-2">
                    <flux:input wire:model="trainingTypeName" :label="__('Training Name')" :placeholder="__('Information')" required />
                </div>

                <flux:input wire:model="durationValue" type="number" min="1" max="999" :label="__('Duration (number)')" :placeholder="__('Information')" required />
                <flux:select wire:model="durationUnit" :label="__('Duration Unit')">
                    <option value="hours">{{ __('Hours') }}</option>
                    <option value="days">{{ __('Days') }}</option>
                    <option value="weeks">{{ __('Weeks') }}</option>
                    <option value="months">{{ __('Months') }}</option>
                </flux:select>

                <div class="sm:col-span-2 mt-2 flex items-center justify-between border-t border-zinc-100 pt-5 dark:border-zinc-800">
                    <flux:switch wire:model="trainingTypeIsActive" :label="__('Keep active')" />
                    <div class="flex gap-2">
                        @if ($editingTrainingTypeId)
                            <flux:button type="button" variant="ghost" wire:click="cancelTrainingTypeEdit">{{ __('Cancel') }}</flux:button>
                        @endif
                        <flux:button type="submit" variant="primary">
                            {{ $editingTrainingTypeId ? __('Update') : __('Save') }}
                        </flux:button>
                    </div>
                </div>
            </form>

            <div class="flex flex-col gap-1">
                @error('trainingInstituteId')<flux:text class="text-sm text-red-600">{{ $message }}</flux:text>@enderror
                @error('trainingTypeName')<flux:text class="text-sm text-red-600">{{ $message }}</flux:text>@enderror
                @error('durationValue')<flux:text class="text-sm text-red-600">{{ $message }}</flux:text>@enderror
            </div>
        </flux:card>

    </div>

    <!-- Table Section -->
    <flux:card class="p-0 sm:p-0 overflow-hidden">

        <div class="border-b border-zinc-200 bg-zinc-50/50 p-4 dark:border-zinc-700/50 dark:bg-zinc-900/50 sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <flux:heading size="lg">{{ __('Training Type List') }}</flux:heading>
                    <flux:subheading class="mt-1">{{ __('Training information') }}</flux:subheading>
                </div>
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    icon="magnifying-glass"
                    :placeholder="__('Training information')"
                    class="w-full sm:max-w-xs"
                />
            </div>
        </div>

        <div class="overflow-x-auto px-4 pb-4 sm:px-6 pt-2">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Training Name') }}</flux:table.column>
                    <flux:table.column>{{ __('Institute') }}</flux:table.column>
                    <flux:table.column>{{ __('Duration') }}</flux:table.column>
                    <flux:table.column class="text-center">{{ __('Assigned Teachers') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                    <flux:table.column class="text-right">{{ __('Action') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($trainingTypes as $trainingType)
                        <flux:table.row wire:key="training-type-{{ $trainingType->id }}" class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition-colors">

                            <!-- Training Name -->
                            <flux:table.cell class="font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $trainingType->name }}
                            </flux:table.cell>

                            <!-- Institute -->
                            <flux:table.cell class="text-zinc-600 dark:text-zinc-300">
                                <div class="flex items-center gap-1.5">
                                    <flux:icon.building-office-2 variant="micro" class="text-zinc-400" />
                                    <span>{{ $trainingType->trainingInstitute->name }}</span>
                                </div>
                            </flux:table.cell>

                            <!-- Duration -->
                            <flux:table.cell>
                                <div class="flex items-center gap-1.5 text-zinc-700 dark:text-zinc-300">
                                    <flux:icon.clock variant="micro" class="text-zinc-400" />
                                    <span>{{ $trainingType->duration_value }} {{ ['hours' => __('Hours'), 'days' => __('Days'), 'weeks' => __('Weeks'), 'months' => __('Months')][$trainingType->duration_unit] ?? '' }}</span>
                                </div>
                            </flux:table.cell>

                            <!-- Teachers Count -->
                            <flux:table.cell class="text-center font-medium">
                                {{ $trainingType->teachers_count }} teachers
                            </flux:table.cell>

                            <!-- Status -->
                            <flux:table.cell>
                                <flux:badge size="sm" :color="$trainingType->is_active ? 'emerald' : 'amber'">
                                    {{ $trainingType->is_active ? __('Active') : __('Inactive') }}
                                </flux:badge>
                            </flux:table.cell>

                            <!-- Actions -->
                            <flux:table.cell>
                                <div class="flex items-center justify-end gap-1">
                                    <flux:button variant="ghost" size="sm" icon="pencil-square" wire:click="editTrainingType({{ $trainingType->id }})" :title="__('Edit')" class="text-zinc-500 hover:text-indigo-600" />
                                    <flux:button variant="ghost" size="sm" icon="trash" wire:click="confirmDeleteTrainingType({{ $trainingType->id }})" :title="__('Delete')" class="text-zinc-500 hover:text-red-600" />
                                </div>
                            </flux:table.cell>

                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6">
                                <div class="flex flex-col items-center justify-center py-12 text-zinc-500 dark:text-zinc-400">
                                    <flux:icon.document-magnifying-glass class="h-10 w-10 mb-3 text-zinc-400" />
                                    <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">{{ __('Training information') }}</p>
                                    <p class="text-sm mt-1">{{ __('Training information') }}</p>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        <!-- Pagination -->
        @if($trainingTypes->hasPages())
            <div class="border-t border-zinc-200 p-4 px-4 sm:px-6 dark:border-zinc-700/50 bg-zinc-50/30 dark:bg-zinc-900/30">
                {{ $trainingTypes->links() }}
            </div>
        @endif

    </flux:card>

    <flux:modal name="confirm-training-catalog-deletion" wire:model="showDeleteModal" @close="cancelDelete" focusable class="max-w-md">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $deletingType === 'institute' ? __('Delete information') : __('Training information') }}</flux:heading>
                <flux:text class="mt-2">
                    <span class="font-semibold text-zinc-900 dark:text-white">{{ $deletingName }}</span>{{ __('Delete information') }}</flux:text>
            </div>

            <div class="flex justify-end gap-2">
                <flux:modal.close><flux:button type="button" wire:click="cancelDelete">{{ __('Cancel') }}</flux:button></flux:modal.close>
                <flux:button variant="danger" wire:click="deleteConfirmed" wire:loading.attr="disabled" wire:target="deleteConfirmed">
                    <span wire:loading.remove wire:target="deleteConfirmed">{{ __('Delete information') }}</span>
                    <span wire:loading wire:target="deleteConfirmed">{{ __('Delete information') }}</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
