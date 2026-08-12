<flux:card class="flex flex-col gap-5">
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
        <div>
            <flux:heading size="lg">{{ __('Training Registrations') }}</flux:heading>
            <flux:text class="mt-1">{{ __('Review registered teachers and manage training progress directly from the dashboard.') }}</flux:text>
        </div>
        <div class="flex gap-2">
            <flux:select wire:model.live="registrationStatus" size="sm" :label="__('Registration status')">
                @foreach (['All', 'Pending', 'Approved', 'Rejected', 'Completed'] as $status)
                    <option value="{{ $status }}">{{ __($status) }}</option>
                @endforeach
            </flux:select>
            <flux:button :href="route('training.manage')" wire:navigate variant="outline" size="sm" icon-trailing="arrow-up-right">{{ __('Manage all') }}</flux:button>
        </div>
    </div>

    <div class="grid gap-4">
        @forelse ($trainings as $training)
            <div wire:key="dashboard-training-{{ $training->id }}" class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                <div class="flex flex-col justify-between gap-3 lg:flex-row lg:items-center">
                    <div>
                        <p class="font-semibold text-zinc-950 dark:text-white">{{ $training->title }}</p>
                        <p class="mt-1 text-xs text-zinc-500">{{ $training->start_date->format('d M Y, g:i A') }} · {{ $training->pending_registrations_count }} {{ __('pending') }} · {{ $training->approved_registrations_count }} {{ __('approved') }}</p>
                    </div>
                    <flux:select wire:change="updateTrainingStatus({{ $training->id }}, $event.target.value)" size="sm" :aria-label="__('Training status')">
                        @foreach (['Draft', 'Upcoming', 'Ongoing', 'Completed', 'Canceled'] as $status)
                            <option value="{{ $status }}" @selected($training->status === $status)>{{ __($status) }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="mt-3 grid gap-2">
                    @foreach ($training->participants as $participant)
                        <div wire:key="dashboard-registration-{{ $training->id }}-{{ $participant->id }}" class="flex flex-col justify-between gap-2 rounded-lg bg-zinc-50 px-3 py-2.5 dark:bg-zinc-800/60 sm:flex-row sm:items-center">
                            <div>
                                <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ $participant->name }}</p>
                                <p class="text-xs text-zinc-500">{{ $participant->teacherProfile?->college?->name ?: __('College not specified') }} · {{ $participant->email }}</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-1.5">
                                <flux:badge :color="match($participant->pivot->status) { 'Approved' => 'blue', 'Completed' => 'green', 'Rejected' => 'red', default => 'amber' }">{{ __($participant->pivot->status) }}</flux:badge>
                                @if ($participant->pivot->status === 'Pending')
                                    <flux:button size="sm" variant="primary" wire:click="approve({{ $training->id }}, {{ $participant->id }})">{{ __('Approve') }}</flux:button>
                                    <flux:button size="sm" variant="ghost" wire:click="reject({{ $training->id }}, {{ $participant->id }})">{{ __('Reject') }}</flux:button>
                                @elseif ($participant->pivot->status === 'Approved' && $training->end_date->isPast())
                                    <flux:button size="sm" variant="primary" wire:click="complete({{ $training->id }}, {{ $participant->id }})">{{ __('Complete') }}</flux:button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-zinc-300 py-8 text-center dark:border-zinc-700">
                <flux:text>{{ __('No training registrations match this status.') }}</flux:text>
            </div>
        @endforelse
    </div>
</flux:card>
