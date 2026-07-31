<div class="mx-auto w-full max-w-7xl space-y-6 p-4 sm:p-6">
    <div>
        <flux:heading size="xl">রোলস ও পারমিশন ম্যানেজার</flux:heading>
        <flux:text>প্রতিটি রোল কী করতে পারবে তা নির্ধারণ করুন এবং ব্যবহারকারীর রোল পরিবর্তন করুন।</flux:text>
    </div>

    <flux:card class="space-y-5">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="w-full sm:max-w-xs">
                <flux:select wire:model.live="selectedRole" label="যে রোলের পারমিশন সম্পাদনা করবেন">
                    @foreach($roles as $role)
                        <option value="{{ $role->value }}">{{ $role->label() }}</option>
                    @endforeach
                </flux:select>
            </div>
            <flux:button variant="primary" icon="check" wire:click="saveRolePermissions">পারমিশন সংরক্ষণ করুন</flux:button>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($permissions as $permission)
                <label wire:key="permission-{{ $permission->id }}" class="flex cursor-pointer gap-3 rounded-xl border border-zinc-200 p-4 transition hover:border-indigo-400 dark:border-zinc-700">
                    <input type="checkbox" wire:model="selectedPermissions" value="{{ $permission->name }}" class="mt-1 size-4 rounded border-zinc-300 text-indigo-600">
                    <span><span class="block font-medium">{{ $permissionLabels[$permission->name] ?? $permission->name }}</span><span class="text-xs text-zinc-500">{{ $permission->name }}</span></span>
                </label>
            @endforeach
        </div>
        <flux:error name="permissions" />
    </flux:card>

    <flux:card>
        <div class="mb-4"><flux:input wire:model.live.debounce.300ms="search" type="search" icon="magnifying-glass" placeholder="নাম বা ইমেইল দিয়ে user খুঁজুন" /></div>
        <div class="overflow-x-auto"><flux:table><flux:table.columns><flux:table.column>User</flux:table.column><flux:table.column>শিক্ষক ও কলেজ</flux:table.column><flux:table.column>বর্তমান রোল</flux:table.column><flux:table.column>রোল পরিবর্তন</flux:table.column></flux:table.columns><flux:table.rows>@foreach($users as $user)@php($teacherProfile = $user->teacherProfile)<flux:table.row wire:key="role-user-{{ $user->id }}"><flux:table.cell><p class="font-semibold">{{ $user->name }}</p><flux:text>{{ $user->email }}</flux:text></flux:table.cell><flux:table.cell>{{ $teacherProfile?->display_name ?: 'শিক্ষক প্রোফাইল নেই' }}<br><flux:text>{{ $teacherProfile?->college?->name ?: $user->college?->name ?: 'কলেজ নেই' }}</flux:text></flux:table.cell><flux:table.cell><flux:badge>{{ $user->role->label() }}</flux:badge></flux:table.cell><flux:table.cell><flux:select :disabled="$user->id === auth()->id()" wire:change="changeRole({{ $user->id }}, $event.target.value)">@foreach($roles as $role)<option value="{{ $role->value }}" @selected($user->role === $role)>{{ $role->label() }}</option>@endforeach</flux:select></flux:table.cell></flux:table.row>@endforeach</flux:table.rows></flux:table></div>
        <div class="mt-4">{{ $users->links() }}</div>
    </flux:card>
</div>
