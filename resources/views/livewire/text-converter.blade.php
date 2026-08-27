<section class="mt-8 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-6" aria-labelledby="bulk-converter-title">
    <div class="flex items-start gap-3">
        <div class="theme-primary-soft theme-primary-text flex size-11 shrink-0 items-center justify-center rounded-xl">
            <flux:icon.document-text class="size-6" />
        </div>
        <div>
            <flux:heading id="bulk-converter-title" size="lg">TXT ফাইল কনভার্টার</flux:heading>
            <flux:text class="mt-1">সর্বোচ্চ ১০ মেগাবাইটের একটি .txt ফাইল কনভার্ট করে ডাউনলোড করুন।</flux:text>
        </div>
    </div>

    <form wire:submit="convertFile" class="mt-6 grid items-end gap-5 lg:grid-cols-[auto_1fr_auto]">
        <flux:radio.group wire:model="fileConversionMode" label="কনভার্সনের ধরন" variant="segmented">
            <flux:radio value="bijoy_to_unicode" label="বিজয় → ইউনিকোড" />
            <flux:radio value="unicode_to_bijoy" label="ইউনিকোড → বিজয়" />
        </flux:radio.group>

        <flux:field>
            <flux:label>TXT ফাইল</flux:label>
            <input type="file" wire:model="document" accept=".txt,text/plain" required class="block w-full rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-600 file:mr-4 file:border-0 file:bg-emerald-50 file:px-4 file:py-3 file:font-bold file:text-emerald-700 hover:file:bg-emerald-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300 dark:file:bg-emerald-500/10 dark:file:text-emerald-300" />
            <flux:error name="document" />
        </flux:field>

        <flux:button type="submit" variant="primary" icon="arrow-down-tray" wire:loading.attr="disabled" wire:target="document,convertFile" class="theme-primary-bg">
            <span wire:loading.remove wire:target="convertFile">কনভার্ট ও ডাউনলোড</span>
            <span wire:loading wire:target="convertFile">কনভার্ট হচ্ছে…</span>
        </flux:button>
    </form>
</section>
