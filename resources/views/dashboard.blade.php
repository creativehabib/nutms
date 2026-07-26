<x-layouts::app :title="__('Dashboard')">
    <div class="flex w-full flex-1 flex-col gap-6">
        <div>
            <flux:heading size="xl">ড্যাশবোর্ড রিপোর্ট</flux:heading>
            <flux:text class="mt-2">কলেজের কম্পিউটার ল্যাব ও শিক্ষকদের আইসিটি ট্রেনিংয়ের সারসংক্ষেপ</flux:text>
        </div>

        <section aria-labelledby="college-report-heading" class="flex flex-col gap-4">
            <div class="flex items-center justify-between gap-4">
                <flux:heading id="college-report-heading" size="lg">কম্পিউটার ল্যাব রিপোর্ট</flux:heading>
                <flux:button :href="route('lab.summary')" variant="ghost" size="sm" wire:navigate>বিস্তারিত দেখুন</flux:button>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm dark:border-emerald-900 dark:bg-emerald-950/40">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-emerald-700 dark:text-emerald-300">কম্পিউটার ল্যাব আছে</p>
                            <p class="mt-2 text-4xl font-bold text-emerald-950 dark:text-emerald-50">{{ number_format($report['collegesWithLab']) }}</p>
                            <p class="mt-1 text-sm text-emerald-700 dark:text-emerald-300">টি কলেজ</p>
                        </div>
                        <div class="rounded-full bg-emerald-100 p-3 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-200">
                            <flux:icon.check-circle class="size-7" />
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-rose-200 bg-rose-50 p-5 shadow-sm dark:border-rose-900 dark:bg-rose-950/40">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-rose-700 dark:text-rose-300">কম্পিউটার ল্যাব নেই</p>
                            <p class="mt-2 text-4xl font-bold text-rose-950 dark:text-rose-50">{{ number_format($report['collegesWithoutLab']) }}</p>
                            <p class="mt-1 text-sm text-rose-700 dark:text-rose-300">টি কলেজ</p>
                        </div>
                        <div class="rounded-full bg-rose-100 p-3 text-rose-700 dark:bg-rose-900 dark:text-rose-200">
                            <flux:icon.x-circle class="size-7" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="mb-3 flex items-center justify-between gap-4 text-sm">
                    <span class="font-medium text-zinc-700 dark:text-zinc-300">মোট কলেজ</span>
                    <span class="font-bold text-zinc-950 dark:text-white">{{ number_format($report['totalColleges']) }}</span>
                </div>
                <div class="flex h-3 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800" aria-label="কলেজের ল্যাব অনুপাত">
                    <div class="bg-emerald-500" style="width: {{ $report['totalColleges'] > 0 ? ($report['collegesWithLab'] / $report['totalColleges']) * 100 : 0 }}%"></div>
                    <div class="bg-rose-500" style="width: {{ $report['totalColleges'] > 0 ? ($report['collegesWithoutLab'] / $report['totalColleges']) * 100 : 0 }}%"></div>
                </div>
            </div>
        </section>

        <section aria-labelledby="training-report-heading" class="flex flex-col gap-4">
            <div class="flex items-center justify-between gap-4">
                <flux:heading id="training-report-heading" size="lg">আইসিটি ট্রেনিং রিপোর্ট</flux:heading>
                <flux:button :href="route('ict.summary')" variant="ghost" size="sm" wire:navigate>বিস্তারিত দেখুন</flux:button>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl border border-sky-200 bg-sky-50 p-5 shadow-sm dark:border-sky-900 dark:bg-sky-950/40">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-sky-700 dark:text-sky-300">আইসিটি ট্রেনিং প্রাপ্ত শিক্ষক</p>
                            <p class="mt-2 text-4xl font-bold text-sky-950 dark:text-sky-50">{{ number_format($report['teachersWithIctTraining']) }}</p>
                            <p class="mt-1 text-sm text-sky-700 dark:text-sky-300">জন শিক্ষক</p>
                        </div>
                        <div class="rounded-full bg-sky-100 p-3 text-sky-700 dark:bg-sky-900 dark:text-sky-200">
                            <flux:icon.academic-cap class="size-7" />
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 shadow-sm dark:border-amber-900 dark:bg-amber-950/40">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-amber-700 dark:text-amber-300">আইসিটি ট্রেনিং বিহীন শিক্ষক</p>
                            <p class="mt-2 text-4xl font-bold text-amber-950 dark:text-amber-50">{{ number_format($report['teachersWithoutIctTraining']) }}</p>
                            <p class="mt-1 text-sm text-amber-700 dark:text-amber-300">জন শিক্ষক</p>
                        </div>
                        <div class="rounded-full bg-amber-100 p-3 text-amber-700 dark:bg-amber-900 dark:text-amber-200">
                            <flux:icon.user-group class="size-7" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="mb-3 flex items-center justify-between gap-4 text-sm">
                    <span class="font-medium text-zinc-700 dark:text-zinc-300">মোট শিক্ষক</span>
                    <span class="font-bold text-zinc-950 dark:text-white">{{ number_format($report['totalTeachers']) }}</span>
                </div>
                <div class="flex h-3 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800" aria-label="শিক্ষকদের আইসিটি ট্রেনিং অনুপাত">
                    <div class="bg-sky-500" style="width: {{ $report['totalTeachers'] > 0 ? ($report['teachersWithIctTraining'] / $report['totalTeachers']) * 100 : 0 }}%"></div>
                    <div class="bg-amber-500" style="width: {{ $report['totalTeachers'] > 0 ? ($report['teachersWithoutIctTraining'] / $report['totalTeachers']) * 100 : 0 }}%"></div>
                </div>
            </div>
        </section>
    </div>
</x-layouts::app>
