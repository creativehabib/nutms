import { convertBijoyToUnicode, convertUnicodeToBijoy } from './bangla-converter.js';

export const paperSizes = Object.freeze({
    A4: { width: 210, height: 297 },
    Letter: { width: 215.9, height: 279.4 },
    Legal: { width: 215.9, height: 355.6 },
});

export const documentPageGeometry = (paper = 'A4', orientation = 'portrait') => {
    const size = paperSizes[paper] ?? paperSizes.A4;

    return orientation === 'landscape'
        ? { width: size.height, height: size.width }
        : { width: size.width, height: size.height };
};

export const documentStatistics = (content = '') => {
    const text = content.replace(/<[^>]*>/g, ' ').replace(/&nbsp;/g, ' ').replace(/\s+/g, ' ').trim();

    return { words: text === '' ? 0 : text.split(' ').length, characters: text.length };
};

export const documentPrintStyles = (width, height) => `@media print { @page { size: ${width}mm ${height}mm; margin: 0; } }`;

export const documentTableMarkup = (rows = 3, columns = 3) => {
    const cells = () => Array.from({ length: columns }, () => '<td><br></td>').join('');

    return `<table><tbody>${Array.from({ length: rows }, () => `<tr>${cells()}</tr>`).join('')}</tbody></table><p><br></p>`;
};

const editorInstances = new WeakSet();

