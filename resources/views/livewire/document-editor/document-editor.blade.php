<div class="min-h-screen bg-slate-100 dark:bg-slate-950 py-8 font-sans" x-data="documentApp()" x-init="initApp()">

    <!-- Dynamic Print Styles (Injected via Alpine) -->
    <style x-html="printStyles"></style>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <!-- Top Action Bar -->
        <div class="mb-6 flex flex-col sm:flex-row items-center justify-between gap-4 bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800">
            <div>
                <h2 class="font-bold text-xl text-slate-800 dark:text-white">স্মার্ট ডকুমেন্ট এডিটর</h2>
                <p class="text-xs text-slate-500">অনলাইন ওয়ার্ড প্রসেসর ও প্রিন্ট টুল</p>
            </div>

            <div class="flex items-center gap-3">
                <flux:button x-on:click="$modalOpen('converter-modal')" variant="subtle" icon="arrows-right-left" class="!text-emerald-700 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-900/30 dark:!text-emerald-400">
                    বিজয় ⇄ ইউনিকোড
                </flux:button>

                <flux:button wire:click="saveDocument" variant="outline" icon="document-check">
                    সেভ করুন
                </flux:button>

                <flux:button onclick="window.print()" variant="primary" icon="printer" class="!bg-slate-900 hover:!bg-slate-800 dark:!bg-white dark:hover:!bg-slate-200 dark:!text-slate-900">
                    প্রিন্ট (Ctrl+P)
                </flux:button>
            </div>
        </div>

        <!-- Main Workspace -->
        <div class="flex flex-col lg:flex-row gap-6 items-start">

            <!-- Left: Document Editor Area -->
            <div class="flex-1 w-full overflow-x-auto pb-8 relative" wire:ignore>

                <!-- Loading Spinner -->
                <div id="editor-loader" class="absolute inset-0 z-10 flex flex-col items-center justify-center bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800" :style="'min-height: ' + sizes[paperSize].h">
                    <svg class="size-8 animate-spin text-emerald-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span class="text-slate-500 font-bold" id="loader-text">এডিটর প্রস্তুত হচ্ছে...</span>
                </div>

                <!-- Editor Wrapper (Styles bound via Alpine CSS Variables) -->
                <div class="editor-wrapper mx-auto transition-all duration-300" :style="editorCssVariables">
                    <textarea id="word_editor" class="hidden">{!! $documentContent !!}</textarea>
                </div>
            </div>

            <!-- Right: Page Setup Sidebar -->
            <div class="w-full lg:w-80 shrink-0 sticky top-8 flex flex-col gap-5">

                <!-- Typing Mode Setup -->
                <div class="rounded-2xl bg-white dark:bg-slate-900 p-5 shadow-sm border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="size-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        টাইপিং মোড
                    </h3>
                    <div class="grid grid-cols-2 gap-3">
                        <button @click="typingMode = 'unicode'" :class="typingMode === 'unicode' ? 'bg-emerald-50 border-emerald-500 text-emerald-700 dark:bg-emerald-900/40 dark:border-emerald-400 dark:text-emerald-300' : 'bg-white border-slate-200 text-slate-600 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-400'" class="border rounded-xl py-3 text-sm font-bold transition-all text-center">
                            ইউনিকোড (অভ্র)
                        </button>
                        <button @click="typingMode = 'bijoy'" :class="typingMode === 'bijoy' ? 'bg-emerald-50 border-emerald-500 text-emerald-700 dark:bg-emerald-900/40 dark:border-emerald-400 dark:text-emerald-300' : 'bg-white border-slate-200 text-slate-600 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-400'" class="border rounded-xl py-3 text-sm font-bold transition-all text-center">
                            বিজয় (SutonnyMJ)
                        </button>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-3 text-center">সঠিক ফন্টে টাইপ করতে মোড সিলেক্ট করুন</p>
                </div>

                <!-- Page Setup -->
                <div class="rounded-2xl bg-white dark:bg-slate-900 p-5 shadow-sm border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="size-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        পেইজ সেটআপ
                    </h3>

                    <!-- Paper Size -->
                    <div class="mb-5">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">কাগজের সাইজ</label>
                        <select x-model="paperSize" class="w-full rounded-xl border-slate-200 bg-slate-50 p-2.5 text-sm font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 outline-none">
                            <option value="A4">A4 (210 × 297 mm)</option>
                            <option value="Letter">Letter (8.5 × 11 in)</option>
                            <option value="Legal">Legal (8.5 × 14 in)</option>
                        </select>
                    </div>

                    <!-- Margin Size -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">মার্জিন</label>
                        <select x-model="marginSize" class="w-full rounded-xl border-slate-200 bg-slate-50 p-2.5 text-sm font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 outline-none">
                            <option value="25.4mm">স্বাভাবিক (১ ইঞ্চি)</option>
                            <option value="12.7mm">সংকীর্ণ (০.৫ ইঞ্চি)</option>
                            <option value="38.1mm">প্রশস্ত (১.৫ ইঞ্চি)</option>
                        </select>
                    </div>
                </div>

                <div class="rounded-xl border border-amber-200/60 bg-amber-50/50 p-4 text-xs leading-relaxed text-amber-800 dark:border-amber-900/30 dark:bg-amber-900/10 dark:text-amber-300">
                    <span class="font-bold block mb-1">প্রিন্ট টিপস:</span>
                    প্রিন্ট ডায়ালগ বক্সে (Ctrl+P) "Margins" অপশনটি "Default" বা "None" রাখুন, তাহলে এখানকার সেটআপ নিখুঁতভাবে কাজ করবে।
                </div>
            </div>
        </div>
    </div>

    <!-- Converter Modal (Unchanged) -->
    <flux:modal name="converter-modal" class="md:w-[600px]">
        <div x-data="documentConverter()" class="space-y-4">
            <flux:heading size="lg" class="border-b pb-3 mb-4">টেক্সট কনভার্টার</flux:heading>
            <flux:radio.group x-model="mode" variant="segmented" class="mb-4">
                <flux:radio value="bijoy_to_unicode" label="বিজয় থেকে ইউনিকোড" />
                <flux:radio value="unicode_to_bijoy" label="ইউনিকোড থেকে বিজয়" />
            </flux:radio.group>
            <textarea x-model="inputText" @input="convertText" class="w-full h-32 rounded-xl border-slate-300 bg-slate-50 p-3 text-sm focus:ring-emerald-500" placeholder="এখানে আপনার টেক্সট পেস্ট করুন..."></textarea>
            <div class="relative">
                <textarea x-model="outputText" readonly class="w-full h-32 rounded-xl border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-900" placeholder="কনভার্ট করা টেক্সট এখানে আসবে..."></textarea>
                <flux:button @click="copyText" size="sm" class="absolute bottom-2 right-2 !bg-emerald-600 !text-white" x-text="copied ? 'কপি হয়েছে!' : 'কপি করুন'"></flux:button>
            </div>
        </div>
    </flux:modal>
