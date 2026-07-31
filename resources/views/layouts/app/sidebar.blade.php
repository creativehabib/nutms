<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                @if(auth()->user()->isAdmin())
                <flux:sidebar.item icon="presentation-chart-bar" :href="route('training-catalog.manage')" :current="request()->routeIs('training-catalog.manage')" wire:navigate>
                    ট্রেনিং ক্যাটালগ
                </flux:sidebar.item>
                <flux:sidebar.item icon="building-library" :href="route('colleges.manage')" :current="request()->routeIs('colleges.*')" wire:navigate>
                    কলেজ ব্যবস্থাপনা
                </flux:sidebar.item>
                <flux:sidebar.item icon="shield-check" :href="route('roles-permissions.manage')" :current="request()->routeIs('roles-permissions.manage')" wire:navigate>
                    রোলস ও পারমিশন
                </flux:sidebar.item>
                @endif
                @if(auth()->user()->isAdmin() || (auth()->user()->role === \App\Enums\UserRole::Principal && auth()->user()->isApproved()))
                <flux:sidebar.item icon="user-group" :href="route('teachers.manage')" :current="request()->routeIs('teachers.*')" wire:navigate>
                    {{ __('Teacher Management') }}
                </flux:sidebar.item>
                @if(auth()->user()->role === \App\Enums\UserRole::Principal && auth()->user()->teacher_id)
                    <flux:sidebar.item icon="identification" :href="route('teachers.edit', auth()->user()->teacher_id)" :current="request()->routeIs('teachers.edit') && (int) request()->route('teacher')?->id === auth()->user()->teacher_id" wire:navigate>
                        আমার শিক্ষক প্রোফাইল
                    </flux:sidebar.item>
                @endif
                @elseif(auth()->user()->role === \App\Enums\UserRole::Teacher)
                    @if(auth()->user()->teacher?->approval_status === \App\Enums\ApprovalStatus::Approved)
                        <flux:sidebar.item icon="user" :href="route('teachers.show', auth()->user()->teacher_id)" :current="request()->routeIs('teachers.show')" wire:navigate>আমার প্রোফাইল</flux:sidebar.item>
                    @elseif(auth()->user()->teacher_id)
                        <div class="px-3 py-2 text-sm text-amber-600">প্রোফাইল অনুমোদনের অপেক্ষায়</div>
                    @else
                        <flux:sidebar.item icon="user-plus" :href="route('teachers.create')" :current="request()->routeIs('teachers.create')" wire:navigate>প্রোফাইল তৈরি</flux:sidebar.item>
                    @endif
                @endif

                @if(auth()->user()->isAdmin())
                <flux:sidebar.group heading="শিক্ষক সেটিংস" expandable :expanded="request()->routeIs('reference-data.manage')" class="grid">
                    <flux:sidebar.item icon="book-open" :href="route('reference-data.manage', 'subjects')" :current="request()->routeIs('reference-data.manage') && request()->route('type') === 'subjects'" wire:navigate>সাবজেক্ট</flux:sidebar.item>
                    <flux:sidebar.item icon="briefcase" :href="route('reference-data.manage', 'designations')" :current="request()->routeIs('reference-data.manage') && request()->route('type') === 'designations'" wire:navigate>পদবি</flux:sidebar.item>
                    <flux:sidebar.item icon="academic-cap" :href="route('reference-data.manage', 'teacher-levels')" :current="request()->routeIs('reference-data.manage') && request()->route('type') === 'teacher-levels'" wire:navigate>শিক্ষক স্তর</flux:sidebar.item>
                    <flux:sidebar.item icon="identification" :href="route('reference-data.manage', 'employments')" :current="request()->routeIs('reference-data.manage') && request()->route('type') === 'employments'" wire:navigate>চাকরির ধরন</flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.item icon="computer-desktop" :href="route('lab.summary')" :current="request()->routeIs('lab.summary')" wire:navigate>
                    {{ __('Lab Summary') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="academic-cap" :href="route('ict.summary')" :current="request()->routeIs('ict.summary')" wire:navigate>
                    {{ __('ICT Training Summary') }}
                </flux:sidebar.item>
                @elseif(auth()->user()->role === \App\Enums\UserRole::Principal && auth()->user()->isApproved())
                    <flux:sidebar.item icon="building-library" :href="route('colleges.manage')" :current="request()->routeIs('colleges.*')" wire:navigate>আমার কলেজ</flux:sidebar.item>
                @elseif(auth()->user()->role === \App\Enums\UserRole::Principal)
                    <div class="px-3 py-2 text-sm text-amber-600">Principal account অনুমোদনের অপেক্ষায়</div>
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
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

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
