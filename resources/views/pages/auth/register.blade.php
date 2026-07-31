<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6" x-data="{ accountRole: @js(old('role', '')) }">
            @csrf
            <!-- Name -->
            <flux:input
                name="name"
                :label="__('Name')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Full name')"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />

            <flux:select name="role" :label="__('Account type')" x-model="accountRole" required>
                <option value="">{{ __('Select account type') }}</option>
                <option value="principal" @selected(old('role') === 'principal')>কলেজ প্রিন্সিপাল</option>
                <option value="teacher" @selected(old('role') === 'teacher')>শিক্ষক</option>
            </flux:select>

            <div x-show="accountRole === 'principal'" x-cloak>
                <flux:select name="college_id" label="কলেজ নির্বাচন করুন" :required="old('role') === 'principal'">
                    <option value="">কলেজ নির্বাচন করুন</option>
                    @foreach($colleges as $college)
                        <option value="{{ $college->id }}" @selected((string) old('college_id') === (string) $college->id)>{{ $college->code ? $college->code.' — ' : '' }}{{ $college->name }}</option>
                    @endforeach
                </flux:select>
                <flux:text class="mt-2">একটি কলেজ শুধু একজন প্রিন্সিপাল account-এর সাথে যুক্ত করা যাবে।</flux:text>
            </div>

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Create account') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
