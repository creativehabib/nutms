const searchableSelects = new WeakMap();
const relatedCollegeCarousels = new WeakMap();

const initializeRelatedCollegeCarousels = async () => {
    const carousels = [...document.querySelectorAll('[data-related-colleges-carousel]')]
        .filter((carousel) => ! relatedCollegeCarousels.has(carousel));

    if (carousels.length === 0) {
        return;
    }

    const { default: EmblaCarousel } = await import('embla-carousel');

    carousels.forEach((carousel) => {
        if (relatedCollegeCarousels.has(carousel)) {
            return;
        }

        const viewport = carousel.querySelector('[data-carousel-viewport]');
        const previousButton = carousel.querySelector('[data-carousel-previous]');
        const nextButton = carousel.querySelector('[data-carousel-next]');

        if (! viewport) {
            return;
        }

        const embla = EmblaCarousel(viewport, {
            align: 'start',
            containScroll: 'trimSnaps',
            slidesToScroll: 'auto',
        });
        const updateControls = () => {
            if (previousButton) {
                previousButton.disabled = ! embla.canScrollPrev();
            }

            if (nextButton) {
                nextButton.disabled = ! embla.canScrollNext();
            }
        };

        previousButton?.addEventListener('click', () => embla.scrollPrev());
        nextButton?.addEventListener('click', () => embla.scrollNext());
        embla.on('init', updateControls);
        embla.on('reInit', updateControls);
        embla.on('select', updateControls);
        updateControls();
        relatedCollegeCarousels.set(carousel, embla);
    });
};

const initializeSearchableSelects = async () => {
    const selects = [...document.querySelectorAll('[data-searchable-select]')]
        .filter((select) => select.dataset.choice !== 'active');

    if (selects.length === 0) {
        return;
    }

    const [{ default: Choices }] = await Promise.all([
        import('choices.js'),
        import('choices.js/public/assets/styles/choices.min.css'),
    ]);

    selects.forEach((select) => {
        const choices = new Choices(select, {
            allowHTML: false,
            itemSelectText: '',
            noResultsText: select.dataset.noResultsText,
            placeholder: true,
            searchEnabled: true,
            searchFields: ['label', 'value'],
            searchPlaceholderValue: select.dataset.searchPlaceholder,
            shouldSort: false,
        });

        searchableSelects.set(select, choices);

        if (select.dataset.livewireModel) {
            select.addEventListener('change', () => {
                const componentElement = select.closest('[wire\\:id]');

                if (componentElement) {
                    window.Livewire.find(componentElement.getAttribute('wire:id'))
                        ?.set(select.dataset.livewireModel, select.value);
                }
            });
        }
    });
};

document.addEventListener('DOMContentLoaded', initializeSearchableSelects);
document.addEventListener('DOMContentLoaded', initializeRelatedCollegeCarousels);
document.addEventListener('livewire:navigated', () => {
    initializeSearchableSelects();
    initializeRelatedCollegeCarousels();
});
document.addEventListener('reset-teacher-filters', () => {
    document.querySelectorAll('[data-teacher-filter]').forEach((select) => {
        searchableSelects.get(select)?.setChoiceByValue('');
    });
});

const unicodeToBijoyCharacters = new Map([
    ['অ', 'A'], ['আ', 'Av'], ['ই', 'B'], ['ঈ', 'C'], ['উ', 'D'], ['ঊ', 'E'], ['ঋ', 'F'], ['এ', 'G'], ['ঐ', 'H'], ['ও', 'I'], ['ঔ', 'J'],
    ['ক', 'K'], ['খ', 'L'], ['গ', 'M'], ['ঘ', 'N'], ['ঙ', 'O'], ['চ', 'P'], ['ছ', 'Q'], ['জ', 'R'], ['ঝ', 'S'], ['ঞ', 'T'],
    ['ট', 'U'], ['ঠ', 'V'], ['ড', 'W'], ['ঢ', 'X'], ['ণ', 'Y'], ['ত', 'Z'], ['থ', '_'], ['দ', '`'], ['ধ', 'a'], ['ন', 'b'],
    ['প', 'c'], ['ফ', 'd'], ['ব', 'e'], ['ভ', 'f'], ['ম', 'g'], ['য', 'h'], ['র', 'i'], ['ল', 'j'], ['শ', 'k'], ['ষ', 'l'], ['স', 'm'], ['হ', 'n'], ['ড়', 'o'], ['ঢ়', 'p'], ['য়', 'q'], ['ৎ', 'r'],
    ['ং', 's'], ['ঃ', 't'], ['ঁ', 'u'], ['া', 'v'], ['ি', 'w'], ['ী', 'x'], ['ু', 'y'], ['ূ', '~'], ['ৃ', '…'], ['ে', '†'], ['ৈ', '‰'], ['ো', '‡'], ['ৌ', 'Š'], ['ৗ', 'Š'], ['্', '&'],
    ['০', '0'], ['১', '1'], ['২', '2'], ['৩', '3'], ['৪', '4'], ['৫', '5'], ['৬', '6'], ['৭', '7'], ['৮', '8'], ['৯', '9'], ['।', '|'], ['–', '–'], ['—', '—'],
]);

