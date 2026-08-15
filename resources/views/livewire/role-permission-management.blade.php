<div class="space-y-6 p-4 sm:p-6 mx-auto w-full max-w-7xl">

    <!-- ======================================= -->
    <!-- PAGE HEADER                             -->
    <!-- ======================================= -->
    <div class="flex flex-col gap-2">
        <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-white">{{ __('Roles & Permissions') }}</flux:heading>
        <flux:subheading class="text-zinc-500 dark:text-zinc-400">{{ __('Manage role permissions and user role assignments.') }}</flux:subheading>
    </div>

    <!-- ======================================= -->
    <!-- CUSTOM ROLES CARD                       -->
    <!-- ======================================= -->
    <flux:card class="flex flex-col gap-5 border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-zinc-100 pb-4 dark:border-zinc-800">
            <div>
                <flux:heading size="lg" class="text-zinc-900 dark:text-white">{{ __('Custom Roles') }}</flux:heading>
                <flux:subheading class="mt-1 text-zinc-500 dark:text-zinc-400">{{ __('Create, rename, or delete roles for your organization.') }}</flux:subheading>
            </div>
            <flux:button variant="primary" icon="plus" wire:click="createRole" class="shadow-sm">{{ __('Create role') }}</flux:button>
        </div>

        <flux:error name="role" />

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($roles as $role)
                @php($isSystemRole = in_array($role->name, $systemRoleNames, true))
                <div wire:key="role-{{ $role->id }}" class="group flex items-center justify-between gap-3 rounded-xl border border-zinc-200 bg-zinc-50/50 p-4 transition-colors hover:border-indigo-300 dark:border-zinc-700/50 dark:bg-zinc-800/40 dark:hover:border-indigo-500/50">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="truncate font-semibold text-zinc-900 dark:text-white">{{ __(str($role->name)->replace(['-', '_'], ' ')->title()->toString()) }}</span>
                            @if($isSystemRole)
                                <flux:badge size="sm" color="zinc" class="font-medium shadow-sm">{{ __('System') }}</flux:badge>
                            @endif
                        </div>
                        <p class="mt-1.5 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                            <flux:icon.user-group variant="micro" class="inline-block size-3.5 mr-0.5 opacity-70" />
                            {{ trans_choice(':count assigned user|:count assigned users', $role->users_count, ['count' => $role->users_count]) }}
                        </p>
                    </div>
                    @unless($isSystemRole)
                        <div class="flex shrink-0 items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <flux:button size="sm" variant="subtle" icon="pencil-square" class="text-indigo-600 hover:bg-indigo-50 hover:text-indigo-700 dark:text-indigo-400 dark:hover:bg-indigo-500/10 dark:hover:text-indigo-300" :aria-label="__('Edit role')" wire:click="editRole({{ $role->id }})" />
                            <flux:button size="sm" variant="subtle" icon="trash" class="text-rose-500 hover:bg-rose-50 hover:text-rose-600 dark:text-rose-400 dark:hover:bg-rose-500/10 dark:hover:text-rose-300" :aria-label="__('Delete role')" wire:click="deleteRole({{ $role->id }})" wire:confirm="{{ __('Are you sure you want to delete this role?') }}" />
                        </div>
                    @endunless
                </div>
            @endforeach
        </div>
    </flux:card>

    <!-- ======================================= -->
    <!-- PERMISSION MATRIX CARD                  -->
    <!-- ======================================= -->
    <flux:card class="flex flex-col gap-6 border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <!-- Matrix Header -->
        <div class="flex flex-col gap-4 border-b border-zinc-100 pb-5 dark:border-zinc-800 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                    <flux:icon.shield-check class="size-6" />
                </div>
                <div>
                    <flux:heading size="lg" class="text-zinc-900 dark:text-white">{{ __('Permission Matrix') }}</flux:heading>
                    <flux:subheading class="mt-0.5 text-zinc-500 dark:text-zinc-400">{{ __('Choose a role and select the permissions it should have.') }}</flux:subheading>
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="w-full sm:w-56">
                    <flux:select wire:model.live="selectedRole" :placeholder="__('Select a role')">
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ __(str($role->name)->replace(['-', '_'], ' ')->title()->toString()) }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <flux:button variant="primary" icon="check" wire:click="saveRolePermissions" class="shrink-0 shadow-sm">{{ __('Save Changes') }}</flux:button>
            </div>
        </div>

        <!-- Permissions Grid (Using Flux Checkbox) -->
        <div>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($permissions as $permission)
                    <label wire:key="permission-{{ $permission->id }}" class="group relative flex cursor-pointer items-start gap-3 rounded-xl border border-zinc-200 bg-zinc-50/30 p-4 transition-all hover:bg-zinc-50 has-[:checked]:border-indigo-400 has-[:checked]:bg-indigo-50 has-[:checked]:shadow-sm dark:border-zinc-700/60 dark:bg-zinc-800/20 dark:hover:bg-zinc-800/50 dark:has-[:checked]:border-indigo-500/50 dark:has-[:checked]:bg-indigo-500/10">

                        <div class="pt-0.5">
                            <flux:checkbox wire:model="selectedPermissions" value="{{ $permission->name }}" />
                        </div>

                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-zinc-700 group-has-[:checked]:text-indigo-800 dark:text-zinc-300 dark:group-has-[:checked]:text-indigo-300 transition-colors">
                                {{ __($permissionLabels[$permission->name] ?? $permission->name) }}
                            </span>
                            <span class="mt-1 text-[11px] font-mono font-medium text-zinc-400 dark:text-zinc-500 group-has-[:checked]:text-indigo-500/70 dark:group-has-[:checked]:text-indigo-400/70">
                                {{ $permission->name }}
                            </span>
                        </div>
                    </label>
                @endforeach
            </div>
            <flux:error name="permissions" class="mt-3" />
        </div>
    </flux:card>

    <!-- ======================================= -->
    <!-- USER ROLE MANAGEMENT CARD               -->
    <!-- ======================================= -->
    <flux:card class="p-0 sm:p-0 overflow-hidden shadow-sm border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">

        <!-- Card Header & Search -->
        <div class="border-b border-zinc-200 bg-zinc-50/50 p-4 dark:border-zinc-800/80 dark:bg-zinc-800/30 sm:p-5">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                        <flux:icon.users class="size-5" />
                    </div>
                    <div>
                        <flux:heading size="lg" class="text-zinc-900 dark:text-white">{{ __('User Roles') }}</flux:heading>
                        <flux:subheading class="mt-0.5 text-zinc-500 dark:text-zinc-400">{{ __('Review users and update their assigned roles.') }}</flux:subheading>
                    </div>
                </div>
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    icon="magnifying-glass"
                    :placeholder="__('Search users by name or email')"
                    class="w-full sm:max-w-xs shadow-sm bg-white dark:bg-zinc-900"
                />
            </div>
        </div>

        <!-- Table Section -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                <tr class="bg-zinc-50 dark:bg-zinc-800/40 border-b border-zinc-200 dark:border-zinc-700/50 text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                    <th class="px-5 py-3 w-1/3">{{ __('User') }}</th>
                    <th class="px-5 py-3 w-1/3">{{ __('Teacher & College') }}</th>
                    <th class="px-5 py-3">{{ __('Current Role') }}</th>
                    <th class="px-5 py-3 text-right">{{ __('Change Role') }}</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/80">
                @forelse($users as $user)
                    @php($teacherProfile = $user->teacherProfile)
                    <tr wire:key="role-user-{{ $user->id }}" class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/30 transition-colors">

                        <!-- User Details -->
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-zinc-100 text-zinc-500 border border-zinc-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400 shadow-sm">
                                    <flux:icon.user class="size-5" />
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $user->name }}</span>
                                    <div class="flex items-center gap-1 mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                                        <flux:icon.envelope variant="micro" class="opacity-70" />
                                        <span>{{ $user->email }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Teacher & College Details -->
                        <td class="px-5 py-4">
                            <div class="flex flex-col">
                                    <span class="font-medium text-sm text-zinc-700 dark:text-zinc-300">
                                        {{ $teacherProfile?->display_name ?: __('No teacher profile') }}
                                    </span>
                                <div class="flex items-center gap-1.5 mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                    <flux:icon.building-library variant="micro" class="opacity-70" />
                                    <span class="truncate max-w-[220px]">
                                            @if($teacherProfile?->college)
                                            {{ $teacherProfile->college->name }} ({{ $teacherProfile->college_code }})
                                        @elseif($user->college)
                                            {{ $user->college->name }}
                                        @else
                                            {{ __('No college assigned') }}
                                        @endif
                                        </span>
                                </div>
                            </div>
                        </td>

                        <!-- Current Role Badge -->
                        <td class="px-5 py-4">
                            <flux:badge size="sm" class="shadow-sm font-medium" :color="$user->hasRole('admin') ? 'indigo' : ($user->hasRole('principal') ? 'emerald' : 'zinc')">
                                {{ __(str($user->primaryRoleName())->replace(['-', '_'], ' ')->title()->toString()) }}
                            </flux:badge>
                        </td>

                        <!-- Change Role Action -->
                        <td class="px-5 py-4">
                            <div class="flex justify-end">
                                <div class="w-40">
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
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-zinc-500 dark:text-zinc-400">
                                <div class="bg-zinc-100 dark:bg-zinc-800 p-3 rounded-full mb-3">
                                    <flux:icon.users class="h-8 w-8 text-zinc-400 dark:text-zinc-500" />
                                </div>
                                <p class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ __('No users found') }}</p>
                                <p class="text-sm mt-1">{{ __('Try adjusting your search criteria.') }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="border-t border-zinc-200 px-5 py-4 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50">
                {{ $users->links() }}
            </div>
        @endif
    </flux:card>

    <!-- ======================================= -->
    <!-- ROLE FORM MODAL                         -->
    <!-- ======================================= -->
    <flux:modal name="role-form" @close="$wire.cancelRoleForm()" focusable class="max-w-md">
        <form wire:submit="saveRole" class="space-y-6">
            <div>
                <flux:heading size="lg" class="text-zinc-900 dark:text-white">{{ $editingRoleId === null ? __('Create new role') : __('Edit role') }}</flux:heading>
                <flux:subheading class="mt-1 text-zinc-500 dark:text-zinc-400">{{ __('Use a short, unique name. Permissions can be selected after saving.') }}</flux:subheading>
            </div>

            <flux:separator class="dark:border-zinc-700/50" />

            <flux:input wire:model="roleName" :label="__('Role name')" placeholder="e.g. content-manager" autocomplete="off" />

            <div class="flex justify-end gap-3 pt-2">
                <flux:modal.close>
                    <flux:button type="button" variant="ghost" wire:click="cancelRoleForm">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" icon="check" class="shadow-sm">{{ $editingRoleId === null ? __('Create Role') : __('Save Changes') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