export const initializeDocumentEditors = () => {
    document.querySelectorAll('[data-document-editor]').forEach((workspace) => {
        if (editorInstances.has(workspace)) return;

        const pages = workspace.querySelector('[data-document-pages]');
        const pageTemplate = workspace.querySelector('[data-page-template]');
        const printRules = workspace.querySelector('[data-print-rules]');
        const title = workspace.querySelector('[data-document-title]');
        const paper = workspace.querySelector('[data-paper-size]');
        const orientationInputs = [...workspace.querySelectorAll('[name="document-orientation"]')];
        const marginInputs = [...workspace.querySelectorAll('[data-margin]')];
        const typingModeInputs = [...workspace.querySelectorAll('[name="document-typing-mode"]')];
        const headerEnabled = workspace.querySelector('[data-header-enabled]');
        const footerEnabled = workspace.querySelector('[data-footer-enabled]');
        const headerInput = workspace.querySelector('[data-header-text]');
        const footerInput = workspace.querySelector('[data-footer-text]');
        const zoom = workspace.querySelector('[data-zoom]');
        const status = workspace.querySelector('[data-save-status]');
        const converter = workspace.querySelector('[data-document-converter]');
        const storageKey = 'nutms-document-editor-v3';
        let activeEditor;
        let saveTimer;

        const editors = () => [...pages.querySelectorAll('[data-editor-canvas]')];
        const selectedOrientation = () => orientationInputs.find((input) => input.checked)?.value ?? 'portrait';
        const selectedTypingMode = () => typingModeInputs.find((input) => input.checked)?.value ?? 'unicode';
        const margins = () => Object.fromEntries(marginInputs.map((input) => [input.dataset.margin, Math.max(0, Number(input.value) || 0)]));
        const setActiveEditor = (editor) => {
            activeEditor = editor;
            editors().forEach((item) => item.closest('[data-page-sheet]').classList.toggle('is-active', item === editor));
        };
        const updatePageNumbers = () => {
            const total = editors().length;
            pages.querySelectorAll('[data-page-sheet]').forEach((sheet, index) => {
                sheet.querySelector('[data-page-number]').textContent = `পৃষ্ঠা ${index + 1}`;
                sheet.querySelector('[data-page-label]').textContent = `পৃষ্ঠা ${index + 1}`;
                sheet.querySelector('[data-delete-page]').disabled = total === 1;
            });
            workspace.querySelector('[data-page-count]').textContent = `${total.toLocaleString('bn-BD')} পৃষ্ঠা`;
        };
        const updateStatistics = () => {
            const statistics = documentStatistics(editors().map((editor) => editor.innerHTML).join(' '));
            workspace.querySelector('[data-word-count]').textContent = statistics.words.toLocaleString('bn-BD');
            workspace.querySelector('[data-character-count]').textContent = statistics.characters.toLocaleString('bn-BD');
        };
        const syncPageDecorations = () => {
            pages.querySelectorAll('[data-page-header]').forEach((header) => {
                header.hidden = ! headerEnabled.checked;
                header.querySelector('[data-header-copy]').textContent = headerInput.value;
            });
            pages.querySelectorAll('[data-page-footer]').forEach((footer) => {
                footer.hidden = ! footerEnabled.checked;
                footer.querySelector('[data-footer-copy]').textContent = footerInput.value;
            });
        };
        const syncTypingMode = () => {
            const usesBijoy = selectedTypingMode() === 'bijoy';
            workspace.style.setProperty('--editor-font', usesBijoy ? "'SutonnyMJ', sans-serif" : "'Noto Sans Bengali', sans-serif");
        };
        const applyPageSetup = () => {
            const geometry = documentPageGeometry(paper.value, selectedOrientation());
            const margin = margins();
            workspace.style.setProperty('--page-width', `${geometry.width}mm`);
            workspace.style.setProperty('--page-height', `${geometry.height}mm`);
            workspace.style.setProperty('--margin-top', `${margin.top}mm`);
            workspace.style.setProperty('--margin-right', `${margin.right}mm`);
            workspace.style.setProperty('--margin-bottom', `${margin.bottom}mm`);
            workspace.style.setProperty('--margin-left', `${margin.left}mm`);
            workspace.style.setProperty('--editor-scale', Number(zoom.value) / 100);
            workspace.querySelector('[data-zoom-label]').textContent = `${zoom.value}%`;
            printRules.textContent = documentPrintStyles(geometry.width, geometry.height);
        };
        const documentPayload = () => ({
            title: title.value,
            pages: editors().map((editor) => editor.innerHTML),
            paper: paper.value,
            orientation: selectedOrientation(),
            margins: margins(),
            headerEnabled: headerEnabled.checked,
            footerEnabled: footerEnabled.checked,
            header: headerInput.value,
            footer: footerInput.value,
            typingMode: selectedTypingMode(),
            updatedAt: new Date().toISOString(),
        });
        const save = () => {
            localStorage.setItem(storageKey, JSON.stringify(documentPayload()));
            status.textContent = 'সব পরিবর্তন সংরক্ষিত';
        };
        const scheduleSave = () => {
            status.textContent = 'সংরক্ষণ হচ্ছে…';
            window.clearTimeout(saveTimer);
            saveTimer = window.setTimeout(save, 500);
        };
        const bindPage = (sheet) => {
            const editor = sheet.querySelector('[data-editor-canvas]');
            editor.addEventListener('focus', () => setActiveEditor(editor));
            editor.addEventListener('input', () => {
                const isOverflowing = editor.scrollHeight > editor.clientHeight + 2;
                sheet.classList.toggle('has-overflow', isOverflowing);
                if (isOverflowing) status.textContent = 'পৃষ্ঠা পূর্ণ—নতুন পৃষ্ঠা যোগ করুন';
                updateStatistics();
                scheduleSave();
            });
            editor.addEventListener('keydown', (event) => {
                if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') {
                    event.preventDefault();
                    addPage();
                    scheduleSave();
                }
            });
            sheet.querySelector('[data-delete-page]').addEventListener('click', () => {
                if (editors().length === 1) return;
                const nextEditor = sheet.nextElementSibling?.querySelector('[data-editor-canvas]')
                    ?? sheet.previousElementSibling?.querySelector('[data-editor-canvas]');
                sheet.remove();
                setActiveEditor(nextEditor);
                updatePageNumbers();
                updateStatistics();
                scheduleSave();
            });
        };
        const addPage = (content = '<p><br></p>', focus = true) => {
            const sheet = pageTemplate.content.firstElementChild.cloneNode(true);
            sheet.querySelector('[data-editor-canvas]').innerHTML = content;
            pages.appendChild(sheet);
            bindPage(sheet);
            syncPageDecorations();
            updatePageNumbers();
            if (focus) {
                setActiveEditor(sheet.querySelector('[data-editor-canvas]'));
                activeEditor.focus();
                sheet.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        };
        const restore = () => {
            let saved;
            try { saved = JSON.parse(localStorage.getItem(storageKey)); } catch { localStorage.removeItem(storageKey); }
            const legacy = (() => { try { return JSON.parse(localStorage.getItem('nutms-document-editor-v2')); } catch { return null; } })();
            saved ??= legacy;
            const savedPages = saved?.pages ?? (saved?.content ? [saved.content] : null);
            if (saved) {
                title.value = saved.title || 'শিরোনামহীন ডকুমেন্ট';
                paper.value = saved.paper || 'A4';
                orientationInputs.forEach((input) => { input.checked = input.value === (saved.orientation || 'portrait'); });
                marginInputs.forEach((input) => { input.value = saved.margins?.[input.dataset.margin] ?? 20; });
                headerEnabled.checked = saved.headerEnabled ?? true;
                footerEnabled.checked = saved.footerEnabled ?? true;
                headerInput.value = saved.header || '';
                footerInput.value = saved.footer || '';
                typingModeInputs.forEach((input) => { input.checked = input.value === (saved.typingMode || 'unicode'); });
            }
            (savedPages?.length ? savedPages : ['<h1>আপনার ডকুমেন্টের শিরোনাম</h1><p>এখানে লেখা শুরু করুন। নতুন পৃষ্ঠা যোগ করতে “পৃষ্ঠা যোগ করুন” বাটনে ক্লিক করুন।</p>'])
                .forEach((content) => addPage(content, false));
            setActiveEditor(editors()[0]);
        };
        const runCommand = (button) => {
            activeEditor?.focus();
            document.execCommand(button.dataset.command, false, button.dataset.value ?? null);
            updateStatistics();
            scheduleSave();
        };

        restore();
        syncPageDecorations();
        syncTypingMode();
        applyPageSetup();
        updateStatistics();
        updatePageNumbers();

        workspace.querySelectorAll('[data-command]').forEach((button) => button.addEventListener('click', () => runCommand(button)));
        workspace.querySelectorAll('[data-format-select]').forEach((select) => select.addEventListener('change', () => {
            activeEditor?.focus();
            document.execCommand(select.dataset.formatSelect, false, select.value);
            scheduleSave();
        }));
        workspace.querySelector('[data-text-color]').addEventListener('input', (event) => {
            activeEditor?.focus();
            document.execCommand('foreColor', false, event.target.value);
            scheduleSave();
        });
        workspace.querySelector('[data-insert-table]').addEventListener('click', () => {
            activeEditor?.focus();
            document.execCommand('insertHTML', false, documentTableMarkup());
            updateStatistics();
            scheduleSave();
        });
        workspace.querySelector('[data-insert-link]').addEventListener('click', () => {
            const url = window.prompt('লিংকের ঠিকানা লিখুন (যেমন: https://example.com)');
            if (! url) return;
            const normalizedUrl = /^(https?:|mailto:|\/)/i.test(url) ? url : `https://${url}`;
            activeEditor?.focus();
            document.execCommand('createLink', false, normalizedUrl);
            scheduleSave();
        });
        workspace.querySelector('[data-add-page]').addEventListener('click', () => { addPage(); scheduleSave(); });
        [paper, zoom, ...orientationInputs, ...marginInputs].forEach((input) => input.addEventListener('input', () => { applyPageSetup(); scheduleSave(); }));
        [headerInput, footerInput, headerEnabled, footerEnabled].forEach((input) => input.addEventListener('input', () => { syncPageDecorations(); scheduleSave(); }));
        typingModeInputs.forEach((input) => input.addEventListener('change', () => { syncTypingMode(); scheduleSave(); }));
        title.addEventListener('input', scheduleSave);
        workspace.querySelector('[data-print-document]').addEventListener('click', () => { save(); window.print(); });
        workspace.querySelector('[data-clear-document]').addEventListener('click', () => {
            if (! window.confirm('ডকুমেন্টের সব পৃষ্ঠা ও লেখা মুছে ফেলতে চান?')) return;
            pages.innerHTML = '';
            addPage();
            updateStatistics();
            save();
        });
        workspace.querySelector('[data-fullscreen]').addEventListener('click', () => workspace.requestFullscreen?.());
        workspace.querySelector('[data-open-converter]').addEventListener('click', () => converter.showModal());
        workspace.querySelector('[data-close-converter]').addEventListener('click', () => converter.close());
        workspace.querySelector('[data-convert-to-bijoy]').addEventListener('click', () => {
            workspace.querySelector('[data-converter-bijoy]').value = convertUnicodeToBijoy(workspace.querySelector('[data-converter-unicode]').value);
        });
        workspace.querySelector('[data-convert-to-unicode]').addEventListener('click', () => {
            workspace.querySelector('[data-converter-unicode]').value = convertBijoyToUnicode(workspace.querySelector('[data-converter-bijoy]').value);
        });
        workspace.querySelector('[data-insert-converted]').addEventListener('click', () => {
            const unicodeValue = workspace.querySelector('[data-converter-unicode]').value;
            const bijoyValue = workspace.querySelector('[data-converter-bijoy]').value;
            const convertedValue = selectedTypingMode() === 'bijoy'
                ? (bijoyValue || convertUnicodeToBijoy(unicodeValue))
                : (unicodeValue || convertBijoyToUnicode(bijoyValue));
            activeEditor?.focus();
            document.execCommand('insertText', false, convertedValue);
            updateStatistics();
            scheduleSave();
            converter.close();
        });
        workspace.addEventListener('keydown', (event) => {
            if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 's') {
                event.preventDefault();
                save();
            }
        });

        editorInstances.add(workspace);
    });
};
