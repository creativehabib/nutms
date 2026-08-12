<div class="mx-auto w-full p-4 sm:p-6">
    <flux:card class="overflow-hidden p-0 shadow-sm">
        <div class="flex flex-col justify-between gap-4 border-b border-zinc-200 bg-zinc-50/50 p-5 dark:border-zinc-700 dark:bg-zinc-900/40 sm:flex-row sm:items-start">
            <div>
                <flux:heading size="xl" class="font-bold tracking-tight">{{ __('Training Management') }}</flux:heading>
                <flux:text class="mt-1 text-sm">{{ __('View recently published trainings, registered teachers, and manage training details.') }}</flux:text>
            </div>
            <div class="flex items-center gap-2">
                <flux:badge color="zinc">{{ trans_choice(':count training|:count trainings', $trainings->total()) }}</flux:badge>
                <flux:button variant="primary" icon="plus" size="sm" wire:click="create">{{ __('Add Training') }}</flux:button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Training') }}</flux:table.column>
                    <flux:table.column>{{ __('Schedule') }}</flux:table.column>
                    <flux:table.column>{{ __('Registered Teachers') }}</flux:table.column>
                    <flux:table.column>{{ __('Type') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                    <flux:table.column class="text-right">{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ($trainings as $training)
                        <flux:table.row wire:key="training-row-{{ $training->id }}">
                            <flux:table.cell>
                                <p class="font-semibold text-zinc-900 dark:text-white">{{ $training->title }}</p>
                                <p class="mt-1 max-w-xs truncate text-xs text-zinc-500">{{ $training->instructor_name ?: __('Instructor not specified') }}</p>
                            </flux:table.cell>
                            <flux:table.cell>
                                <p class="text-sm font-medium">{{ $training->start_date->format('d M Y') }}</p>
                                <p class="text-xs text-zinc-500">{{ $training->start_date->format('g:i A') }} – {{ $training->end_date->format('g:i A') }}</p>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex items-center gap-2">
                                    <div class="flex -space-x-2">
                                        @foreach ($training->participants->take(4) as $participant)
                                            <flux:avatar wire:key="participant-avatar-{{ $training->id }}-{{ $participant->id }}" size="sm" :name="$participant->name" :title="$participant->name" />
                                        @endforeach
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold">{{ $training->participants_count }}</p>
                                        @if ($training->participants->isNotEmpty())
                                            <p class="max-w-48 truncate text-xs text-zinc-500">{{ $training->participants->pluck('name')->join(', ') }}</p>
                                        @else
                                            <p class="text-xs text-zinc-500">{{ __('No registrations') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell><flux:badge :color="match($training->type) { 'Online' => 'green', 'Hybrid' => 'amber', default => 'blue' }">{{ __($training->type) }}</flux:badge></flux:table.cell>
                            <flux:table.cell><flux:badge :color="match($training->status) { 'Upcoming' => 'blue', 'Ongoing' => 'amber', 'Completed' => 'green', 'Canceled' => 'red', default => 'zinc' }">{{ __($training->status) }}</flux:badge></flux:table.cell>
                            <flux:table.cell class="text-right">
                                <flux:button variant="ghost" size="sm" icon="pencil-square" wire:click="edit({{ $training->id }})">{{ __('Edit') }}</flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6">
                                <div class="flex flex-col items-center gap-2 py-12 text-center">
                                    <flux:icon.academic-cap class="size-10 text-zinc-300" />
                                    <flux:heading>{{ __('No training has been published yet.') }}</flux:heading>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        @if ($trainings->hasPages())
            <div class="border-t border-zinc-200 p-4 dark:border-zinc-700">{{ $trainings->links() }}</div>
        @endif
    </flux:card>

    <flux:modal wire:model="showTrainingModal" name="training-form-modal" class="max-w-4xl p-0">
        <form wire:submit="save">
            <div class="border-b border-zinc-200 px-6 py-5 dark:border-zinc-700">
                <flux:heading size="lg">{{ $editingTrainingId ? __('Edit Training') : __('Create Training') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Enter the training schedule, registration details, and publication status.') }}</flux:text>
            </div>

            <div class="grid max-h-[70vh] gap-5 overflow-y-auto p-6 sm:grid-cols-2">
                <div class="sm:col-span-2"><flux:input wire:model="title" :label="__('Training title')" required /></div>
                <div class="sm:col-span-2"><flux:textarea wire:model="description" :label="__('Details')" rows="4" /></div>
                <flux:input wire:model="startDate" type="datetime-local" :label="__('Start date and time')" required />
                <flux:input wire:model="endDate" type="datetime-local" :label="__('End date and time')" required />
                <flux:input wire:model="registrationDeadline" type="datetime-local" :label="__('Registration deadline')" required />
                <flux:select wire:model="type" :label="__('Training type')"><option value="Online">{{ __('Online') }}</option><option value="Offline">{{ __('Offline') }}</option><option value="Hybrid">{{ __('Hybrid') }}</option></flux:select>
                <flux:input wire:model="instructorName" :label="__('Instructor name')" />
                <flux:input wire:model="capacity" type="number" min="1" :label="__('Seat capacity')" />
                <div class="sm:col-span-2"><flux:input wire:model="locationOrLink" :label="__('Venue or meeting link')" /></div>
                <flux:select wire:model="status" :label="__('Publication status')">@foreach (['Draft', 'Upcoming', 'Ongoing', 'Completed', 'Canceled'] as $trainingStatus)<option value="{{ $trainingStatus }}">{{ __($trainingStatus) }}</option>@endforeach</flux:select>
                <div class="flex items-end pb-2"><flux:switch wire:model="hasCertificate" :label="__('Issue certificate after completion')" /></div>
                <div class="sm:col-span-2"><flux:callout variant="info" icon="user-group">{{ __('Every approved teacher from an active affiliated college can register.') }}</flux:callout></div>
            </div>

            <div class="flex justify-end gap-2 border-t border-zinc-200 px-6 py-4 dark:border-zinc-700">
                <flux:button type="button" variant="ghost" wire:click="resetForm">{{ __('Cancel') }}</flux:button>
                <flux:button type="submit" variant="primary" icon="check">{{ $editingTrainingId ? __('Update Training') : __('Create Training') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
