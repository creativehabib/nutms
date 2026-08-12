<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body
          x-data="{
            settingsOpen: false,
            helpOpen: false,
            mode: localStorage.getItem('flux.appearance') || localStorage.getItem('theme') || 'system',
            applyAppearance(selected) {
                this.mode = selected;

                if (this.$flux) {
                    this.$flux.appearance = selected;
                }

                if (selected === 'dark') {
                    localStorage.setItem('theme', 'dark');
                    localStorage.setItem('flux.appearance', 'dark');
                    document.documentElement.classList.add('dark');
                } else if (selected === 'light') {
                    localStorage.setItem('theme', 'light');
                    localStorage.setItem('flux.appearance', 'light');
                    document.documentElement.classList.remove('dark');
                } else {
                    localStorage.removeItem('theme');
                    localStorage.setItem('flux.appearance', 'system');
                    document.documentElement.classList.toggle('dark', window.matchMedia('(prefers-color-scheme: dark)').matches);
                }

                window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: selected } }));
            }
        }"
          x-on:keydown.escape.window="settingsOpen = false; helpOpen = false"
          class="min-h-screen bg-white dark:bg-zinc-800 flex"
    >


    <flux:header sticky collapsible="mobile" class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:navbar class="-mb-px max-lg:hidden">
            <flux:navbar.item icon="inbox" badge="12" href="#">{{ __('Inbox') }}</flux:navbar.item>
            <flux:separator vertical variant="subtle" class="my-2"/>
            <flux:dropdown class="max-lg:hidden">
                <flux:navbar.item icon:trailing="chevron-down">{{__('Favorites')}}</flux:navbar.item>
                <flux:navmenu>
                    <flux:navmenu.item href="#">{{__('Marketing site')}}</flux:navmenu.item>
                    <flux:navmenu.item href="#">{{__('Android app')}}</flux:navmenu.item>
                    <flux:navmenu.item href="#">{{ __('Brand guidelines') }}</flux:navmenu.item>
                </flux:navmenu>
            </flux:dropdown>
        </flux:navbar>
        <flux:spacer />
        <flux:navbar class="me-4">
            <flux:navbar.item icon="magnifying-glass" href="#" label="Search" />
            <livewire:layout.language-switcher wire:key="header-language-switcher" />
            <flux:navbar.item icon="globe-alt" :href="route('home')" target="_blank" label="{{ __('Visit Website') }}" />
            <flux:button type="button" variant="ghost" icon="cog-6-tooth" class="max-lg:hidden" x-on:click="settingsOpen = true" aria-label="{{ __('Open settings') }}" />
        </flux:navbar>

        <flux:dropdown align="end">
            <flux:profile
                :initials="auth()->user()->initials()"
                :avatar="filled(auth()->user()->picture) ? asset('storage/' . auth()->user()->picture) : null"
            />

            <flux:menu class="min-w-72">
                <div class="px-3 py-3">
                    <div class="flex items-start gap-3">
                        <div class="min-w-0 flex-1">
                            <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                            <flux:text size="sm" class="truncate">{{ auth()->user()->email }}</flux:text>
                            <div class="mt-2 flex flex-wrap gap-1.5">
{{--                                <flux:badge color="zinc" size="sm">{{ __('PF No:') }} {{ auth()->user()->pf_no ?? 'N/A' }}</flux:badge>--}}
                            </div>
                        </div>
                    </div>
                </div>

                <flux:menu.separator />

                <flux:menu.item :href="route('profile.edit')" icon="user" wire:navigate>
                    {{ __('Profile Settings') }}
                </flux:menu.item>
                <flux:menu.item :href="route('security.edit')" icon="shield-check" wire:navigate>
                    {{ __('Security') }}
                </flux:menu.item>
                <flux:menu.item :href="route('appearance.edit')" icon="paint-brush" wire:navigate>
                    {{ __('Appearance') }}
                </flux:menu.item>
                <flux:menu.item icon="cog-6-tooth" x-on:click="settingsOpen = true">
                    {{ __('Quick Settings') }}
                </flux:menu.item>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button
                        type="submit"
                        class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-left text-sm text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/30"
                        data-test="logout-button"
                    >
                        <flux:icon.arrow-right-start-on-rectangle class="size-4" />
                        <span>{{ __('Log out') }}</span>
                    </button>
                </form>
            </flux:menu>
        </flux:dropdown>

    </flux:header>

    <flux:sidebar sticky collapsible class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
            <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
        </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                @if(auth()->user()->isAdmin() && auth()->user()->can('colleges.view'))
                    <flux:sidebar.item icon="building-library" :href="route('colleges.manage')" :current="request()->routeIs('colleges.*')" wire:navigate>{{ __('College Management') }}</flux:sidebar.item>
                @endif

                @if((auth()->user()->isAdmin() || (auth()->user()->hasRole('principal') && auth()->user()->isApproved())) && auth()->user()->can('teachers.view'))
                    <flux:sidebar.item icon="user-group" :href="route('teachers.manage')" :current="request()->routeIs('teachers.*')" wire:navigate>
                        {{ __('Teacher Management') }}
                    </flux:sidebar.item>
                @php($principalTeacherId = auth()->user()->teacherProfile?->id)
                @if(auth()->user()->hasRole('principal') && $principalTeacherId)
                    <flux:sidebar.item icon="identification" :href="route('teachers.show', $principalTeacherId)" :current="request()->routeIs('teachers.show') && (int) request()->route('teacher')?->id === $principalTeacherId" wire:navigate>{{ __('My Profile') }}</flux:sidebar.item>
                @endif
                @elseif(auth()->user()->hasRole('teacher'))
                    @if(auth()->user()->teacherProfile?->approval_status === \App\Enums\ApprovalStatus::Approved && auth()->user()->can('teachers.view'))
                        <flux:sidebar.item icon="user" :href="route('teachers.show', auth()->user()->teacherProfile)" :current="request()->routeIs('teachers.show')" wire:navigate>{{ __('My Profile') }}</flux:sidebar.item>
                    @elseif(auth()->user()->teacherProfile?->approval_status === \App\Enums\ApprovalStatus::Rejected && auth()->user()->can('teachers.create'))
                        <flux:sidebar.item icon="pencil-square" :href="route('teachers.resubmit', auth()->user()->teacherProfile)" :current="request()->routeIs('teachers.resubmit')" wire:navigate>{{ __('সংশোধন করে পুনরায় জমা দিন') }}</flux:sidebar.item>
                    @elseif(auth()->user()->teacherProfile)
                        <div class="px-3 py-2 text-sm text-amber-600">{{ __('Profile needs attention') }}</div>
                    @elseif(auth()->user()->can('teachers.create'))
                        <flux:sidebar.item icon="user-plus" :href="route('teachers.create')" :current="request()->routeIs('teachers.create')" wire:navigate>{{ __('Create Profile') }}</flux:sidebar.item>
                    @endif
                @endif

                @can('reference-data.manage')
                <flux:sidebar.group expandable icon="cog-8-tooth" :heading="__('Academic & Teacher Settings')" expandable :expanded="request()->routeIs('reference-data.manage')" class="grid">
                    <flux:sidebar.item icon="book-open" :href="route('reference-data.manage', 'subjects')" :current="request()->routeIs('reference-data.manage') && request()->route('type') === 'subjects'" wire:navigate>{{ __('Subjects') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="academic-cap" :href="route('reference-data.manage', 'courses')" :current="request()->routeIs('reference-data.manage') && request()->route('type') === 'courses'" wire:navigate>{{ __('Courses') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="briefcase" :href="route('reference-data.manage', 'designations')" :current="request()->routeIs('reference-data.manage') && request()->route('type') === 'designations'" wire:navigate>{{ __('Designation') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="academic-cap" :href="route('reference-data.manage', 'teacher-levels')" :current="request()->routeIs('reference-data.manage') && request()->route('type') === 'teacher-levels'" wire:navigate>{{ __('Teacher Level') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="identification" :href="route('reference-data.manage', 'employments')" :current="request()->routeIs('reference-data.manage') && request()->route('type') === 'employments'" wire:navigate>{{ __('Employment Type') }}</flux:sidebar.item>
                </flux:sidebar.group>
                @endcan
                <flux:sidebar.group expandable icon="academic-cap" :heading="__('Training')" :expanded="request()->routeIs('training.*', 'training-catalog.manage')" class="grid">
                    <flux:sidebar.item icon="calendar-days" :href="route('training.calendar')" :current="request()->routeIs('training.calendar')" wire:navigate>{{ __('Training Calendar') }}</flux:sidebar.item>
                    @can('training-catalog.manage')
                        <flux:sidebar.item icon="presentation-chart-bar" :href="route('training.manage')" :current="request()->routeIs('training.manage')" wire:navigate>{{ __('Training Management') }}</flux:sidebar.item>
                        <flux:sidebar.item icon="user-group" :href="route('training.registrations')" :current="request()->routeIs('training.registrations')" wire:navigate>{{ __('Registered Teachers') }}</flux:sidebar.item>
                        <flux:sidebar.item icon="list-bullet" :href="route('training-catalog.manage')" :current="request()->routeIs('training-catalog.manage')" wire:navigate>{{ __('Training Catalog') }}</flux:sidebar.item>
                    @endcan
                    @can('reports.view')
                        <flux:sidebar.item icon="chart-bar" :href="route('ict.summary')" :current="request()->routeIs('ict.summary')" wire:navigate>{{ __('ICT Training Summary') }}</flux:sidebar.item>
                    @endcan
                </flux:sidebar.group>
                @can('roles.manage')
                    <flux:sidebar.item icon="shield-check" :href="route('roles-permissions.manage')" :current="request()->routeIs('roles-permissions.manage')" wire:navigate>{{ __('Roles & Permissions') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="cog-6-tooth" :href="route('system-settings.manage')" :current="request()->routeIs('system-settings.manage')" wire:navigate>{{ __('System Settings') }}</flux:sidebar.item>
                @endcan
                @can('reports.view')
                <flux:sidebar.item icon="computer-desktop" :href="route('lab.summary')" :current="request()->routeIs('lab.summary')" wire:navigate>
                    {{ __('Lab Summary') }}
                </flux:sidebar.item>

                @endcan
                @if(auth()->user()->isAdmin() )
                    <flux:sidebar.item icon="language" :href="route('admin.language_settings')" :current="request()->routeIs('admin.language_settings')" wire:navigate>
                        {{ __('Language Settings') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="language" :href="route('admin.survey.report')" :current="request()->routeIs('admin.survey.report')" wire:navigate>
                        {{ __('Survey Report') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="identification" :href="route('admission.summary')" :current="request()->routeIs('admission.summary')" wire:navigate>
                        {{ __('Admission Summary') }}
                    </flux:sidebar.item>
                @endif
                @if(auth()->user()->hasRole('principal') && auth()->user()->isApproved())
                    <flux:sidebar.item icon="building-library" :href="route('colleges.show', auth()->user()->college_id)" :current="request()->routeIs('colleges.show', 'colleges.edit') && (int) request()->route('college')?->id === auth()->user()->college_id" wire:navigate>{{ __('College Profile') }}</flux:sidebar.item>
                @elseif(auth()->user()->hasRole('principal'))
                    <div class="px-3 py-2 text-sm text-amber-600">{{ __('Awaiting approval') }}</div>
                @endif

            </flux:sidebar.nav>

            <flux:spacer />

{{--            <flux:sidebar.nav>--}}
{{--                <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">--}}
{{--                    {{ __('Repository') }}--}}
{{--                </flux:sidebar.item>--}}

{{--                <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">--}}
{{--                    {{ __('Documentation') }}--}}
{{--                </flux:sidebar.item>--}}
{{--            </flux:sidebar.nav>--}}

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
    <flux:header class="hidden">
        <flux:sidebar.toggle class="hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
