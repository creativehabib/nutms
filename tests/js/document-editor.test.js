import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';
import { documentPageGeometry, documentPrintStyles, documentStatistics, documentTableMarkup } from '../../resources/js/document-editor.js';

test('documentPageGeometry returns portrait and landscape measurements', () => {
    assert.deepEqual(documentPageGeometry('A4', 'portrait'), { width: 210, height: 297 });
    assert.deepEqual(documentPageGeometry('A4', 'landscape'), { width: 297, height: 210 });
    assert.deepEqual(documentPageGeometry('Letter', 'portrait'), { width: 215.9, height: 279.4 });
});

test('documentPrintStyles creates a zero-margin page matching the preview', () => {
    assert.equal(documentPrintStyles(297, 210), '@media print { @page { size: 297mm 210mm; margin: 0; } }');
});

test('documentTableMarkup creates an editable table of the requested size', () => {
    const markup = documentTableMarkup(2, 4);

    assert.equal((markup.match(/<tr>/g) ?? []).length, 2);
    assert.equal((markup.match(/<td>/g) ?? []).length, 8);
    assert.match(markup, /<p><br><\/p>$/);
});

test('print stylesheet keeps the document page and its contents visible', () => {
    const view = readFileSync('resources/views/livewire/document-editor/document-editor.blade.php', 'utf8');

    assert.match(view, /body \.document-pages, body \.document-pages \*, body \.document-page, body \.document-page \* \{ visibility:visible !important; \}/);
    assert.doesNotMatch(view, /body > \*:not\(\.document-studio\)/);
    assert.doesNotMatch(view, /\.document-page \{ position:fixed/);
    assert.match(view, /page-break-after:always/);
    assert.match(view, /data-command="justifyFull"/);
    assert.match(view, /data-insert-table/);
});

test('documentStatistics counts visible words and characters', () => {
    assert.deepEqual(documentStatistics('<p>জাতীয় বিশ্ববিদ্যালয়</p><p>Hello&nbsp;world</p>'), { words: 4, characters: 31 });
    assert.deepEqual(documentStatistics('<p><br></p>'), { words: 0, characters: 0 });
});
