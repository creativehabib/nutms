<div class="space-y-6">
    <div class="flex items-start gap-3 border-b border-zinc-200 px-5 py-5 dark:border-zinc-700 sm:px-7">
        <div class="rounded-xl bg-indigo-50 p-2.5 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300">
            <flux:icon.arrow-up-tray class="size-6" />
        </div>
        <div>
            <flux:heading size="xl">শিক্ষকের ডেটা ইম্পোর্ট</flux:heading>
            <flux:text class="mt-1">Excel অথবা CSV ফাইল থেকে একসঙ্গে শিক্ষক তথ্য যুক্ত করুন।</flux:text>
        </div>
    </div>

    <div class="space-y-5 px-5 pb-6 sm:px-7">
        @if($message)
            <flux:callout :variant="match($messageType) { 'success' => 'success', 'warning' => 'warning', default => 'danger' }" :heading="$message" />
        @endif

        <form wire:submit="import" class="space-y-5">
            <flux:card class="border-2 border-dashed text-center transition hover:border-indigo-400 dark:hover:border-indigo-600">
                <div class="mx-auto flex max-w-lg flex-col items-center gap-3 py-3">
                    <div class="rounded-full bg-indigo-50 p-3 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300">
                        <flux:icon.document-arrow-up class="size-7" />
                    </div>
                    <div>
                        <flux:heading size="lg">Excel বা CSV ফাইল নির্বাচন করুন</flux:heading>
                        <flux:text class="mt-1">সর্বোচ্চ ১০ MB — XLSX, XLS অথবা CSV</flux:text>
                    </div>
                    <flux:input id="teacher-import-file" type="file" wire:model="file" accept=".csv,.xlsx,.xls" />
                    <flux:text wire:loading wire:target="file" class="text-indigo-600 dark:text-indigo-300">ফাইল প্রস্তুত হচ্ছে...</flux:text>
                    <flux:error name="file" />
                </div>
            </flux:card>

            <flux:button type="submit" variant="primary" icon="arrow-up-tray" class="w-full" wire:loading.attr="disabled" wire:target="import">
                <span wire:loading.remove wire:target="import">ডেটা ইম্পোর্ট করুন</span>
                <span wire:loading wire:target="import">ইম্পোর্ট হচ্ছে...</span>
            </flux:button>
        </form>
    </div>
</div>
