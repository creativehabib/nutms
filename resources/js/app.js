import Choices from 'choices.js';
import 'choices.js/public/assets/styles/choices.min.css';

const searchableSelects = new WeakMap();

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
document.addEventListener('livewire:navigated', initializeSearchableSelects);
document.addEventListener('reset-teacher-filters', () => {
    document.querySelectorAll('[data-teacher-filter]').forEach((select) => {
        searchableSelects.get(select)?.setChoiceByValue('');
    });
});
