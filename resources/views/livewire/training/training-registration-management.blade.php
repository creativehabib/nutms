<div class="mx-auto w-full p-4 sm:p-6">
<flux:card class="overflow-hidden p-0">
    <div class="flex flex-col justify-between gap-4 border-b border-zinc-200 bg-zinc-50/50 p-5 dark:border-zinc-700 dark:bg-zinc-900/40 sm:flex-row sm:items-end">
        <div>
            <flux:heading size="lg">{{ __('Registered Teachers') }}</flux:heading>
            <flux:text class="mt-1">{{ __('Review and verify teacher registrations before training participation.') }}</flux:text>
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
        <flux:table class="px-4">
            <flux:table.columns>
                <flux:table.column>{{ __('Training') }}</flux:table.column>
                <flux:table.column>{{ __('Registered Teacher') }}</flux:table.column>
                <flux:table.column>{{ __('College') }}</flux:table.column>
                <flux:table.column>{{ __('Registered At') }}</flux:table.column>
                <flux:table.column>{{ __('Registration Status') }}</flux:table.column>
                <flux:table.column class="text-right">{{ __('Actions') }}</flux:table.column>
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
                                <flux:select wire:change="updateRegistrationStatus({{ $training->id }}, {{ $participant->id }}, $event.target.value)" size="sm" :aria-label="__('Registration status')">
                                    @foreach (['Pending', 'Approved', 'Rejected'] as $status)
                                        <option value="{{ $status }}" @selected($participant->pivot->status === $status)>{{ __($status) }}</option>
                                    @endforeach
                                </flux:select>
                            </flux:table.cell>
                            <flux:table.cell class="text-right">
                                <div class="flex justify-end gap-1.5">
                                    <flux:button size="sm" variant="ghost" icon="eye" wire:click="viewRegistration({{ $training->id }}, {{ $participant->id }})">{{ __('View') }}</flux:button>
                                    <flux:button size="sm" variant="ghost" icon="trash" class="text-red-600" wire:click="confirmDelete({{ $training->id }}, {{ $participant->id }})">{{ __('Delete') }}</flux:button>
                                    @if ($participant->pivot->status === 'Approved' && $training->end_date->isPast())
                                        <flux:button size="sm" variant="primary" icon="academic-cap" wire:click="complete({{ $training->id }}, {{ $participant->id }})">{{ __('Complete Training') }}</flux:button>
                                    @endif
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                @empty
                    <flux:table.row><flux:table.cell colspan="6"><div class="py-10 text-center"><flux:text>{{ __('No training registrations match this status.') }}</flux:text></div></flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
    @if ($trainings->hasPages())
        <div class="border-t border-zinc-200 p-4 dark:border-zinc-700">{{ $trainings->links() }}</div>
    @endif
</flux:card>

<flux:modal wire:model="showRegistrationModal" name="registration-details" class="max-w-lg">
    @if ($selectedRegistration)
        <div class="flex flex-col gap-5">
            <div><flux:heading size="lg">{{ __('Registration Details') }}</flux:heading><flux:text class="mt-1">{{ $selectedRegistration['training']->title }}</flux:text></div>
            <dl class="grid gap-3 text-sm sm:grid-cols-2">
                <div><dt class="text-zinc-500">{{ __('Teacher') }}</dt><dd class="font-medium">{{ $selectedRegistration['participant']->name }}</dd></div>
                <div><dt class="text-zinc-500">{{ __('Email') }}</dt><dd class="font-medium">{{ $selectedRegistration['participant']->email }}</dd></div>
                <div><dt class="text-zinc-500">{{ __('College') }}</dt><dd class="font-medium">{{ $selectedRegistration['participant']->teacherProfile?->college?->name ?: __('College not specified') }}</dd></div>
                <div><dt class="text-zinc-500">{{ __('Status') }}</dt><dd><flux:badge>{{ __($selectedRegistration['participant']->pivot->status) }}</flux:badge></dd></div>
                <div><dt class="text-zinc-500">{{ __('Registered At') }}</dt><dd class="font-medium">{{ \Illuminate\Support\Carbon::parse($selectedRegistration['participant']->pivot->created_at)->format('d M Y, g:i A') }}</dd></div>
                <div><dt class="text-zinc-500">{{ __('Training Date') }}</dt><dd class="font-medium">{{ $selectedRegistration['training']->start_date->format('d M Y, g:i A') }}</dd></div>
            </dl>
            <div class="flex justify-end"><flux:button variant="primary" wire:click="resetSelection">{{ __('Close') }}</flux:button></div>
        </div>
    @endif
</flux:modal>

<flux:modal wire:model="showDeleteModal" name="delete-registration" class="max-w-md">
    <div class="flex flex-col gap-4">
        <div><flux:heading size="lg">{{ __('Delete Registration?') }}</flux:heading><flux:text class="mt-2">{{ __('This removes the teacher registration from this training. This action cannot be undone.') }}</flux:text></div>
        <div class="flex justify-end gap-2"><flux:button variant="ghost" wire:click="resetSelection">{{ __('Cancel') }}</flux:button><flux:button variant="danger" wire:click="deleteRegistration">{{ __('Delete') }}</flux:button></div>
    </div>
</flux:modal>
</div>