</div>

<!-- Custom Editor CSS -->
<style>
    /* Load SutonnyMJ globally */
    @font-face {
        font-family: 'SutonnyMJ';
        src: url('https://themes.muffingroup.com/be/marketing2/wp-content/uploads/2020/11/SutonnyMJ.ttf') format('truetype');
        font-weight: normal; font-style: normal;
    }

    /* Editor Wrapper uses CSS Variables injected by Alpine */
    .editor-wrapper {
        width: var(--paper-width, 210mm);
    }

    .ck-editor__editable_inline {
        min-height: var(--paper-height, 297mm) !important;
        padding: var(--paper-margin, 25.4mm) !important;
        font-family: var(--editor-font, 'Noto Sans Bengali') !important;
        background-color: white !important;
        color: black !important;
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1) !important;
        border: 1px solid #cbd5e1 !important;
        font-size: 14pt;
        line-height: 1.6;
        transition: padding 0.3s ease, min-height 0.3s ease;
    }

    .ck.ck-toolbar {
        border-radius: 0.75rem !important; margin-bottom: 1rem;
        border: 1px solid #e2e8f0 !important; background-color: #f8fafc !important;
    }
    .ck.ck-editor__editable.ck-focused {
        outline: none !important; box-shadow: 0 0 0 2px #10b981 !important;
    }

    /* Base Print Styles (Page size and margins handled by Alpine x-html) */
    @media print {
        body { background-color: white !important; }
        body * { visibility: hidden; }
        .editor-wrapper, .editor-wrapper * { visibility: visible; }
        .editor-wrapper {
            position: absolute; left: 0; top: 0;
            width: var(--paper-width, 210mm) !important;
            margin: 0 !important;
        }
        .ck-editor__editable_inline {
            padding: 0 !important; /* Margins handled by @page */
            box-shadow: none !important; border: none !important;
        }
        .ck-editor__top { display: none !important; }
    }
