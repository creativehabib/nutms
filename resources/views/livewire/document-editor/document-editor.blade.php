@push('styles')
    <link rel="stylesheet" href="https://fonts.maateen.me/sutonny-mj/font.css">
@endpush

<div data-document-editor class="document-studio min-h-screen bg-[#eef1f5] text-slate-800 dark:bg-slate-950 dark:text-slate-100" style="--page-width:210mm;--page-height:297mm;--margin-top:20mm;--margin-right:20mm;--margin-bottom:20mm;--margin-left:20mm;--editor-scale:1">
    <style data-print-rules></style>

    <header class="document-chrome sticky top-0 z-40 border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur dark:border-slate-800 dark:bg-slate-900/95">
        <div class="mx-auto flex max-w-[1600px] items-center gap-3 px-4 py-2.5">
            <a href="{{ route('tools.index') }}" class="grid size-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-700 text-white shadow-lg shadow-emerald-500/20" aria-label="টুলস পাতায় ফিরুন">
                <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
            <div class="min-w-0 flex-1">
                <input data-document-title value="শিরোনামহীন ডকুমেন্ট" aria-label="ডকুমেন্টের নাম" class="w-full truncate border-0 bg-transparent p-0 text-base font-bold text-slate-900 outline-none focus:ring-0 dark:text-white sm:text-lg">
                <div class="mt-0.5 flex items-center gap-2 text-[11px] text-slate-500 dark:text-slate-400">
                    <span class="inline-flex items-center gap-1.5"><span class="size-1.5 rounded-full bg-emerald-500"></span><span data-save-status data-state="saved">সব পরিবর্তন সংরক্ষিত</span></span>
                    <span class="hidden sm:inline">•</span><span class="hidden sm:inline">এই ডিভাইসে অটোসেভ</span>
                </div>
            </div>
            <button type="button" data-fullscreen class="hidden items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800 md:flex">
                <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3M3 16v3a2 2 0 0 0 2 2h3m8 0h3a2 2 0 0 0 2-2v-3"/></svg>ফুলস্ক্রিন
            </button>
            <button type="button" data-print-document class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white shadow-lg transition hover:bg-emerald-700 dark:bg-emerald-500 dark:text-slate-950">
                <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M6 14h12v8H6z"/></svg><span class="hidden sm:inline">প্রিন্ট করুন</span><span class="sm:hidden">প্রিন্ট</span>
            </button>
        </div>

        <div class="editor-toolbar mx-auto my-2 flex max-w-[1568px] flex-wrap items-center gap-1 rounded-xl border border-slate-200 bg-slate-50 px-2 py-1.5 shadow-inner dark:border-slate-700 dark:bg-slate-950" role="toolbar" aria-label="লেখা ফরম্যাটিং টুলবার">
            <button type="button" data-command="undo" class="editor-tool" title="পূর্বাবস্থায় ফিরুন">↶</button>
            <button type="button" data-command="redo" class="editor-tool" title="পুনরায় করুন">↷</button>
            <span class="mx-1 h-6 w-px shrink-0 bg-slate-200 dark:bg-slate-700"></span>
            <select data-format-select="formatBlock" aria-label="লেখার ধরন" class="editor-select w-28"><option value="p">সাধারণ লেখা</option><option value="h1">শিরোনাম ১</option><option value="h2">শিরোনাম ২</option><option value="h3">শিরোনাম ৩</option></select>
            <select data-format-select="fontName" aria-label="ফন্ট" class="editor-select w-36"><option value="Noto Sans Bengali">Noto Sans Bengali</option><option value="SolaimanLipi">SolaimanLipi</option><option value="Arial">Arial</option><option value="Georgia">Georgia</option></select>
            <label class="editor-size-control" title="কাস্টম ফন্ট সাইজ"><input data-custom-font-size type="number" min="8" max="96" value="12" aria-label="ফন্ট সাইজ"><span>pt</span></label>
            <span class="mx-1 h-6 w-px shrink-0 bg-slate-200 dark:bg-slate-700"></span>
            <button type="button" data-command="bold" class="editor-tool font-black" title="বোল্ড">B</button>
            <button type="button" data-command="italic" class="editor-tool font-serif italic" title="ইটালিক">I</button>
            <button type="button" data-command="underline" class="editor-tool underline" title="আন্ডারলাইন">U</button>
            <button type="button" data-command="strikeThrough" class="editor-tool line-through" title="স্ট্রাইকথ্রু">S</button>
            <button type="button" data-command="superscript" class="editor-tool text-xs" title="সুপারস্ক্রিপ্ট">x²</button>
            <button type="button" data-command="subscript" class="editor-tool text-xs" title="সাবস্ক্রিপ্ট">x₂</button>
            <label class="editor-tool relative cursor-pointer" title="লেখার রঙ"><span class="font-bold">A</span><span class="absolute inset-x-2 bottom-1 h-0.5 bg-emerald-500"></span><input data-text-color type="color" value="#111827" class="absolute inset-0 opacity-0"></label>
            <button type="button" data-command="hiliteColor" data-value="#fef08a" class="editor-tool" title="হাইলাইট"><span class="rounded bg-yellow-200 px-1 font-bold text-slate-900">A</span></button>
            <span class="mx-1 h-6 w-px shrink-0 bg-slate-200 dark:bg-slate-700"></span>
            <button type="button" data-command="justifyLeft" class="editor-tool" title="বামে অ্যালাইন">≡←</button>
            <button type="button" data-command="justifyCenter" class="editor-tool" title="মাঝে অ্যালাইন">≡</button>
            <button type="button" data-command="justifyRight" class="editor-tool" title="ডানে অ্যালাইন">→≡</button>
            <button type="button" data-command="justifyFull" class="editor-tool" title="জাস্টিফাই">☰</button>
            <button type="button" data-command="insertUnorderedList" class="editor-tool" title="বুলেট তালিকা">• তালিকা</button>
            <button type="button" data-command="insertOrderedList" class="editor-tool" title="নম্বর তালিকা">1. তালিকা</button>
            <select data-spacing-property="lineHeight" aria-label="Line spacing" title="Line spacing" class="editor-select w-[6.5rem]">
                <option value="1">Line 1.0</option><option value="1.15">Line 1.15</option><option value="1.5">Line 1.5</option><option value="1.6" selected>Line 1.6</option><option value="2">Line 2.0</option><option value="2.5">Line 2.5</option><option value="3">Line 3.0</option>
            </select>
            <select data-spacing-property="marginBottom" aria-label="Paragraph spacing" title="Paragraph spacing after" class="editor-select w-[7.25rem]">
                <option value="0pt">After 0 pt</option><option value="4pt">After 4 pt</option><option value="6pt" selected>After 6 pt</option><option value="8pt">After 8 pt</option><option value="12pt">After 12 pt</option><option value="18pt">After 18 pt</option><option value="24pt">After 24 pt</option>
            </select>
            <button type="button" data-command="outdent" class="editor-tool" title="ইনডেন্ট কমান">⇤</button>
            <button type="button" data-command="indent" class="editor-tool" title="ইনডেন্ট বাড়ান">⇥</button>
            <span class="mx-1 h-6 w-px shrink-0 bg-slate-200 dark:bg-slate-700"></span>
            <button type="button" data-insert-link class="editor-tool" title="লিংক যোগ করুন">🔗</button>
            <button type="button" data-open-table class="editor-tool" title="Insert custom table">▦ Table</button>
            <button type="button" data-table-only hidden data-table-action="add-row" class="editor-tool" title="Insert row below">＋ Row</button>
            <button type="button" data-table-only hidden data-table-action="add-column" class="editor-tool" title="Insert column right">＋ Column</button>
            <button type="button" data-table-only hidden data-table-action="delete-row" class="editor-tool text-red-500" title="Delete current row">− Row</button>
            <button type="button" data-table-only hidden data-table-action="delete-column" class="editor-tool text-red-500" title="Delete current column">− Column</button>
            <button type="button" data-table-only hidden data-table-action="delete-table" class="editor-tool text-red-500" title="Delete table">× Table</button>
            <button type="button" data-command="formatBlock" data-value="blockquote" class="editor-tool" title="উদ্ধৃতি">❝</button>
            <button type="button" data-command="insertHorizontalRule" class="editor-tool" title="অনুভূমিক রেখা">―</button>
            <button type="button" data-clear-format class="editor-tool text-xs" title="সব ফরম্যাট মুছুন">Tx</button>
        </div>
    </header>

    <main class="mx-auto grid max-w-[1600px] items-start gap-5 px-3 py-5 lg:grid-cols-[minmax(0,1fr)_310px] lg:px-5">
        <section class="min-w-0 overflow-auto rounded-2xl border border-slate-200 bg-slate-200/70 dark:border-slate-800 dark:bg-slate-900/50">
            <div class="document-chrome flex h-10 items-center justify-between border-b border-slate-300/70 bg-slate-100 px-4 text-xs text-slate-500 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
                <span>প্রিন্ট লেআউট</span>
                <span class="hidden sm:inline">Ctrl + S দিয়ে যেকোনো সময় সংরক্ষণ করুন</span>
            </div>
            <div class="page-stage min-h-[70vh] overflow-auto p-5 sm:p-10">
                <div data-document-pages class="document-pages flex flex-col items-center gap-8"></div>
                <button type="button" data-add-page class="document-chrome mx-auto mt-8 flex items-center gap-2 rounded-xl border border-dashed border-emerald-400 bg-white px-5 py-3 text-sm font-bold text-emerald-700 shadow-sm transition hover:bg-emerald-50 dark:bg-slate-900 dark:text-emerald-300">＋ নতুন পৃষ্ঠা যোগ করুন</button>
            </div>
            <footer class="document-chrome flex flex-wrap items-center justify-between gap-3 border-t border-slate-300/70 bg-white px-4 py-2 text-xs text-slate-500 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
                <div class="flex items-center gap-4"><span data-page-count>১ পৃষ্ঠা</span><span><strong data-word-count class="text-slate-700 dark:text-slate-200">০</strong> শব্দ</span><span><strong data-character-count class="text-slate-700 dark:text-slate-200">০</strong> অক্ষর</span></div>
                <div class="flex items-center gap-2"><span class="hidden sm:inline">জুম</span><input data-zoom type="range" min="60" max="120" value="100" class="h-1.5 w-24 accent-emerald-600"><span data-zoom-label class="w-9 font-semibold">100%</span></div>
            </footer>
        </section>

        <aside class="document-chrome flex flex-col gap-4 lg:sticky lg:top-[130px]">
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div><h2 class="font-bold">বাংলা টাইপিং</h2><p class="text-[11px] text-slate-500">ইউনিকোড অথবা বিজয় সুতনি</p></div>
                    <button type="button" data-open-converter class="rounded-lg bg-emerald-50 px-2.5 py-1.5 text-[11px] font-bold text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-950 dark:text-emerald-300">কনভার্টার</button>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <label class="typing-card"><input type="radio" name="document-typing-mode" value="unicode" checked class="peer sr-only"><span class="typing-option"><strong>অভ্র / ইউনিকোড</strong><small>Noto Sans Bengali</small></span></label>
                    <label class="typing-card"><input type="radio" name="document-typing-mode" value="bijoy" class="peer sr-only"><span class="typing-option"><strong>বিজয় সুতনি</strong><small>SutonnyMJ</small></span></label>
                </div>
                <label class="mt-3 flex items-center justify-between gap-3 rounded-xl bg-slate-50 p-3 text-xs font-bold dark:bg-slate-950"><span>বিল্ট-ইন কিবোর্ড</span><input data-built-in-keyboard type="checkbox" checked class="size-4 accent-emerald-600"></label>
                <p class="mt-2 text-[10px] leading-4 text-slate-500">অভ্র মোডে ইংরেজি অক্ষরে লিখে Space দিন; বিজয় মোডে SutonnyMJ layout সরাসরি কাজ করবে—কম্পিউটারে আলাদা সফটওয়্যার লাগবে না।</p>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-5 flex items-center gap-3"><span class="grid size-9 place-items-center rounded-xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">▤</span><div><h2 class="font-bold">পেইজ সেটআপ</h2><p class="text-[11px] text-slate-500">প্রিন্টের মাপ ও বিন্যাস</p></div></div>
                <div class="space-y-4">
                    <label class="block"><span class="editor-label">কাগজের সাইজ</span><select data-paper-size class="setup-input"><option value="A4">A4 — 210 × 297 mm</option><option value="Letter">Letter — 8.5 × 11 in</option><option value="Legal">Legal — 8.5 × 14 in</option></select></label>
                    <fieldset><legend class="editor-label">ওরিয়েন্টেশন</legend><div class="grid grid-cols-2 gap-2"><label class="orientation-card"><input type="radio" name="document-orientation" value="portrait" checked class="peer sr-only"><span class="orientation-option"><i class="h-6 w-4 rounded-sm border-2 border-current"></i>পোর্ট্রেট</span></label><label class="orientation-card"><input type="radio" name="document-orientation" value="landscape" class="peer sr-only"><span class="orientation-option"><i class="h-4 w-6 rounded-sm border-2 border-current"></i>ল্যান্ডস্কেপ</span></label></div></fieldset>
                    <fieldset><legend class="editor-label">মার্জিন (মিলিমিটার)</legend><div class="grid grid-cols-2 gap-2"><label class="margin-field">উপরে<input data-margin="top" type="number" min="0" max="60" value="20"></label><label class="margin-field">নিচে<input data-margin="bottom" type="number" min="0" max="60" value="20"></label><label class="margin-field">বামে<input data-margin="left" type="number" min="0" max="60" value="20"></label><label class="margin-field">ডানে<input data-margin="right" type="number" min="0" max="60" value="20"></label></div></fieldset>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h2 class="mb-4 font-bold">হেডার সেটিং</h2>
                <div class="space-y-3">
                    <label class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 p-3 text-sm font-bold dark:bg-slate-950"><span>হেডার দেখাবেন?</span><input data-header-enabled type="checkbox" checked class="size-4 accent-emerald-600"></label>
                    <label class="block"><span class="editor-label">হেডারের লেখা</span><input data-header-text class="setup-input" placeholder="যেমন: গোপনীয় প্রতিবেদন"></label>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h2 class="mb-4 font-bold">ফুটার সেটিং</h2>
                <div class="space-y-3">
                    <label class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 p-3 text-sm font-bold dark:bg-slate-950"><span>ফুটার ও পৃষ্ঠা নম্বর দেখাবেন?</span><input data-footer-enabled type="checkbox" checked class="size-4 accent-emerald-600"></label>
                    <label class="block"><span class="editor-label">ফুটারের লেখা</span><input data-footer-text class="setup-input" placeholder="যেমন: জাতীয় বিশ্ববিদ্যালয়"></label>
                </div>
            </section>

            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-xs leading-5 text-emerald-900 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-200"><strong class="mb-1 block">নিখুঁত প্রিন্টের জন্য</strong>প্রিন্ট ডায়ালগে “Margins: None” এবং “Scale: 100%” রাখুন। ব্রাউজারের Headers and footers বন্ধ করুন।</div>
            <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4 text-xs leading-5 text-sky-900 dark:border-sky-900 dark:bg-sky-950/40 dark:text-sky-200"><strong class="mb-1 block">একাধিক পৃষ্ঠা</strong>“নতুন পৃষ্ঠা যোগ করুন” চাপুন অথবা লেখার সময় <kbd>Ctrl + Enter</kbd> দিন। পৃষ্ঠা পূর্ণ হলে লাল সংকেত দেখাবে।</div>
            <button type="button" data-clear-document class="rounded-xl border border-red-200 bg-white px-4 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-50 dark:border-red-900 dark:bg-slate-900 dark:hover:bg-red-950/30">ডকুমেন্ট পরিষ্কার করুন</button>
        </aside>
    </main>

    <div data-table-context hidden class="document-chrome fixed z-50 flex items-center gap-1 rounded-lg border border-slate-300 bg-white p-1.5 text-xs font-bold text-slate-700 shadow-xl dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200" role="toolbar" aria-label="Table cell tools">
        <button type="button" data-table-context-action="add-row" class="table-context-tool" title="Insert row">＋ Row</button>
        <button type="button" data-table-context-action="add-column" class="table-context-tool" title="Insert column">＋ Column</button>
        <span class="h-6 w-px bg-slate-200 dark:bg-slate-600"></span>
        <button type="button" data-table-context-action="select-cell" class="table-context-tool" title="Select cell">Cell</button>
        <button type="button" data-table-context-action="select-column" class="table-context-tool" title="Select column">Column</button>
        <button type="button" data-table-context-action="merge" class="table-context-tool" title="Merge cells">Merge</button>
        <button type="button" data-table-context-action="split" class="table-context-tool" title="Split cell">Split</button>
        <button type="button" data-table-context-action="delete-selected" class="table-context-tool text-red-600 dark:text-red-400" title="Delete selected cells, rows, or columns">Delete selection</button>
        <button type="button" data-table-context-action="delete-table" class="table-context-tool text-red-600 dark:text-red-400" title="Delete entire table">Delete table</button>
    </div>

    <dialog data-document-converter class="m-auto w-[min(92vw,760px)] rounded-3xl border-0 bg-white p-0 text-slate-900 shadow-2xl backdrop:bg-slate-950/60 dark:bg-slate-900 dark:text-white">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-slate-800">
            <div><h2 class="text-lg font-extrabold">বিজয় ⇄ ইউনিকোড কনভার্টার</h2><p class="text-xs text-slate-500">লেখা রূপান্তর করে সরাসরি ডকুমেন্টে যোগ করুন</p></div>
            <button type="button" data-close-converter class="grid size-9 place-items-center rounded-full bg-slate-100 text-xl hover:bg-slate-200 dark:bg-slate-800">×</button>
        </div>
        <div class="grid gap-4 p-5 md:grid-cols-2">
            <label><span class="editor-label">ইউনিকোড লেখা</span><textarea data-converter-unicode rows="9" class="converter-field" placeholder="ইউনিকোড লেখা লিখুন…"></textarea></label>
            <label><span class="editor-label">বিজয় সুতনি লেখা</span><textarea data-converter-bijoy rows="9" class="converter-field" style="font-family:'SutonnyMJ', sans-serif" placeholder="weRq myZwb‡Z wjLyb…"></textarea></label>
        </div>
        <div class="flex flex-wrap items-center justify-end gap-2 border-t border-slate-200 px-5 py-4 dark:border-slate-800">
            <button type="button" data-convert-to-unicode class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-bold dark:border-slate-700">← ইউনিকোড করুন</button>
            <button type="button" data-convert-to-bijoy class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-bold dark:border-slate-700">বিজয় করুন →</button>
            <button type="button" data-insert-converted class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-bold text-white">ডকুমেন্টে যোগ করুন</button>
        </div>
    </dialog>

    <dialog data-table-dialog class="m-auto w-[min(92vw,420px)] rounded-3xl border-0 bg-white p-0 text-slate-900 shadow-2xl backdrop:bg-slate-950/60 dark:bg-slate-900 dark:text-white">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-slate-800"><h2 class="text-lg font-extrabold">Insert table</h2><button type="button" data-close-table class="grid size-9 place-items-center rounded-full bg-slate-100 text-xl dark:bg-slate-800">×</button></div>
        <div class="grid grid-cols-2 gap-4 p-5">
            <label><span class="editor-label">Rows</span><input data-table-rows type="number" min="1" max="30" value="3" class="setup-input"></label>
            <label><span class="editor-label">Columns</span><input data-table-columns type="number" min="1" max="12" value="3" class="setup-input"></label>
        </div>
        <div class="flex justify-end border-t border-slate-200 px-5 py-4 dark:border-slate-800"><button type="button" data-insert-table class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white">Insert table</button></div>
    </dialog>

    <template data-page-template>
        <div data-page-sheet class="page-scaler relative origin-top">
            <div class="document-chrome absolute -top-6 left-0 right-0 flex items-center justify-between text-[11px] font-bold text-slate-500">
                <span data-page-label>পৃষ্ঠা</span>
                <button type="button" data-delete-page class="text-red-500 disabled:cursor-not-allowed disabled:opacity-30">পৃষ্ঠা মুছুন</button>
            </div>
            <article class="document-page relative flex flex-col bg-white text-slate-900 shadow-[0_8px_35px_rgba(15,23,42,.16)]">
                <header data-page-header class="page-header absolute left-[var(--margin-left)] right-[var(--margin-right)] top-5 border-b border-slate-200 pb-2 text-center text-[10pt] text-slate-500"><span data-header-copy></span></header>
                <div data-editor-canvas contenteditable="true" role="textbox" aria-multiline="true" spellcheck="true" class="editor-canvas min-h-0 flex-1 outline-none" data-placeholder="এখানে লেখা শুরু করুন…"></div>
                <footer data-page-footer class="page-footer absolute bottom-4 left-[var(--margin-left)] right-[var(--margin-right)] flex items-center justify-between border-t border-slate-200 pt-2 text-[9pt] text-slate-400"><span data-footer-copy></span><span data-page-number></span></footer>
            </article>
        </div>
    </template>

    <style>
        .editor-tool { display:flex; height:2.15rem; min-width:2.15rem; flex-shrink:0; align-items:center; justify-content:center; border:1px solid transparent; border-radius:.4rem; padding:0 .5rem; color:#334155; transition:.15s; }
        .editor-tool:hover { border-color:#cbd5e1; background:#fff; color:#0f172a; box-shadow:0 1px 2px rgb(15 23 42 / .08); }
        .editor-tool:active { background:#dbeafe; color:#1d4ed8; }
        .editor-tool.is-on { border-color:#bfdbfe; background:#dbeafe; color:#1d4ed8; }
        .editor-tool:focus-visible, .editor-select:focus-visible { outline:2px solid #3b82f6; outline-offset:1px; }
        [data-table-only][hidden], [data-table-context][hidden] { display:none; }
        .editor-select { height:2rem; flex-shrink:0; border:0; border-radius:.5rem; background:#f1f5f9; padding:0 .5rem; font-size:.75rem; font-weight:600; color:#475569; outline:none; }
        .editor-size-control { display:flex; height:2rem; width:4.5rem; align-items:center; border-radius:.5rem; background:#f1f5f9; padding:0 .45rem; color:#64748b; }
        .editor-size-control input { min-width:0; width:100%; border:0; background:transparent; padding:0; font-size:.75rem; font-weight:700; color:#334155; outline:none; }
        .editor-size-control span { font-size:.6rem; }
        .editor-label { margin-bottom:.4rem; display:block; font-size:.68rem; font-weight:800; letter-spacing:.06em; color:#64748b; text-transform:uppercase; }
        .setup-input { width:100%; border:1px solid #e2e8f0; border-radius:.65rem; background:#f8fafc; padding:.65rem .75rem; font-size:.8rem; outline:none; }
        .setup-input:focus { border-color:#10b981; box-shadow:0 0 0 3px rgb(16 185 129 / .12); }
        .orientation-option { display:flex; height:4.25rem; cursor:pointer; flex-direction:column; align-items:center; justify-content:center; gap:.35rem; border:1px solid #e2e8f0; border-radius:.75rem; font-size:.72rem; font-weight:700; color:#64748b; transition:.15s; }
        .peer:checked + .orientation-option { border-color:#10b981; background:#ecfdf5; color:#047857; box-shadow:0 0 0 1px #10b981; }
        .margin-field { border:1px solid #e2e8f0; border-radius:.65rem; padding:.4rem .6rem; font-size:.65rem; font-weight:700; color:#64748b; }
        .margin-field input { width:100%; border:0; background:transparent; padding:.1rem 0 0; font-size:.85rem; font-weight:700; color:#1e293b; outline:none; }
        .typing-option { display:flex; cursor:pointer; flex-direction:column; gap:.15rem; border:1px solid #e2e8f0; border-radius:.75rem; padding:.65rem; font-size:.7rem; color:#475569; transition:.15s; }
        .typing-option small { color:#94a3b8; font-size:.58rem; }
        .peer:checked + .typing-option { border-color:#10b981; background:#ecfdf5; color:#047857; box-shadow:0 0 0 1px #10b981; }
        .converter-field { width:100%; resize:vertical; border:1px solid #e2e8f0; border-radius:.85rem; background:#f8fafc; padding:.85rem; font-size:1rem; line-height:1.7; outline:none; }
        .converter-field:focus { border-color:#10b981; box-shadow:0 0 0 3px rgb(16 185 129 / .12); }
        .page-scaler { width:calc(var(--page-width) * var(--editor-scale)); height:calc(var(--page-height) * var(--editor-scale)); }
        .document-page { width:var(--page-width); height:var(--page-height); transform:scale(var(--editor-scale)); transform-origin:top left; }
        .page-scaler.is-active .document-page { outline:2px solid #10b981; outline-offset:3px; }
        .page-scaler.has-overflow .document-page { outline-color:#ef4444; }
        .editor-canvas { padding:var(--margin-top) var(--margin-right) var(--margin-bottom) var(--margin-left); overflow:hidden; overflow-wrap:normal; word-break:normal; hyphens:none; font-family:var(--editor-font, 'Noto Sans Bengali', sans-serif); font-size:12pt; line-height:1.6; text-justify:inter-word; text-align-last:left; letter-spacing:normal; word-spacing:normal; }
        .editor-canvas h1 { margin:0 0 1rem; font-size:24pt; font-weight:800; line-height:1.2; }
        .editor-canvas h2 { margin:1rem 0 .75rem; font-size:19pt; font-weight:750; line-height:1.25; }
        .editor-canvas h3 { margin:.75rem 0 .5rem; font-size:15pt; font-weight:700; }
        .editor-canvas p { margin:0 0 .45rem; }
        .editor-canvas ul { list-style:disc; padding-left:1.5rem; } .editor-canvas ol { list-style:decimal; padding-left:1.5rem; }
        .editor-canvas blockquote { margin:1rem 0; border-left:4px solid #10b981; background:#f0fdf4; padding:.75rem 1rem; color:#475569; }
        .editor-canvas table { width:100%; margin:1rem 0; border-collapse:collapse; table-layout:fixed; }
        .editor-canvas td, .editor-canvas th { min-width:2rem; border:1px solid #64748b; padding:.4rem .55rem; vertical-align:top; }
        .editor-canvas td:hover, .editor-canvas th:hover { outline:2px solid #10b981; outline-offset:-2px; }
        .editor-canvas td.is-selected, .editor-canvas th.is-selected { background:#d1fae5; box-shadow:inset 0 0 0 2px #059669; }
        .table-context-tool { display:flex; height:2rem; align-items:center; justify-content:center; white-space:nowrap; border-radius:.35rem; padding:0 .55rem; }
        .table-context-tool:hover { background:#ecfdf5; }
        .editor-canvas hr { margin:1rem 0; border:0; border-top:1px solid #94a3b8; }
        .editor-canvas a { color:#047857; text-decoration:underline; }
        .editor-canvas:empty::before { content:attr(data-placeholder); color:#94a3b8; pointer-events:none; }
        .dark .editor-tool { color:#cbd5e1; }
        .dark .editor-tool:hover { background:#334155; color:#fff; }
        .dark .editor-tool.is-on { border-color:#2563eb; background:#1e3a8a; color:#dbeafe; }
        .dark .editor-select, .dark .editor-size-control, .dark .setup-input, .dark .converter-field { border-color:#334155; background:#0f172a; color:#e2e8f0; }
        .dark .editor-size-control input { color:#e2e8f0; }
        .dark .editor-label { color:#94a3b8; }
        .dark .orientation-option, .dark .margin-field, .dark .typing-option { border-color:#334155; background:#0f172a; color:#cbd5e1; }
        .dark .margin-field input { color:#e2e8f0; }
        .dark .peer:checked + .orientation-option, .dark .peer:checked + .typing-option { border-color:#34d399; background:#064e3b; color:#d1fae5; box-shadow:0 0 0 1px #34d399; }
        .dark .editor-canvas td.is-selected, .dark .editor-canvas th.is-selected { background:#064e3b; box-shadow:inset 0 0 0 2px #34d399; }
        .dark .table-context-tool:hover { background:#1e293b; }
        @media print {
            html, body { margin:0 !important; padding:0 !important; background:white !important; }
            body * { visibility:hidden !important; }
            body .document-pages, body .document-pages *, body .document-page, body .document-page * { visibility:visible !important; }
            .document-studio { position:absolute !important; inset:0 !important; min-height:0 !important; }
            .document-studio > main, .document-studio > main > section, .page-stage, .document-pages { display:block !important; width:auto !important; max-width:none !important; min-height:0 !important; margin:0 !important; padding:0 !important; border:0 !important; overflow:visible !important; background:white !important; }
            .document-studio > main > aside, .document-chrome { display:none !important; }
            .page-scaler { width:var(--page-width) !important; height:var(--page-height) !important; margin:0 !important; break-after:page; page-break-after:always; }
            .page-scaler:last-child { break-after:auto; page-break-after:auto; }
            .document-page { width:var(--page-width) !important; height:var(--page-height) !important; margin:0 !important; transform:none !important; outline:0 !important; box-shadow:none !important; print-color-adjust:exact; -webkit-print-color-adjust:exact; }
            .editor-canvas { overflow:visible !important; }
        }
    </style>
</div>
