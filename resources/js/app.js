import Choices from 'choices.js';
import EmblaCarousel from 'embla-carousel';
import 'choices.js/public/assets/styles/choices.min.css';

const searchableSelects = new WeakMap();
const relatedCollegeCarousels = new WeakMap();

const initializeRelatedCollegeCarousels = () => {
    document.querySelectorAll('[data-related-colleges-carousel]').forEach((carousel) => {
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

const initializeSearchableSelects = () => {
    document.querySelectorAll('[data-searchable-select]').forEach((select) => {
        if (select.dataset.choice === 'active') {
            return;
        }

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
