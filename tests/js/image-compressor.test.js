import assert from 'node:assert/strict';
import test from 'node:test';

import { calculateCoverCrop, createConnectedBackgroundMask, estimateBackgroundColor, hexToRgb, removeImageBackground } from '../../resources/js/image-compressor.js';

test('positions a cover crop using the selected focal point', () => {
    assert.deepEqual(calculateCoverCrop(800, 400, 300, 300, 0, 0.5), {
        sourceWidth: 400,
        sourceHeight: 400,
        sourceX: 0,
        sourceY: 0,
    });
    assert.deepEqual(calculateCoverCrop(800, 400, 300, 300, 1, 0.5), {
        sourceWidth: 400,
        sourceHeight: 400,
        sourceX: 400,
        sourceY: 0,
    });
});

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

test('preserves background-colored details enclosed inside the subject', () => {
    const size = 5;
    const data = new Uint8ClampedArray(size * size * 4);

    for (let index = 0; index < size * size; index += 1) {
        const isBorder = index < size || index >= size * (size - 1) || index % size === 0 || index % size === size - 1;
        const isEnclosedCenter = index === 12;
        const color = isBorder || isEnclosedCenter ? 255 : 0;
        data.set([color, color, color, 255], index * 4);
    }

    const result = removeImageBackground({ width: size, height: size, data }, 30);

    assert.equal(result.data[3], 0);
    assert.equal(result.data[(12 * 4) + 3], 255);
});

test('only marks background-colored pixels connected to an outer edge', () => {
    const pixels = new Uint8ClampedArray([
        255, 255, 255, 255, 0, 0, 0, 255, 255, 255, 255, 255,
    ]);
    const mask = createConnectedBackgroundMask(pixels, 3, 1, [255, 255, 255], 30);

    assert.deepEqual([...mask.connected], [1, 0, 1]);
});
