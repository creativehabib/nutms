<div class="mx-auto w-full p-4 sm:p-6">
<flux:card class="overflow-hidden p-0">
    <div class="flex flex-col justify-between gap-4 border-b border-zinc-200 bg-zinc-50/50 p-5 dark:border-zinc-700 dark:bg-zinc-900/40 sm:flex-row sm:items-end">
        <div>
            <flux:heading size="lg">{{ __('Upcoming Training Registrations') }}</flux:heading>
            <flux:text class="mt-1">{{ __('Verify registered teachers before they participate in training.') }}</flux:text>
        </div>
        <div class="flex flex-wrap items-end gap-2">
            <flux:select wire:model.live="registrationStatus" size="sm" :label="__('Registration status')">
                @foreach (['All', 'Pending', 'Approved', 'Rejected'] as $status)
                    <option value="{{ $status }}">{{ __($status) }}</option>
                @endforeach
            </flux:select>
            <flux:button :href="route('training.manage')" wire:navigate variant="outline" size="sm" icon-trailing="arrow-up-right">{{ __('Manage trainings') }}</flux:button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Training') }}</flux:table.column>
                <flux:table.column>{{ __('Registered Teacher') }}</flux:table.column>
                <flux:table.column>{{ __('College') }}</flux:table.column>
                <flux:table.column>{{ __('Registered At') }}</flux:table.column>
                <flux:table.column>{{ __('Registration Status') }}</flux:table.column>
                <flux:table.column>{{ __('Training Status') }}</flux:table.column>
                <flux:table.column class="text-right">{{ __('Verification') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($trainings as $training)
                    @foreach ($training->participants as $participant)
                        <flux:table.row wire:key="registration-row-{{ $training->id }}-{{ $participant->id }}">
                            <flux:table.cell>
                                <p class="font-semibold text-zinc-900 dark:text-white">{{ $training->title }}</p>
                                <p class="mt-1 text-xs text-zinc-500">{{ $training->start_date->format('d M Y, g:i A') }}</p>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex items-center gap-2">
                                    <flux:avatar size="sm" :name="$participant->name" />
                                    <div><p class="text-sm font-medium">{{ $participant->name }}</p><p class="text-xs text-zinc-500">{{ $participant->email }}</p></div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>{{ $participant->teacherProfile?->college?->name ?: __('College not specified') }}</flux:table.cell>
                            <flux:table.cell>{{ \Illuminate\Support\Carbon::parse($participant->pivot->created_at)->format('d M Y, g:i A') }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="match($participant->pivot->status) { 'Approved' => 'blue', 'Completed' => 'green', 'Rejected' => 'red', default => 'amber' }">{{ __($participant->pivot->status) }}</flux:badge>
                                @if ($participant->pivot->status === 'Approved')
                                    <p class="mt-1 text-xs text-emerald-600 dark:text-emerald-400">{{ __('Eligible to participate') }}</p>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:select wire:change="updateTrainingStatus({{ $training->id }}, $event.target.value)" size="sm" :aria-label="__('Training status')">
                                    @foreach (['Draft', 'Upcoming', 'Ongoing', 'Completed', 'Canceled'] as $status)
                                        <option value="{{ $status }}" @selected($training->status === $status)>{{ __($status) }}</option>
                                    @endforeach
                                </flux:select>
                            </flux:table.cell>
                            <flux:table.cell class="text-right">
                                <div class="flex justify-end gap-1.5">
                                    @if ($participant->pivot->status === 'Pending')
                                        <flux:button size="sm" variant="primary" icon="check" wire:click="approve({{ $training->id }}, {{ $participant->id }})">{{ __('Approve') }}</flux:button>
                                        <flux:button size="sm" variant="ghost" icon="x-mark" wire:click="reject({{ $training->id }}, {{ $participant->id }})">{{ __('Reject') }}</flux:button>
                                    @elseif ($participant->pivot->status === 'Approved' && $training->end_date->isPast())
                                        <flux:button size="sm" variant="primary" icon="academic-cap" wire:click="complete({{ $training->id }}, {{ $participant->id }})">{{ __('Complete Training') }}</flux:button>
                                    @else
                                        <span class="text-xs text-zinc-500">{{ __('No action available') }}</span>
                                    @endif
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                @empty
                    <flux:table.row><flux:table.cell colspan="7"><div class="py-10 text-center"><flux:text>{{ __('No training registrations match this status.') }}</flux:text></div></flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
    @if ($trainings->hasPages())
        <div class="border-t border-zinc-200 p-4 dark:border-zinc-700">{{ $trainings->links() }}</div>
    @endif
</flux:card>
</div>
