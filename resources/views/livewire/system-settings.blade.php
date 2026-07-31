<div class="mx-auto w-full max-w-3xl space-y-6 p-4 sm:p-6">
    <div>
        <flux:heading size="xl">সিস্টেম সেটিংস</flux:heading>
        <flux:text>শিক্ষকদের অবসর সংক্রান্ত কেন্দ্রীয় নিয়ম নির্ধারণ করুন।</flux:text>
    </div>

    <form wire:submit="save">
        <flux:card class="space-y-5">
            <div>
                <flux:heading size="lg">শিক্ষকের অবসর বয়স</flux:heading>
                <flux:text class="mt-1">জন্ম তারিখ থেকে এই বয়স পূর্ণ হওয়ার দিনে শিক্ষককে অবসরপ্রাপ্ত হিসেবে গণনা করা হবে।</flux:text>
            </div>
            <flux:input wire:model="retirementAge" type="number" min="50" max="70" label="অবসর বয়স (বছর)" required />
            <div class="flex justify-end"><flux:button type="submit" variant="primary">সংরক্ষণ করুন</flux:button></div>
        </flux:card>
    </form>
</div>
