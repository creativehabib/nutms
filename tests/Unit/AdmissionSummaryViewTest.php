<?php

it('uses a Choices.js enhanced college selector', function (): void {
    $view = file_get_contents(__DIR__.'/../../resources/views/livewire/admission-summary.blade.php');
    $javascript = file_get_contents(__DIR__.'/../../resources/js/app.js');

    expect($view)
        ->toContain('<select wire:model.live="selectedCollege" data-searchable-select')
        ->toContain('<div wire:ignore>')
        ->and($javascript)
        ->toContain("import Choices from 'choices.js';")
        ->toContain("searchFields: ['label', 'value']")
        ->toContain("document.addEventListener('livewire:navigated', initializeSearchableSelects);");
});
