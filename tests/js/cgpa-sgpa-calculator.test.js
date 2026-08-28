import assert from 'node:assert/strict';
import test from 'node:test';

import { calculateWeightedGpa, gradeFromMarks } from '../../resources/js/cgpa-sgpa-calculator.js';

test('maps national university mark boundaries to grades', () => {
    assert.deepEqual(gradeFromMarks(80), { letter: 'A+', point: 4 });
    assert.deepEqual(gradeFromMarks(75), { letter: 'A', point: 3.75 });
    assert.deepEqual(gradeFromMarks(40), { letter: 'D', point: 2 });
    assert.deepEqual(gradeFromMarks(39.99), { letter: 'F', point: 0 });
});

test('rejects blank and out of range marks', () => {
    assert.equal(gradeFromMarks(''), null);
    assert.equal(gradeFromMarks(-1), null);
    assert.equal(gradeFromMarks(101), null);
});

test('calculates a credit-weighted gpa', () => {
    const result = calculateWeightedGpa([
        { point: 4, credits: 3 },
        { point: 3.5, credits: 2 },
        { point: 3, credits: 1 },
    ]);

    assert.equal(result.credits, 6);
    assert.equal(result.qualityPoints, 22);
    assert.equal(result.gpa, 22 / 6);
});

test('ignores incomplete rows in the weighted result', () => {
    assert.deepEqual(calculateWeightedGpa([
        { point: undefined, credits: 3 },
        { point: 4, credits: 0 },
    ]), { credits: 0, qualityPoints: 0, gpa: 0 });
});