const unicodeToBijoyLigatures = new Map([
    ['ক্ষ্ম', '²'], ['ক্ষ', '¶'], ['জ্ঞ', 'Á'], ['ঙ্ক', '¼'], ['ঙ্খ', '•L'], ['ঙ্গ', '½'], ['ঙ্ঘ', '•N'], ['চ্চ', '”P'], ['চ্ছ', '”Q'], ['জ্জ', '¾'], ['ঞ্ছ', 'TQ'], ['ঞ্চ', 'Â'], ['ঞ্জ', 'Ã'],
    ['ট্ট', 'Æ'], ['ড্ড', 'Ç'], ['ণ্ট', 'È'], ['ণ্ঠ', 'É'], ['ণ্ড', 'Ê'], ['ত্ত', 'Ë'], ['ত্থ', 'Ì'], ['ত্ম', 'Z¥'], ['ত্র', 'Î'], ['দ্দ', 'Ï'], ['দ্ধ', '×'], ['দ্ব', 'Ø'], ['দ্ম', '`¥'], ['দ্র', '`ª'],
    ['ধ্ব', 'aŸ'], ['ন্ত', 'šÍ'], ['ন্থ', 'š’'], ['ন্দ', '›`'], ['ন্ধ', 'Ü'], ['ন্ন', 'bœ'], ['ন্ব', 'š^'], ['ন্ম', 'š§'], ['প্ত', 'ß'], ['প্প', 'à'], ['প্ল', 'cø'], ['ব্জ', 'â'], ['ব্দ', 'ã'], ['ব্ধ', 'ä'],
    ['ব্ব', 'eŸ'], ['ব্ল', 'eø'], ['ভ্র', 'å'], ['ম্ন', 'gœ'], ['ম্প', '¤ú'], ['ম্ফ', 'ç'], ['ম্ব', '¤^'], ['ম্ভ', '¤¢'], ['ম্ম', '¤§'], ['ম্ল', 'gø'], ['ল্ক', 'é'], ['ল্গ', 'ê'], ['ল্ট', 'ë'], ['ল্ড', 'ì'],
    ['ল্প', 'í'], ['ল্ফ', 'î'], ['ল্ব', 'j¦'], ['ল্ম', 'j¥'], ['ল্ল', 'jø'], ['শ্চ', 'ð'], ['শ্ন', 'kœ'], ['শ্ব', 'k¦'], ['শ্ম', 'k¥'], ['শ্ল', 'kø'], ['ষ্ক', '®‹'], ['ষ্ট', 'ó'], ['ষ্ঠ', 'ô'], ['ষ্প', '®ú'], ['ষ্ফ', 'õ'],
    ['ষ্ম', '®§'], ['স্ক', '¯‹'], ['স্ট', '÷'], ['স্ত', '¯Í'], ['স্থ', '¯’'], ['স্ন', 'mœ'], ['স্প', '¯ú'], ['স্ফ', 'ù'], ['স্ব', '¯^'], ['স্ম', '¯§'], ['স্ল', 'mø'], ['হ্ন', 'nè'], ['হ্ব', 'nŸ'], ['হ্ম', 'þ'], ['হ্ল', 'n¬'],
    ['ক্র', 'µ'], ['গ্র', 'MÖ'], ['প্র', 'cÖ'], ['ব্র', 'eª'], ['শ্র', 'kÖ'], ['রু', 'i“'], ['রূ', 'iƒ'],
]);

const bijoyToUnicodeLigatures = new Map([...unicodeToBijoyLigatures].map(([unicode, bijoy]) => [bijoy, unicode]));
const bijoyToUnicodeCharacters = new Map([...unicodeToBijoyCharacters].map(([unicode, bijoy]) => [bijoy, unicode]));

const replaceFromMap = (value, replacements) => [...replacements.entries()]
    .sort(([first], [second]) => second.length - first.length)
    .reduce((result, [search, replacement]) => result.split(search).join(replacement), value);

const normalizeUnicodeBangla = (value) => value
    .normalize('NFC')
    .replace(/ড়/g, 'ড়')
    .replace(/ঢ়/g, 'ঢ়')
    .replace(/য়/g, 'য়')
    .replace(/ো/g, 'ো')
    .replace(/ৌ/g, 'ৌ')
    .replace(/্{2,}/g, '্');

const convertUnicodeToBijoy = (value) => {
    let converted = normalizeUnicodeBangla(value)
        .replace(/ো/g, 'ো')
        .replace(/ৌ/g, 'ৌ')
        .replace(/([ক-হড়ঢ়য়ৎ](?:্[ক-হড়ঢ়য়ৎ])*)([িেৈ])/g, '$2$1')
        .replace(/র্([ক-হড়ঢ়য়ৎ](?:্[ক-হড়ঢ়য়ৎ])*)/g, '$1©');

    converted = replaceFromMap(converted, unicodeToBijoyLigatures);

    return replaceFromMap(converted, unicodeToBijoyCharacters);
};

