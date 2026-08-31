import assert from 'node:assert/strict';
import test from 'node:test';
import { documentPageGeometry, documentStatistics } from '../../resources/js/document-editor.js';

test('documentPageGeometry returns portrait and landscape measurements', () => {
    assert.deepEqual(documentPageGeometry('A4', 'portrait'), { width: 210, height: 297 });
    assert.deepEqual(documentPageGeometry('A4', 'landscape'), { width: 297, height: 210 });
    assert.deepEqual(documentPageGeometry('Letter', 'portrait'), { width: 215.9, height: 279.4 });
});

test('documentStatistics counts visible words and characters', () => {
    assert.deepEqual(documentStatistics('<p>জাতীয় বিশ্ববিদ্যালয়</p><p>Hello&nbsp;world</p>'), { words: 4, characters: 31 });
    assert.deepEqual(documentStatistics('<p><br></p>'), { words: 0, characters: 0 });
});