</style>

<!-- CKEditor Main App Logic -->
<script>
    document.addEventListener('alpine:init', () => {

        Alpine.data('documentApp', () => ({

            paperSize: 'A4',
            marginSize: '25.4mm', // Default 1 inch
            typingMode: 'unicode', // 'unicode' or 'bijoy'

            sizes: {
                'A4': { w: '210mm', h: '297mm' },
                'Letter': { w: '215.9mm', h: '279.4mm' },
                'Legal': { w: '215.9mm', h: '355.6mm' }
            },

            // Computes dynamic CSS variables for the editor wrapper
            get editorCssVariables() {
                let size = this.sizes[this.paperSize];
                let font = this.typingMode === 'bijoy' ? "'SutonnyMJ', sans-serif" : "'Noto Sans Bengali', sans-serif";

                return `
                    --paper-width: ${size.w};
                    --paper-height: ${size.h};
                    --paper-margin: ${this.marginSize};
                    --editor-font: ${font};
                `;
            },

            // Dynamically injects @page print rules based on settings
            get printStyles() {
                return `
                    @media print {
                        @page { size: ${this.paperSize}; margin: ${this.marginSize}; }
                    }
                `;
            },

            initApp() {
                const el = document.getElementById('word_editor');
                const loader = document.getElementById('editor-loader');

                if (typeof CKEDITOR === 'undefined') {
                    const script = document.createElement('script');
                    script.src = 'https://cdn.ckeditor.com/ckeditor5/41.1.0/super-build/ckeditor.js';
                    script.onload = () => this.startCKEditor(el, loader);
                    script.onerror = () => { document.getElementById('loader-text').innerText = "ইন্টারনেট কানেকশন চেক করুন!"; };
                    document.head.appendChild(script);
                } else {
                    this.startCKEditor(el, loader);
                }
            },

            startCKEditor(el, loader) {
                if (window.ckeditorInstance) {
                    window.ckeditorInstance.destroy().catch(()=>{});
                }

                CKEDITOR.ClassicEditor.create(el, {
                    toolbar: {
                        items: [
                            'undo', 'redo', '|', 'heading', '|',
                            'fontFamily', 'fontSize', 'fontColor', 'fontBackgroundColor', '|',
                            'bold', 'italic', 'underline', 'strikethrough', '|',
                            'alignment', '|', 'bulletedList', 'numberedList', '|',
                            'insertTable', 'pageBreak', '|', 'removeFormat'
                        ],
                        shouldNotGroupWhenFull: true
                    },
                    fontFamily: {
                        options: ['default', 'Noto Sans Bengali, sans-serif', 'SutonnyMJ, sans-serif', 'Arial, Helvetica, sans-serif']
                    },
                    fontSize: {
                        options: [10, 12, 14, 'default', 18, 20, 24]
                    },
                    removePlugins: [
                        'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage',
                        'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
                        'RealTimeCollaborativeRevisionHistory', 'PresenceList', 'Comments',
                        'TrackChanges', 'TrackChangesData', 'RevisionHistory', 'Pagination',
                        'WProofreader', 'MathType', 'SlashCommand', 'Template', 'DocumentOutline',
                        'FormatPainter', 'TableOfContents', 'PasteFromOfficeEnhanced', 'CaseChange'
                    ]
                }).then(editor => {
                    window.ckeditorInstance = editor;
                    if (loader) loader.remove();

                    let timer;
                    editor.model.document.on('change:data', () => {
                        clearTimeout(timer);
                        timer = setTimeout(() => {
                            if (typeof this.$wire !== 'undefined') {
                                this.$wire.set('documentContent', editor.getData());
                            }
                        }, 1000);
                    });
                }).catch(err => {
                    console.error("CKEditor Error:", err);
                    document.getElementById('loader-text').innerText = "এডিটর লোড হতে সমস্যা হয়েছে!";
                });
            }
        }));

        // Converter Logic
        Alpine.data('documentConverter', () => ({
            mode: 'bijoy_to_unicode',
            inputText: '',
            outputText: '',
            copied: false,
            bijoyMap: {'Av':'আ','A':'অ','B':'ই','C':'ঈ','D':'উ','E':'ঊ','F':'ঋ','G':'এ','H':'ঐ','I':'ও','J':'ঔ','K':'ক','L':'খ','M':'গ','N':'ঘ','O':'ঙ','P':'চ','Q':'ছ','R':'জ','S':'ঝ','T':'ঞ','U':'ট','V':'ঠ','W':'ড','X':'ঢ','Y':'ণ','Z':'ত','_':'থ','`':'দ','~':'ধ','v':'ন','w':'প','x':'ফ','y':'ব','z':'ভ','h':'য','i':'র','j':'ল','k':'শ','l':'ষ','m':'স','n':'হ','o':'ড়','p':'ঢ়','q':'য়','r':'ৎ','s':'ং','t':'ঃ','u':'ঁ','0':'০','1':'১','2':'২','3':'৩','4':'৪','5':'৫','6':'৬','7':'৭','8':'৮','9':'৯','|':'।'},
            b2u(text) {
                let out = text.replace(/©/g, 'র্').replace(/¨/g, '্য').replace(/ÿ/g, 'ক্ষ');
                out = out.replace(/av/g, 'ো').replace(/aZ/g, 'ৌ').replace(/a`/g, 'ৈ').replace(/a/g, 'ে').replace(/w/g, 'ি').replace(/x/g, 'ী');
                out = out.replace(/([েৈি])([ক-হড়-য়])/g, '$2$1');
                let res = ''; let skip = false;
                for(let i=0; i<out.length; i++) {
                    if(skip){ skip = false; continue; }
                    let d = out.substring(i, i+2);
                    if(this.bijoyMap[d]){ res += this.bijoyMap[d]; skip = true; }
                    else { res += this.bijoyMap[out[i]] || out[i]; }
                }
                return res;
            },
            u2b(text) {
                let out = text.replace(/ে/g, 'a').replace(/ৈ/g, 'a`').replace(/ো/g, 'av').replace(/ৌ/g, 'aZ').replace(/ি/g, 'w').replace(/ী/g, 'x');
                out = out.replace(/র্য/g, 'h©').replace(/র্/g, '©').replace(/্য/g, '¨').replace(/ক্ষ/g, 'ÿ');
                let revMap = {}; for(let k in this.bijoyMap) revMap[this.bijoyMap[k]] = k;
                let res = '';
                for(let i=0; i<out.length; i++) { res += revMap[out[i]] || out[i]; }
                res = res.replace(/([K-Vw-z])([waZ`])/g, '$2$1');
                return res;
            },
            convertText() {
                if (!this.inputText) { this.outputText = ''; return; }
                this.outputText = this.mode === 'bijoy_to_unicode' ? this.b2u(this.inputText) : this.u2b(this.inputText);
            },
            copyText() {
                navigator.clipboard.writeText(this.outputText);
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            }
        }));
    });
</script>