const convertBijoyToUnicode = (value) => {
    let converted = replaceFromMap(value, bijoyToUnicodeLigatures);
    converted = replaceFromMap(converted, bijoyToUnicodeCharacters)
        .replace(/([িেৈোৌ])([ক-হড়ঢ়য়ৎ](?:্[ক-হড়ঢ়য়ৎ])*)/g, '$2$1')
        .replace(/([ক-হড়ঢ়য়ৎ](?:্[ক-হড়ঢ়য়ৎ])*)©/g, 'র্$1');

    return normalizeUnicodeBangla(converted);
};

const initializeUnicodeBijoyConverters = () => {
    document.querySelectorAll('[data-unicode-bijoy-converter]').forEach((converter) => {
        if (converter.dataset.initialized === 'true') {
            return;
        }

        const input = converter.querySelector('[data-converter-input]');
        const output = converter.querySelector('[data-converter-output]');
        const status = converter.querySelector('[data-converter-status]');
        const voiceButton = converter.querySelector('[data-voice-typing]');
        const voiceLabel = converter.querySelector('[data-voice-label]');
        const setStatus = (message) => { status.textContent = message; };
        const showResult = (field, result, message) => {
            field.value = result;
            setStatus(message);
            field.focus();
        };

        converter.querySelectorAll('[data-convert]').forEach((button) => {
            button.addEventListener('click', () => {
                const actions = {
                    'unicode-to-bijoy': {
                        source: input,
                        run: () => showResult(output, convertUnicodeToBijoy(input.value), 'ইউনিকোড লেখা বিজয়ে রূপান্তর হয়েছে।'),
                    },
                    'bijoy-to-unicode': {
                        source: output,
                        run: () => showResult(input, convertBijoyToUnicode(output.value), 'বিজয় লেখা ইউনিকোডে রূপান্তর হয়েছে।'),
                    },
                    'fix-unicode': {
                        source: input,
                        run: () => showResult(input, normalizeUnicodeBangla(input.value), 'ইউনিকোড লেখার সাধারণ ত্রুটি ঠিক করা হয়েছে।'),
                    },
                    'fix-bijoy': {
                        source: output,
                        run: () => showResult(output, convertUnicodeToBijoy(convertBijoyToUnicode(output.value)), 'বিজয় লেখার সাধারণ ত্রুটি ঠিক করা হয়েছে।'),
                    },
                };

                const action = actions[button.dataset.convert];

                if (! action?.source.value.trim()) {
                    setStatus('রূপান্তরের জন্য নির্ধারিত ঘরে আগে কিছু লেখা দিন।');
                    action?.source.focus();
                    return;
                }

                action.run();
            });
        });

        converter.querySelector('[data-clear-converter]').addEventListener('click', () => {
            input.value = '';
            output.value = '';
            setStatus('লেখা মুছে ফেলা হয়েছে।');
            input.focus();
        });

        converter.querySelector('[data-copy-converter]').addEventListener('click', async () => {
            if (! output.value) {
                setStatus('কপি করার মতো কোনো লেখা নেই।');
                return;
            }

            await navigator.clipboard.writeText(output.value);
            setStatus('রূপান্তরিত লেখা কপি হয়েছে।');
        });

        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

        if (! SpeechRecognition) {
            voiceButton.disabled = true;
            voiceButton.title = 'এই ব্রাউজারে ভয়েস টাইপিং সমর্থিত নয়';
        } else {
            const recognition = new SpeechRecognition();
            recognition.lang = 'bn-BD';
            recognition.interimResults = true;
            recognition.continuous = true;
            let startingText = '';

            recognition.addEventListener('start', () => {
                startingText = input.value;
                voiceButton.dataset.listening = 'true';
                voiceLabel.textContent = 'ভয়েস টাইপিং বন্ধ করুন';
                setStatus('শুনছি… বাংলায় কথা বলুন।');
            });
            recognition.addEventListener('result', (event) => {
                const transcript = [...event.results].map((result) => result[0].transcript).join('');
                input.value = `${startingText}${startingText && transcript ? ' ' : ''}${transcript}`;
            });
            recognition.addEventListener('end', () => {
                delete voiceButton.dataset.listening;
                voiceLabel.textContent = 'ভয়েস টাইপিং শুরু করুন';
                setStatus('ভয়েস টাইপিং শেষ হয়েছে।');
            });
            recognition.addEventListener('error', () => setStatus('ভয়েস শনাক্ত করা যায়নি। আবার চেষ্টা করুন।'));
            voiceButton.addEventListener('click', () => {
                if (voiceButton.dataset.listening === 'true') {
                    recognition.stop();
                } else {
                    recognition.start();
                }
            });
        }

        converter.dataset.initialized = 'true';
    });
};

document.addEventListener('DOMContentLoaded', initializeUnicodeBijoyConverters);
document.addEventListener('livewire:navigated', initializeUnicodeBijoyConverters);
