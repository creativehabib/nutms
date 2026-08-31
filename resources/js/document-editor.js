import { convertBijoyToUnicode, convertUnicodeToBijoy } from './bangla-converter.js';

export const paperSizes = Object.freeze({
    A4: { width: 210, height: 297, label: 'A4' },
    Letter: { width: 215.9, height: 279.4, label: 'Letter' },
    Legal: { width: 215.9, height: 355.6, label: 'Legal' },
});

export const documentPageGeometry = (paper = 'A4', orientation = 'portrait') => {
    const size = paperSizes[paper] ?? paperSizes.A4;

    return orientation === 'landscape'
        ? { width: size.height, height: size.width }
        : { width: size.width, height: size.height };
};

export const documentStatistics = (content = '') => {
    const text = content.replace(/<[^>]*>/g, ' ').replace(/&nbsp;/g, ' ').replace(/\s+/g, ' ').trim();

    return {
        words: text === '' ? 0 : text.split(' ').length,
        characters: text.length,
    };
};

export const documentPrintStyles = (width, height) => `@media print { @page { size: ${width}mm ${height}mm; margin: 0; } }`;

const editorInstances = new WeakSet();

export const initializeDocumentEditors = () => {
    document.querySelectorAll('[data-document-editor]').forEach((workspace) => {
        if (editorInstances.has(workspace)) {
            return;
        }

        const editor = workspace.querySelector('[data-editor-canvas]');
        const printRules = workspace.querySelector('[data-print-rules]');
        const title = workspace.querySelector('[data-document-title]');
        const paper = workspace.querySelector('[data-paper-size]');
        const orientationInputs = [...workspace.querySelectorAll('[name="document-orientation"]')];
        const marginInputs = [...workspace.querySelectorAll('[data-margin]')];
        const headerInput = workspace.querySelector('[data-header-text]');
        const footerInput = workspace.querySelector('[data-footer-text]');
        const header = workspace.querySelector('[data-page-header]');
        const footer = workspace.querySelector('[data-page-footer]');
        const zoom = workspace.querySelector('[data-zoom]');
        const status = workspace.querySelector('[data-save-status]');
        const typingModeInputs = [...workspace.querySelectorAll('[name="document-typing-mode"]')];
        const converter = workspace.querySelector('[data-document-converter]');
        const storageKey = 'nutms-document-editor-v2';
        let saveTimer;

        const selectedOrientation = () => orientationInputs.find((input) => input.checked)?.value ?? 'portrait';
        const margins = () => Object.fromEntries(marginInputs.map((input) => [input.dataset.margin, Math.max(0, Number(input.value) || 0)]));
        const updateStatistics = () => {
            const stats = documentStatistics(editor.innerHTML);
            workspace.querySelector('[data-word-count]').textContent = stats.words.toLocaleString('bn-BD');
            workspace.querySelector('[data-character-count]').textContent = stats.characters.toLocaleString('bn-BD');
        };
        const applyPageSetup = () => {
            const geometry = documentPageGeometry(paper.value, selectedOrientation());
            const margin = margins();
            const scale = Number(zoom.value) / 100;

            workspace.style.setProperty('--page-width', `${geometry.width}mm`);
            workspace.style.setProperty('--page-height', `${geometry.height}mm`);
            workspace.style.setProperty('--margin-top', `${margin.top}mm`);
            workspace.style.setProperty('--margin-right', `${margin.right}mm`);
            workspace.style.setProperty('--margin-bottom', `${margin.bottom}mm`);
            workspace.style.setProperty('--margin-left', `${margin.left}mm`);
            workspace.style.setProperty('--editor-scale', scale);
            workspace.querySelector('[data-zoom-label]').textContent = `${zoom.value}%`;
            printRules.textContent = documentPrintStyles(geometry.width, geometry.height);
        };
        const save = () => {
            const payload = {
                title: title.value,
                content: editor.innerHTML,
                paper: paper.value,
                orientation: selectedOrientation(),
                margins: margins(),
                header: headerInput.value,
                footer: footerInput.value,
                typingMode: typingModeInputs.find((input) => input.checked)?.value ?? 'unicode',
                updatedAt: new Date().toISOString(),
            };
            localStorage.setItem(storageKey, JSON.stringify(payload));
            status.textContent = 'সব পরিবর্তন সংরক্ষিত';
            status.dataset.state = 'saved';
        };
        const scheduleSave = () => {
            status.textContent = 'সংরক্ষণ হচ্ছে…';
            status.dataset.state = 'saving';
            window.clearTimeout(saveTimer);
            saveTimer = window.setTimeout(save, 500);
        };
        const syncHeaderFooter = () => {
            header.textContent = headerInput.value;
            footer.querySelector('[data-footer-copy]').textContent = footerInput.value;
        };
        const syncTypingMode = () => {
            const mode = typingModeInputs.find((input) => input.checked)?.value ?? 'unicode';
            editor.dataset.typingMode = mode;
            editor.style.fontFamily = mode === 'bijoy'
                ? "'SutonnyMJ', sans-serif"
                : "'Noto Sans Bengali', sans-serif";
        };
        const restore = () => {
            try {
                const saved = JSON.parse(localStorage.getItem(storageKey));
                if (! saved) return;
                title.value = saved.title || 'শিরোনামহীন ডকুমেন্ট';
                editor.innerHTML = saved.content || '';
                paper.value = saved.paper || 'A4';
                orientationInputs.forEach((input) => { input.checked = input.value === (saved.orientation || 'portrait'); });
                marginInputs.forEach((input) => { input.value = saved.margins?.[input.dataset.margin] ?? 20; });
                headerInput.value = saved.header || '';
                footerInput.value = saved.footer || '';
                typingModeInputs.forEach((input) => { input.checked = input.value === (saved.typingMode || 'unicode'); });
            } catch {
                localStorage.removeItem(storageKey);
            }
        };
        const runCommand = (button) => {
            editor.focus();
            document.execCommand(button.dataset.command, false, button.dataset.value ?? null);
            updateStatistics();
            scheduleSave();
        };

        restore();
        syncHeaderFooter();
        syncTypingMode();
        applyPageSetup();
        updateStatistics();

        workspace.querySelectorAll('[data-command]').forEach((button) => button.addEventListener('click', () => runCommand(button)));
        workspace.querySelectorAll('[data-format-select]').forEach((select) => select.addEventListener('change', () => {
            editor.focus();
            document.execCommand(select.dataset.formatSelect, false, select.value);
            scheduleSave();
        }));
        workspace.querySelector('[data-text-color]').addEventListener('input', (event) => {
            editor.focus();
            document.execCommand('foreColor', false, event.target.value);
            scheduleSave();
        });
        [paper, zoom, ...orientationInputs, ...marginInputs].forEach((input) => input.addEventListener('input', () => { applyPageSetup(); scheduleSave(); }));
        [headerInput, footerInput].forEach((input) => input.addEventListener('input', () => { syncHeaderFooter(); scheduleSave(); }));
        typingModeInputs.forEach((input) => input.addEventListener('change', () => { syncTypingMode(); scheduleSave(); }));
        title.addEventListener('input', scheduleSave);
        editor.addEventListener('input', () => { updateStatistics(); scheduleSave(); });
        workspace.querySelector('[data-print-document]').addEventListener('click', () => window.print());
        workspace.querySelector('[data-clear-document]').addEventListener('click', () => {
            if (window.confirm('ডকুমেন্টের সব লেখা মুছে ফেলতে চান?')) {
                editor.innerHTML = '<p><br></p>';
                updateStatistics();
                save();
                editor.focus();
            }
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
            const usesBijoy = editor.dataset.typingMode === 'bijoy';
            const convertedValue = usesBijoy
                ? (bijoyValue || convertUnicodeToBijoy(unicodeValue))
                : (unicodeValue || convertBijoyToUnicode(bijoyValue));
            editor.focus();
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
