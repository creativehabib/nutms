import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';
import { changeDocumentTable, deleteDocumentTableCells, documentPageGeometry, documentPrintStyles, documentStatistics, documentTableMarkup, mergeDocumentTableCells, transliteratePhoneticWord } from '../../resources/js/document-editor.js';

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

test('changeDocumentTable supports every table toolbar action', () => {
    const calls = [];
    const table = {
        rows: [{
            cells: [{}, {}],
            deleteCell: (index) => calls.push(['delete-cell', index]),
            insertCell: () => ({ set innerHTML(value) { calls.push(['add-cell', value]); } }),
        }],
        insertRow: (index) => ({ insertCell: () => ({ set innerHTML(value) { calls.push(['add-cell', value]); } }) }),
        remove: () => calls.push(['remove-table']),
    };
    const row = { rowIndex: 0, cells: [{}, {}], closest: () => table, remove: () => calls.push(['remove-row']) };
    const cell = { cellIndex: 0, closest: (selector) => selector === 'tr' ? row : null };

    assert.equal(changeDocumentTable(cell, 'add-row'), true);
    assert.equal(changeDocumentTable(cell, 'add-column'), true);
    assert.equal(changeDocumentTable(cell, 'delete-row'), true);
    assert.equal(changeDocumentTable(cell, 'delete-column'), true);
    assert.equal(changeDocumentTable(cell, 'delete-table'), true);
    assert.deepEqual(calls, [
        ['add-cell', '<br>'], ['add-cell', '<br>'],
        ['add-cell', '<br>'],
        ['remove-table'],
        ['delete-cell', 0],
        ['remove-table'],
    ]);
});

test('deleteDocumentTableCells clears every selected cell', () => {
    const cells = [{ innerHTML: 'এক' }, { innerHTML: 'দুই' }];

    assert.equal(deleteDocumentTableCells(cells), true);
    assert.deepEqual(cells.map((cell) => cell.innerHTML), ['<br>', '<br>']);
});

test('deleteDocumentTableCells removes a fully selected column', () => {
    const deletedColumns = [];
    const table = { rows: [] };
    const cells = [0, 1].map(() => ({ cellIndex: 1, closest: () => table }));
    table.rows = cells.map((cell) => ({ cells: [{}, cell], deleteCell: (index) => deletedColumns.push(index) }));

    assert.equal(deleteDocumentTableCells(cells), true);
    assert.deepEqual(deletedColumns, [1, 1]);
});

test('mergeDocumentTableCells merges a rectangular selection', () => {
    const table = { rows: [] };
    table.rows = [0, 1].map((rowIndex) => {
        const row = { rowIndex, cells: [] };
        row.cells = [0, 1].map((cellIndex) => ({
            cellIndex,
            colSpan: 1,
            rowSpan: 1,
            innerHTML: `${rowIndex}-${cellIndex}`,
            parentElement: row,
            closest: () => table,
            remove() { this.removed = true; },
        }));
        return row;
    });

    const cells = table.rows.flatMap((row) => row.cells);
    assert.equal(mergeDocumentTableCells(cells), true);
    assert.equal(cells[0].rowSpan, 2);
    assert.equal(cells[0].colSpan, 2);
    assert.equal(cells[0].innerHTML, '0-0<br>0-1<br>1-0<br>1-1');
    assert.equal(cells.filter((cell) => cell.removed).length, 3);
});

test('built-in phonetic input creates Unicode Bangla without desktop software', () => {
    assert.equal(transliteratePhoneticWord('ami'), 'আমি');
    assert.equal(transliteratePhoneticWord('bangladesh'), 'বাংলাদেশ');
});

test('print stylesheet keeps the document page and its contents visible', () => {
    const view = readFileSync('resources/views/livewire/document-editor/document-editor.blade.php', 'utf8');

    assert.match(view, /body \.document-pages, body \.document-pages \*, body \.document-page, body \.document-page \* \{ visibility:visible !important; \}/);
    assert.doesNotMatch(view, /body > \*:not\(\.document-studio\)/);
    assert.doesNotMatch(view, /\.document-page \{ position:fixed/);
    assert.match(view, /page-break-after:always/);
    assert.match(view, /data-command="justifyFull"/);
    assert.match(view, /data-insert-table/);
    assert.match(view, /data-table-action="delete-table"/);
    assert.match(view, /data-table-context-action="merge"/);
    assert.match(view, /data-table-context-action="select-column"/);
    assert.match(view, /data-custom-font-size/);
    assert.match(view, /data-clear-format/);
    assert.match(view, />＋ Row</);
    assert.match(view, />Merge cells</);
    assert.match(view, /class="editor-toolbar/);
});

test('documentStatistics counts visible words and characters', () => {
    assert.deepEqual(documentStatistics('<p>জাতীয় বিশ্ববিদ্যালয়</p><p>Hello&nbsp;world</p>'), { words: 4, characters: 31 });
    assert.deepEqual(documentStatistics('<p><br></p>'), { words: 0, characters: 0 });
});
