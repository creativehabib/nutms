import assert from 'node:assert/strict';
import test from 'node:test';

import { noticeArchiveUrl } from '../../resources/js/notice-archive.js';

test('builds a trimmed live notice search URL and resets pagination', () => {
    assert.equal(
        noticeArchiveUrl('https://portal.test/notices?page=4', '  পরীক্ষা  ', 'পরীক্ষা'),
        '/notices?search=%E0%A6%AA%E0%A6%B0%E0%A7%80%E0%A6%95%E0%A7%8D%E0%A6%B7%E0%A6%BE&category=%E0%A6%AA%E0%A6%B0%E0%A7%80%E0%A6%95%E0%A7%8D%E0%A6%B7%E0%A6%BE',
    );
});

test('removes empty search and category parameters', () => {
    assert.equal(noticeArchiveUrl('https://portal.test/notices?search=old&category=old', '', ''), '/notices');
});
