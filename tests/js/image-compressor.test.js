import assert from 'node:assert/strict';
import test from 'node:test';

import { estimateBackgroundColor, hexToRgb, removeImageBackground } from '../../resources/js/image-compressor.js';

test('estimates the background from the image corners', () => {
    const pixels = new Uint8ClampedArray([
        250, 250, 250, 255, 250, 250, 250, 255,
        250, 250, 250, 255, 20, 20, 20, 255,
    ]);

    assert.deepEqual(estimateBackgroundColor(pixels, 2, 2), [250, 250, 250]);
});

test('makes matching background pixels transparent while preserving the subject', () => {
    const imageData = {
        width: 2,
        height: 2,
        data: new Uint8ClampedArray([
            255, 255, 255, 255, 255, 255, 255, 255,
            255, 255, 255, 255, 0, 0, 0, 255,
        ]),
    };

    const result = removeImageBackground(imageData, 30);

    assert.equal(result.data[3], 0);
    assert.equal(result.data[15], 255);
});

test('replaces the removed area with a selected solid color', () => {
    const imageData = {
        width: 1,
        height: 1,
        data: new Uint8ClampedArray([255, 255, 255, 255]),
    };

    const result = removeImageBackground(imageData, 30, '#16a34a');

    assert.deepEqual([...result.data], [22, 163, 74, 255]);
    assert.deepEqual(hexToRgb('#16a34a'), [22, 163, 74]);
});
