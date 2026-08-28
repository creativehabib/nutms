import assert from 'node:assert/strict';
import test from 'node:test';

import { addCalendarYears, calculateCalendarDifference } from '../../resources/js/age-retirement-calculator.js';

test('calculates exact calendar age in years, months and days', () => {
    assert.deepEqual(calculateCalendarDifference('1985-05-15', '2026-08-28'), {
        years: 41,
        months: 3,
        days: 13,
    });
});

test('borrows the correct number of days from the previous month', () => {
    assert.deepEqual(calculateCalendarDifference('2020-01-31', '2020-03-01'), {
        years: 0,
        months: 1,
        days: 1,
    });
});

test('rejects a calculation date before the start date', () => {
    assert.throws(
        () => calculateCalendarDifference('2026-08-29', '2026-08-28'),
        /শুরুর তারিখ/,
    );
});

test('uses the last valid February date for leap-day retirement anniversaries', () => {
    assert.equal(addCalendarYears('1980-02-29', 59), '2039-02-28');
});
