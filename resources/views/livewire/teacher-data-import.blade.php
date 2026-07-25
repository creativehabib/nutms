<div>
    <div class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 px-5 py-5 sm:px-7">
        <div class="flex items-center gap-3 pr-10">
            <span class="inline-flex size-11 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 18a4.6 4.6 0 0 1-.9-9.1A6 6 0 0 1 17.7 7a4 4 0 0 1 .3 8h-1m-4-6-3 3m0 0-3-3m3 3v8"></path></svg>
            </span>
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">শিক্ষকের ডেটা ইম্পোর্ট</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Excel অথবা CSV ফাইল থেকে একসঙ্গে তথ্য যুক্ত করুন।</p>
            </div>
        </div>
    </div>

    <div class="space-y-5 px-5 py-6 sm:px-7">

    @if($message)
        <div @class([
            'rounded-lg border px-4 py-3 text-sm font-medium',
            'bg-green-100 text-green-700 dark:border-green-900 dark:bg-green-950/60 dark:text-green-300' => $messageType === 'success',
            'bg-amber-100 text-amber-800 dark:border-amber-900 dark:bg-amber-950/60 dark:text-amber-300' => $messageType === 'warning',
            'bg-red-100 text-red-700 dark:border-red-900 dark:bg-red-950/60 dark:text-red-300' => $messageType === 'error',
        ]) role="alert">
            {{ $message }}
        </div>
    @endif

    <form wire:submit.prevent="import" class="space-y-5">
        <div class="rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-950 p-5 text-center transition hover:border-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 sm:p-7">
            <label for="teacher-import-file" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Excel বা CSV ফাইল নির্বাচন করুন</label>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">সর্বোচ্চ ১০ MB — XLSX, XLS অথবা CSV</p>
            <input id="teacher-import-file" type="file" wire:model="file" class="mt-4 block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-sm text-slate-500 dark:text-slate-400 shadow-sm
                file:mr-4 file:border-0 file:border-r file:border-slate-200 dark:file:border-slate-700 file:px-4 file:py-2.5
                file:rounded-l-lg
                file:text-sm file:font-semibold
                file:bg-indigo-50 file:text-indigo-700
                hover:file:bg-indigo-100" accept=".csv, .xlsx, .xls">
            <div wire:loading wire:target="file" class="mt-3 text-xs font-medium text-indigo-600">ফাইল প্রস্তুত হচ্ছে...</div>
            @error('file') <span class="mt-2 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
        </div>

        <button type="submit" wire:loading.attr="disabled" wire:target="import" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-transparent bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60">
            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16V4m0 0L7 9m5-5 5 5M5 20h14"></path></svg>
            <span wire:loading.remove wire:target="import">ডেটা ইম্পোর্ট করুন</span>
            <span wire:loading wire:target="import">ইম্পোর্ট হচ্ছে...</span>
        </button>
    </form>
    </div>
</div>
