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
            printRules.textContent = `@media print { @page { size: ${geometry.width}mm ${geometry.height}mm; margin: 0; } }`;
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
        workspace.addEventListener('keydown', (event) => {
            if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 's') {
                event.preventDefault();
                save();
            }
        });

        editorInstances.add(workspace);
    });
};
