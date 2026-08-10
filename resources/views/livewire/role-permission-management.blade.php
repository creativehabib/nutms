<div class="space-y-6 p-4 sm:p-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-2">
        <flux:heading size="xl" class="font-bold tracking-tight">{{ __('Roles & Permissions') }}</flux:heading>
        <flux:subheading>{{ __('Manage role permissions and user role assignments.') }}</flux:subheading>
    </div>

    <flux:card class="flex flex-col gap-5">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="lg">{{ __('Custom Roles') }}</flux:heading>
                <flux:subheading class="mt-1">{{ __('Create, rename, or delete roles for your organization.') }}</flux:subheading>
            </div>
            <flux:button variant="primary" icon="plus" wire:click="createRole">{{ __('Create role') }}</flux:button>
        </div>

        <flux:error name="role" />

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($roles as $role)
                @php($isSystemRole = in_array($role->name, $systemRoleNames, true))
                <div wire:key="role-{{ $role->id }}" class="flex items-center justify-between gap-3 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="truncate font-semibold text-zinc-900 dark:text-zinc-100">{{ __(str($role->name)->replace(['-', '_'], ' ')->title()->toString()) }}</span>
                            @if($isSystemRole)
                                <flux:badge size="sm" color="zinc">{{ __('System') }}</flux:badge>
                            @endif
                        </div>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ trans_choice(':count assigned user|:count assigned users', $role->users_count, ['count' => $role->users_count]) }}</p>
                    </div>
                    @unless($isSystemRole)
                        <div class="flex shrink-0 items-center gap-1">
                            <flux:button size="sm" variant="ghost" icon="pencil-square" :aria-label="__('Edit role')" wire:click="editRole({{ $role->id }})" />
                            <flux:button size="sm" variant="ghost" icon="trash" :aria-label="__('Delete role')" wire:click="deleteRole({{ $role->id }})" wire:confirm="{{ __('Are you sure you want to delete this role?') }}" />
                        </div>
                    @endunless
                </div>
            @endforeach
        </div>
    </flux:card>

    <!-- Permissions Setup Card -->
    <flux:card class="flex flex-col gap-6">
        <!-- Card Header -->
        <div class="flex flex-col gap-4 border-b border-zinc-100 pb-5 dark:border-zinc-800 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400">
                    <flux:icon.shield-check class="size-5" />
                </div>
                <div>
                    <flux:heading size="lg">{{ __('Permission Matrix') }}</flux:heading>
                    <flux:subheading class="mt-0.5">{{ __('Choose a role and select the permissions it should have.') }}</flux:subheading>
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="w-full sm:w-64">
                    <flux:select wire:model.live="selectedRole" :placeholder="__('Select a role')">
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ __(str($role->name)->replace(['-', '_'], ' ')->title()->toString()) }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <flux:button variant="primary" icon="check" wire:click="saveRolePermissions" class="shrink-0 shadow-sm">{{ __('Save') }}</flux:button>
            </div>
        </div>

        <!-- Permissions Grid -->
        <div>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($permissions as $permission)
                    <label wire:key="permission-{{ $permission->id }}" class="group relative flex cursor-pointer items-start gap-3 rounded-xl border border-zinc-200 p-4 transition-colors hover:bg-zinc-50 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50/50 dark:border-zinc-700 dark:hover:bg-zinc-800/50 dark:has-[:checked]:border-indigo-400 dark:has-[:checked]:bg-indigo-500/10">
                        <div class="flex h-5 items-center">
                            <input type="checkbox" wire:model="selectedPermissions" value="{{ $permission->name }}" class="size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-600 dark:border-zinc-600 dark:bg-zinc-800 dark:checked:border-indigo-500 dark:checked:bg-indigo-500">
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-zinc-900 group-has-[:checked]:text-indigo-700 dark:text-zinc-100 dark:group-has-[:checked]:text-indigo-400">
                                {{ __($permissionLabels[$permission->name] ?? $permission->name) }}
                            </span>
                            <span class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400 font-mono">
                                {{ $permission->name }}
                            </span>
                        </div>
                    </label>
                @endforeach
            </div>
            <flux:error name="permissions" class="mt-2" />
        </div>
    </flux:card>

    <!-- Users Role Management Card -->
    <flux:card class="p-0 sm:p-0 overflow-hidden shadow-sm">

        <!-- Card Header & Search -->
        <div class="border-b border-zinc-200 bg-zinc-50/40 p-4 dark:border-zinc-700/50 dark:bg-zinc-900/40 sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <flux:heading size="lg">{{ __('User Roles') }}</flux:heading>
                    <flux:subheading class="mt-1">{{ __('Review users and update their assigned roles.') }}</flux:subheading>
                </div>
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    icon="magnifying-glass"
                    :placeholder="__('Search users')"
                    class="w-full sm:max-w-xs shadow-sm"
                />
            </div>
        </div>

        <!-- Table Section -->
        <div class="overflow-x-auto px-4 pb-4 sm:px-6 pt-2">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('User') }}</flux:table.column>
                    <flux:table.column>{{ __('Teacher & College') }}</flux:table.column>
                    <flux:table.column>{{ __('Current Role') }}</flux:table.column>
                    <flux:table.column class="text-right">{{ __('Change Role') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($users as $user)
                        @php($teacherProfile = $user->teacherProfile)
                        <flux:table.row wire:key="role-user-{{ $user->id }}" class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition-colors">

                            <!-- User Details -->
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                                        <flux:icon.user class="size-4" />
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $user->name }}</span>
                                        <div class="flex items-center gap-1 mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                                            <flux:icon.envelope variant="micro" />
                                            <span>{{ $user->email }}</span>
                                        </div>
                                    </div>
                                </div>
                            </flux:table.cell>

                            <!-- Teacher & College Details -->
                            <flux:table.cell>
                                <div class="flex flex-col">
                                    <span class="font-medium text-zinc-700 dark:text-zinc-300">
                                        {{ $teacherProfile?->display_name ?: __('No teacher profile') }}
                                    </span>
                                    <div class="flex items-center gap-1 mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                                        <flux:icon.building-library variant="micro" />
                                        <span class="truncate max-w-[200px]">{{ $teacherProfile?->college?->name ?: $user->college?->name ?: __('No college assigned') }}</span>
                                    </div>
                                </div>
                            </flux:table.cell>

                            <!-- Current Role Badge -->
                            <flux:table.cell>
                                <flux:badge size="sm" :color="$user->hasRole('admin') ? 'indigo' : ($user->hasRole('principal') ? 'emerald' : 'zinc')">
                                    {{ __(str($user->primaryRoleName())->replace(['-', '_'], ' ')->title()->toString()) }}
                                </flux:badge>
                            </flux:table.cell>

                            <!-- Change Role Action -->
                            <flux:table.cell>
                                <div class="flex justify-end">
                                    <div class="w-36">
                                        <flux:select
                                            :disabled="$user->id === auth()->id()"
                                            wire:change="changeRole({{ $user->id }}, $event.target.value)"
                                            size="sm"
                                        >
                                            @foreach($roles as $role)
                                                <option value="{{ $role->name }}" @selected($user->hasRole($role->name))>
                                                    {{ __(str($role->name)->replace(['-', '_'], ' ')->title()->toString()) }}
                                                </option>
                                            @endforeach
                                        </flux:select>
                                    </div>
                                </div>
                            </flux:table.cell>

                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="4">
                                <div class="flex flex-col items-center justify-center py-12 text-zinc-500 dark:text-zinc-400">
                                    <flux:icon.users class="h-10 w-10 mb-3 text-zinc-400" />
                                    <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">{{ __('No users found') }}</p>
                                    <p class="text-sm mt-1">{{ __('Try adjusting your search criteria.') }}</p>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="border-t border-zinc-200 p-4 px-4 sm:px-6 dark:border-zinc-700/50 bg-zinc-50/30 dark:bg-zinc-900/30">
                {{ $users->links() }}
            </div>
        @endif
    </flux:card>

    <flux:modal name="role-form" @close="$wire.cancelRoleForm()" focusable class="max-w-md">
        <form wire:submit="saveRole" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingRoleId === null ? __('Create role') : __('Edit role') }}</flux:heading>
                <flux:subheading class="mt-1">{{ __('Use a short, unique name. Permissions can be selected after saving.') }}</flux:subheading>
            </div>

            <flux:input wire:model="roleName" :label="__('Role name')" placeholder="content-manager" autocomplete="off" />

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button type="button" variant="ghost" wire:click="cancelRoleForm">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ $editingRoleId === null ? __('Create') : __('Save') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
