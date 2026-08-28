export const noticeArchiveUrl = (baseUrl, search = '', category = '', page = '') => {
    const url = new URL(baseUrl, 'https://example.test');

    if (search.trim()) {
        url.searchParams.set('search', search.trim());
    } else {
        url.searchParams.delete('search');
    }

    if (category.trim()) {
        url.searchParams.set('category', category.trim());
    } else {
        url.searchParams.delete('category');
    }

    if (page) {
        url.searchParams.set('page', page);
    } else {
        url.searchParams.delete('page');
    }

    return `${url.pathname}${url.search}`;
};

const liveNoticeArchives = new WeakMap();

export const initializeNoticeArchives = () => {
    document.querySelectorAll('[data-notice-archive]').forEach((archive) => {
        if (liveNoticeArchives.has(archive)) {
            return;
        }

        const state = { controller: null, timeout: null };
        const form = archive.querySelector('[data-notice-search]');
        const searchInput = form.querySelector('[name="search"]');
        const categoryInput = form.querySelector('[name="category"]');

        const loadResults = async (url, { pushHistory = true, focusSearch = false } = {}) => {
            state.controller?.abort();
            state.controller = new AbortController();
            archive.dataset.loading = 'true';
            archive.setAttribute('aria-busy', 'true');

            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: state.controller.signal,
                });

                if (! response.ok) {
                    throw new Error('নোটিশ লোড করা যায়নি।');
                }

                const page = new DOMParser().parseFromString(await response.text(), 'text/html');
                const nextArchive = page.querySelector('[data-notice-archive]');

                if (! nextArchive) {
                    throw new Error('নোটিশের ফলাফল পাওয়া যায়নি।');
                }

                archive.replaceWith(nextArchive);
                if (pushHistory) {
                    window.history.pushState({}, '', url);
                }
                initializeNoticeArchives();

                if (focusSearch) {
                    const nextInput = nextArchive.querySelector('[name="search"]');
                    nextInput.focus();
                    nextInput.setSelectionRange(nextInput.value.length, nextInput.value.length);
                }
            } catch (error) {
                if (error.name !== 'AbortError') {
                    archive.querySelector('[data-live-search-status]').textContent = error.message;
                }
            } finally {
                archive.dataset.loading = 'false';
                archive.removeAttribute('aria-busy');
            }
        };

        searchInput.addEventListener('input', () => {
            window.clearTimeout(state.timeout);
            archive.querySelector('[data-live-search-status]').textContent = 'খোঁজা হচ্ছে…';
            state.timeout = window.setTimeout(() => {
                loadResults(noticeArchiveUrl(form.action, searchInput.value, categoryInput?.value ?? ''), { focusSearch: true });
            }, 350);
        });

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            window.clearTimeout(state.timeout);
            loadResults(noticeArchiveUrl(form.action, searchInput.value, categoryInput?.value ?? ''), { focusSearch: true });
        });

        archive.addEventListener('click', (event) => {
            const navigation = event.target.closest('[data-notice-navigation], [data-notice-pagination] a');

            if (! navigation || event.ctrlKey || event.metaKey || event.shiftKey) {
                return;
            }

            event.preventDefault();
            loadResults(navigation.href);
        });
        archive.addEventListener('notice:popstate', () => loadResults(window.location.href, { pushHistory: false }));

        liveNoticeArchives.set(archive, state);
    });
};

if (typeof window !== 'undefined') {
    window.addEventListener('popstate', () => {
        const archive = document.querySelector('[data-notice-archive]');
        const state = liveNoticeArchives.get(archive);

        if (archive && state) {
            archive.dispatchEvent(new Event('notice:popstate'));
        }
    });
}
