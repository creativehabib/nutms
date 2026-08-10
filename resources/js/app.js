import Choices from 'choices.js';
import 'choices.js/public/assets/styles/choices.min.css';

const initializeSearchableSelects = () => {
    document.querySelectorAll('[data-searchable-select]').forEach((select) => {
        if (select.dataset.choice === 'active') {
            return;
        }

        new Choices(select, {
            allowHTML: false,
            itemSelectText: '',
            noResultsText: select.dataset.noResultsText,
            placeholder: true,
            searchEnabled: true,
            searchFields: ['label', 'value'],
            searchPlaceholderValue: select.dataset.searchPlaceholder,
            shouldSort: false,
        });
    });
};

document.addEventListener('DOMContentLoaded', initializeSearchableSelects);
document.addEventListener('livewire:navigated', initializeSearchableSelects);
