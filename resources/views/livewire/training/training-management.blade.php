<div class="mx-auto flex w-full max-w-7xl flex-col gap-6 p-4 sm:p-6">
    <div>
        <flux:heading size="xl" class="font-bold">{{ __('Training Management') }}</flux:heading>
        <flux:subheading>{{ __('Create upcoming training, choose eligible teachers, and review registrations.') }}</flux:subheading>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_minmax(22rem,0.8fr)]">
        <flux:card>
            <form wire:submit="save" class="flex flex-col gap-5">
                <div class="flex items-center justify-between gap-3">
                    <flux:heading size="lg">{{ $editingTrainingId ? __('Edit Training') : __('Create Upcoming Training') }}</flux:heading>
                    @if ($editingTrainingId)
                        <flux:button type="button" variant="ghost" size="sm" wire:click="resetForm">{{ __('Cancel') }}</flux:button>
                    @endif
                </div>

                <flux:input wire:model="title" :label="__('Training title')" required />
                <flux:textarea wire:model="description" :label="__('Details')" rows="4" />

                <div class="grid gap-4 sm:grid-cols-2">
                    <flux:input wire:model="startDate" type="datetime-local" :label="__('Start date and time')" required />
                    <flux:input wire:model="endDate" type="datetime-local" :label="__('End date and time')" required />
                    <flux:input wire:model="registrationDeadline" type="datetime-local" :label="__('Registration deadline')" required />
                    <flux:select wire:model="type" :label="__('Training type')">
                        <option value="Online">{{ __('Online') }}</option>
                        <option value="Offline">{{ __('Offline') }}</option>
                        <option value="Hybrid">{{ __('Hybrid') }}</option>
                    </flux:select>
                    <flux:input wire:model="instructorName" :label="__('Instructor name')" />
                    <flux:input wire:model="capacity" type="number" min="1" :label="__('Seat capacity')" />
                    <div class="sm:col-span-2"><flux:input wire:model="locationOrLink" :label="__('Venue or meeting link')" /></div>
                    <flux:select wire:model="status" :label="__('Publication status')">
                        @foreach (['Draft', 'Upcoming', 'Ongoing', 'Completed', 'Canceled'] as $trainingStatus)
                            <option value="{{ $trainingStatus }}">{{ __($trainingStatus) }}</option>
                        @endforeach
                    </flux:select>
                    <div class="flex items-end pb-2"><flux:switch wire:model="hasCertificate" :label="__('Issue certificate after completion')" /></div>
                </div>

                <fieldset class="flex flex-col gap-3">
                    <legend class="text-sm font-semibold text-zinc-900 dark:text-white">{{ __('Teachers eligible to register') }}</legend>
                    <div class="grid max-h-64 gap-2 overflow-y-auto rounded-xl border border-zinc-200 p-3 dark:border-zinc-700 sm:grid-cols-2">
                        @forelse ($teachers as $teacher)
                            <flux:checkbox wire:key="eligible-teacher-{{ $teacher->id }}" wire:model="eligibleTeacherIds" value="{{ $teacher->id }}" :label="$teacher->name.' · '.$teacher->email" />
                        @empty
                            <flux:text>{{ __('No teacher profile is available.') }}</flux:text>
                        @endforelse
                    </div>
                    @error('eligibleTeacherIds') <flux:text class="text-sm text-red-600">{{ $message }}</flux:text> @enderror
                </fieldset>

                <flux:button type="submit" variant="primary" icon="check">{{ __('Save Training') }}</flux:button>
            </form>
        </flux:card>

        <div class="flex flex-col gap-4">
            <flux:heading size="lg">{{ __('Trainings and Registrations') }}</flux:heading>
            @forelse ($trainings as $training)
                <flux:card wire:key="managed-training-{{ $training->id }}" class="flex flex-col gap-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <flux:heading>{{ $training->title }}</flux:heading>
                            <flux:text class="mt-1 text-sm">{{ $training->start_date->format('d M Y, g:i A') }} · {{ __($training->type) }}</flux:text>
                        </div>
                        <flux:button variant="ghost" size="sm" icon="pencil-square" wire:click="edit({{ $training->id }})" :aria-label="__('Edit')" />
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <flux:badge color="indigo">{{ __($training->status) }}</flux:badge>
                        <flux:badge color="zinc">{{ trans_choice(':count eligible teacher|:count eligible teachers', $training->eligibleTeachers->count()) }}</flux:badge>
                        <flux:badge color="zinc">{{ trans_choice(':count registration|:count registrations', $training->participants_count) }}</flux:badge>
                    </div>

                    <div class="flex flex-col gap-2 border-t border-zinc-100 pt-3 dark:border-zinc-800">
                        @forelse ($training->participants as $participant)
                            <div wire:key="registration-{{ $training->id }}-{{ $participant->id }}" class="flex flex-col justify-between gap-2 rounded-lg bg-zinc-50 p-3 dark:bg-zinc-800/60 sm:flex-row sm:items-center">
                                <div>
                                    <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $participant->name }}</p>
                                    <p class="text-xs text-zinc-500">{{ $participant->email }} · {{ __($participant->pivot->status) }}</p>
                                </div>
                                <div class="flex gap-1">
                                    @if ($participant->pivot->status === 'Pending')
                                        <flux:button size="sm" variant="primary" wire:click="approve({{ $training->id }}, {{ $participant->id }})">{{ __('Approve') }}</flux:button>
                                        <flux:button size="sm" variant="ghost" wire:click="reject({{ $training->id }}, {{ $participant->id }})">{{ __('Reject') }}</flux:button>
                                    @elseif ($participant->pivot->status === 'Approved' && $training->end_date->isPast())
                                        <flux:button size="sm" variant="primary" wire:click="complete({{ $training->id }}, {{ $participant->id }})">{{ __('Mark completed') }}</flux:button>
                                    @else
                                        <flux:badge :color="$participant->pivot->status === 'Completed' ? 'green' : 'zinc'">{{ __($participant->pivot->status) }}</flux:badge>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <flux:text class="text-sm">{{ __('No registration has been submitted.') }}</flux:text>
                        @endforelse
                    </div>
                </flux:card>
            @empty
                <flux:card><flux:text>{{ __('No training has been created yet.') }}</flux:text></flux:card>
            @endforelse
        </div>
    </div>
</div>
