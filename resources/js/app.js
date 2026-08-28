import { convertBijoyToUnicode, convertUnicodeToBijoy } from './bangla-converter';
import { initializeImageCompressors } from './image-compressor';
import { initializeAgeRetirementCalculators } from './age-retirement-calculator';

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
document.addEventListener('DOMContentLoaded', initializeImageCompressors);
document.addEventListener('DOMContentLoaded', initializeAgeRetirementCalculators);
document.addEventListener('livewire:navigated', () => {
    initializeSearchableSelects();
    initializeRelatedCollegeCarousels();
    initializeImageCompressors();
    initializeAgeRetirementCalculators();
});
document.addEventListener('reset-teacher-filters', () => {
    document.querySelectorAll('[data-teacher-filter]').forEach((select) => {
        searchableSelects.get(select)?.setChoiceByValue('');
    });
});

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
        const showFluxToast = (text, variant = 'success') => window.Flux?.toast({ text, variant });
        const usesMobileBijoyPreview = window.matchMedia('(max-width: 767px)').matches;
        const getBijoyValue = () => output.dataset.bijoyValue || output.value;
        const showBijoyResult = () => {
            const convertedValue = convertUnicodeToBijoy(input.value);

            if (usesMobileBijoyPreview) {
                output.dataset.bijoyValue = convertedValue;
                output.value = normalizeUnicodeBangla(input.value);
                output.style.fontFamily = 'var(--font-sans)';
                setStatus('বিজয় লেখা তৈরি হয়েছে। মোবাইলে বাংলা প্রিভিউ দেখানো হচ্ছে; কপি করলে বিজয় লেখা কপি হবে।');
                output.focus();

                return;
            }

            delete output.dataset.bijoyValue;
            output.style.removeProperty('font-family');
            showResult(output, convertedValue, 'ইউনিকোড লেখা বিজয়ে রূপান্তর হয়েছে।');
        };
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
                        run: showBijoyResult,
                    },
                    'bijoy-to-unicode': {
                        source: output,
                        run: () => showResult(input, convertBijoyToUnicode(getBijoyValue()), 'বিজয় লেখা ইউনিকোডে রূপান্তর হয়েছে।'),
                    },
                    'fix-unicode': {
                        source: input,
                        run: () => showResult(input, normalizeUnicodeBangla(input.value), 'ইউনিকোড লেখার সাধারণ ত্রুটি ঠিক করা হয়েছে।'),
                    },
                    'fix-bijoy': {
                        source: output,
                        run: () => showResult(output, convertUnicodeToBijoy(convertBijoyToUnicode(getBijoyValue())), 'বিজয় লেখার সাধারণ ত্রুটি ঠিক করা হয়েছে।'),
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
            delete output.dataset.bijoyValue;
            output.style.removeProperty('font-family');
            setStatus('লেখা মুছে ফেলা হয়েছে।');
            input.focus();
        });

        converter.querySelector('[data-copy-converter]').addEventListener('click', async () => {
            if (! getBijoyValue()) {
                setStatus('কপি করার মতো কোনো লেখা নেই।');
                return;
            }

            await navigator.clipboard.writeText(getBijoyValue());
            setStatus('রূপান্তরিত লেখা কপি হয়েছে।');
            showFluxToast('বিজয় লেখা কপি হয়েছে।');
        });

        converter.querySelector('[data-copy-unicode]').addEventListener('click', async () => {
            if (! input.value) {
                setStatus('কপি করার মতো কোনো ইউনিকোড লেখা নেই।');
                showFluxToast('কপি করার মতো কোনো ইউনিকোড লেখা নেই।', 'warning');

                return;
            }

            await navigator.clipboard.writeText(input.value);
            setStatus('ইউনিকোড লেখা কপি হয়েছে।');
            showFluxToast('ইউনিকোড লেখা কপি হয়েছে।');
        });

        output.addEventListener('input', () => {
            delete output.dataset.bijoyValue;
            output.style.removeProperty('font-family');
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
