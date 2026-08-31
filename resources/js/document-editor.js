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

export const applyDocumentBlockSpacing = (blocks, property, value) => {
    const supportedProperties = ['lineHeight', 'marginBottom'];

    if (! supportedProperties.includes(property)) {
        return false;
    }

    const uniqueBlocks = [...new Set(blocks)].filter(Boolean);
    uniqueBlocks.forEach((block) => { block.style[property] = value; });

    return uniqueBlocks.length > 0;
};

export const documentTableMarkup = (rows = 3, columns = 3) => {
    const cells = () => Array.from({ length: columns }, () => '<td><br></td>').join('');

    return `<table><tbody>${Array.from({ length: rows }, () => `<tr>${cells()}</tr>`).join('')}</tbody></table><p><br></p>`;
};

export const changeDocumentTable = (cell, action) => {
    const row = cell?.closest?.('tr');
    const table = row?.closest?.('table');

    if (! cell || ! row || ! table) {
        return false;
    }

    if (action === 'add-row') {
        const newRow = table.insertRow(row.rowIndex + 1);
        Array.from({ length: row.cells.length }, () => {
            newRow.insertCell().innerHTML = '<br>';
        });
    } else if (action === 'add-column') {
        [...table.rows].forEach((tableRow) => {
            tableRow.insertCell(Math.min(cell.cellIndex + 1, tableRow.cells.length)).innerHTML = '<br>';
        });
    } else if (action === 'delete-row') {
        if (table.rows.length === 1) {
            table.remove();
        } else {
            row.remove();
        }
    } else if (action === 'delete-column') {
        if (row.cells.length === 1) {
            table.remove();
        } else {
            [...table.rows].forEach((tableRow) => tableRow.deleteCell(cell.cellIndex));
        }
    } else if (action === 'delete-table') {
        table.remove();
    } else {
        return false;
    }

    return true;
};

export const mergeDocumentTableCells = (cells) => {
    const selectedCells = [...new Set(cells)].filter((cell) => cell?.isConnected !== false);
    const table = selectedCells[0]?.closest?.('table');

    if (! table || selectedCells.length < 2 || selectedCells.some((cell) => cell.closest('table') !== table)) {
        return false;
    }

    const positions = selectedCells.map((cell) => ({ cell, row: cell.parentElement.rowIndex, column: cell.cellIndex }));
    const firstRow = Math.min(...positions.map(({ row }) => row));
    const lastRow = Math.max(...positions.map(({ row }) => row));
    const firstColumn = Math.min(...positions.map(({ column }) => column));
    const lastColumn = Math.max(...positions.map(({ column }) => column));
    const expectedCellCount = (lastRow - firstRow + 1) * (lastColumn - firstColumn + 1);

    if (expectedCellCount !== selectedCells.length || positions.some(({ cell }) => cell.rowSpan > 1 || cell.colSpan > 1)) {
        return false;
    }

    const firstCell = table.rows[firstRow].cells[firstColumn];
    const contents = positions
        .sort((first, second) => first.row - second.row || first.column - second.column)
        .map(({ cell }) => cell.innerHTML)
        .filter((content) => content && content !== '<br>');

    firstCell.innerHTML = contents.join('<br>') || '<br>';
    firstCell.rowSpan = lastRow - firstRow + 1;
    firstCell.colSpan = lastColumn - firstColumn + 1;
    positions.filter(({ cell }) => cell !== firstCell).forEach(({ cell }) => cell.remove());

    return true;
};

export const splitDocumentTableCell = (cell) => {
    const row = cell?.parentElement;
    const table = row?.closest?.('table');
    const rowSpan = Number(cell?.rowSpan) || 1;
    const columnSpan = Number(cell?.colSpan) || 1;

    if (! table || (rowSpan === 1 && columnSpan === 1)) {
        return false;
    }

    const rowIndex = row.rowIndex;
    const columnIndex = cell.cellIndex;
    cell.rowSpan = 1;
    cell.colSpan = 1;

    for (let columnOffset = 1; columnOffset < columnSpan; columnOffset++) {
        table.rows[rowIndex].insertCell(columnIndex + columnOffset).innerHTML = '<br>';
    }

    for (let rowOffset = 1; rowOffset < rowSpan; rowOffset++) {
        const targetRow = table.rows[rowIndex + rowOffset];
        for (let columnOffset = 0; columnOffset < columnSpan; columnOffset++) {
            targetRow.insertCell(Math.min(columnIndex + columnOffset, targetRow.cells.length)).innerHTML = '<br>';
        }
    }

    return true;
};

export const deleteDocumentTableCells = (cells) => {
    const selectedCells = [...new Set(cells)].filter((cell) => cell?.isConnected !== false);
    const table = selectedCells[0]?.closest?.('table');
    const tableCells = table ? [...table.rows].flatMap((row) => [...row.cells]) : [];
    const selectedColumn = selectedCells[0]?.cellIndex;
    const selectedRows = table
        ? [...table.rows].filter((row) => [...row.cells].every((cell) => selectedCells.includes(cell)))
        : [];
    const isCompleteColumn = table
        && selectedCells.length === table.rows.length
        && selectedCells.every((cell) => cell.closest('table') === table && cell.cellIndex === selectedColumn);

    if (table && selectedCells.length === tableCells.length) {
        table.remove();

        return true;
    }

    if (selectedRows.length > 0 && selectedRows.flatMap((row) => [...row.cells]).length === selectedCells.length) {
        selectedRows.forEach((row) => row.remove());

        return true;
    }

    if (isCompleteColumn) {
        if (table.rows[0].cells.length === 1) {
            table.remove();
        } else {
            [...table.rows].forEach((row) => row.deleteCell(selectedColumn));
        }

        return true;
    }

    selectedCells.forEach((cell) => { cell.innerHTML = '<br>'; });

    return selectedCells.length > 0;
};

export const documentTableTabTarget = (cell, backwards = false) => {
    const table = cell?.closest?.('table');

    if (! table) return null;

    const cells = [...table.rows].flatMap((row) => [...row.cells]);
    const currentIndex = cells.indexOf(cell);
    const targetCell = cells[currentIndex + (backwards ? -1 : 1)];

    if (targetCell) return targetCell;
    if (backwards || currentIndex === -1) return cells[0] ?? null;

    const referenceRow = cell.closest('tr');
    const newRow = table.insertRow(-1);
    Array.from({ length: Math.max(1, referenceRow.cells.length) }, () => {
        newRow.insertCell().innerHTML = '<br>';
    });

    return newRow.cells[0];
};

const phoneticConsonants = Object.freeze({
    ng: 'ং', kh: 'খ', gh: 'ঘ', ch: 'চ', chh: 'ছ', jh: 'ঝ', th: 'থ', dh: 'ধ', ph: 'ফ', bh: 'ভ', sh: 'শ', ss: 'ষ', rr: 'ড়',
    k: 'ক', g: 'গ', c: 'চ', j: 'জ', t: 'ত', d: 'দ', n: 'ন', p: 'প', f: 'ফ', b: 'ব', v: 'ভ', m: 'ম', z: 'য', r: 'র', l: 'ল', s: 'স', h: 'হ', y: 'য়', q: 'ক', x: 'ক্স', w: 'ও',
});
const phoneticVowels = Object.freeze({
    ou: ['ঔ', 'ৌ'], oi: ['ঐ', 'ৈ'], aa: ['আ', 'া'], ee: ['ঈ', 'ী'], oo: ['ঊ', 'ূ'],
    a: ['আ', 'া'], i: ['ই', 'ি'], u: ['উ', 'ু'], e: ['এ', 'ে'], o: ['ও', 'ো'],
});

export const transliteratePhoneticWord = (word) => {
    const input = word.toLowerCase();
    const tokens = [...new Set([...Object.keys(phoneticConsonants), ...Object.keys(phoneticVowels)])]
        .sort((first, second) => second.length - first.length);
    let result = '';
    let index = 0;
    let followsConsonant = false;

    while (index < input.length) {
        const token = tokens.find((candidate) => input.startsWith(candidate, index));
        if (! token) {
            result += input[index];
            followsConsonant = false;
            index++;
            continue;
        }
        if (phoneticVowels[token]) {
            result += phoneticVowels[token][followsConsonant ? 1 : 0];
            followsConsonant = false;
        } else {
            result += phoneticConsonants[token];
            followsConsonant = true;
        }
        index += token.length;
    }

    return result;
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
        const tableDialog = workspace.querySelector('[data-table-dialog]');
        const builtInKeyboard = workspace.querySelector('[data-built-in-keyboard]');
        const storageKey = 'nutms-document-editor-v3';
        let activeEditor;
        let activeTableCell;
        const selectedTableCells = new Set();
        let tableContextHideTimer;
        let dragStartCell;
        let isDraggingTableCells = false;
        let savedRange;
        let saveTimer;
        let pendingFontSize = 12;

        const editors = () => [...pages.querySelectorAll('[data-editor-canvas]')];
        const selectedOrientation = () => orientationInputs.find((input) => input.checked)?.value ?? 'portrait';
        const selectedTypingMode = () => typingModeInputs.find((input) => input.checked)?.value ?? 'unicode';
        const margins = () => Object.fromEntries(marginInputs.map((input) => [input.dataset.margin, Math.max(0, Number(input.value) || 0)]));
        const setActiveEditor = (editor) => {
            activeEditor = editor;
            editors().forEach((item) => item.closest('[data-page-sheet]').classList.toggle('is-active', item === editor));
        };
        const rememberSelection = () => {
            const selection = window.getSelection();
            if (selection?.rangeCount && activeEditor?.contains(selection.anchorNode)) {
                savedRange = selection.getRangeAt(0).cloneRange();
                workspace.querySelectorAll('[data-command]').forEach((button) => {
                    const statefulCommands = ['bold', 'italic', 'underline', 'strikeThrough', 'superscript', 'subscript', 'justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull', 'insertUnorderedList', 'insertOrderedList'];
                    button.classList.toggle('is-on', statefulCommands.includes(button.dataset.command) && document.queryCommandState(button.dataset.command));
                });
            }
        };
        const rememberTableCell = (target) => {
            const cell = target.nodeType === Node.ELEMENT_NODE ? target.closest('td, th') : target.parentElement?.closest('td, th');

            if (cell && activeEditor?.contains(cell)) {
                activeTableCell = cell;
            }
        };
        const clearTableSelection = () => {
            selectedTableCells.forEach((cell) => cell.classList.remove('is-selected'));
            selectedTableCells.clear();
        };
        const setTableToolsVisibility = (visible) => {
            workspace.querySelectorAll('[data-table-only]').forEach((tool) => { tool.hidden = ! visible; });
        };
        const normalizeCustomFontSize = (editor) => {
            editor?.querySelectorAll('font[size="7"]').forEach((font) => {
                font.removeAttribute('size');
                font.style.fontSize = `${pendingFontSize}pt`;
            });
        };
        const selectTableCells = (cells) => {
            clearTableSelection();
            cells.forEach((cell) => {
                selectedTableCells.add(cell);
                cell.classList.add('is-selected');
            });
        };
        const selectTableRectangle = (firstCell, lastCell) => {
            const table = firstCell?.closest('table');

            if (! table || lastCell?.closest('table') !== table) return;

            const firstRow = firstCell.parentElement.rowIndex;
            const lastRow = lastCell.parentElement.rowIndex;
            const firstColumn = firstCell.cellIndex;
            const lastColumn = lastCell.cellIndex;
            const rowStart = Math.min(firstRow, lastRow);
            const rowEnd = Math.max(firstRow, lastRow);
            const columnStart = Math.min(firstColumn, lastColumn);
            const columnEnd = Math.max(firstColumn, lastColumn);
            const cells = [...table.rows]
                .slice(rowStart, rowEnd + 1)
                .flatMap((row) => [...row.cells].slice(columnStart, columnEnd + 1));

            selectTableCells(cells);
        };
        const restoreSelection = () => {
            activeEditor?.focus();
            if (! savedRange) return;
            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(savedRange);
        };
        const selectedDocumentBlocks = () => {
            const selection = window.getSelection();
            const range = selection?.rangeCount ? selection.getRangeAt(0) : null;

            if (! range || ! activeEditor?.contains(range.commonAncestorContainer)) return [];

            const blockSelector = 'p, h1, h2, h3, li, blockquote, div';
            const startElement = range.startContainer.nodeType === Node.ELEMENT_NODE ? range.startContainer : range.startContainer.parentElement;
            const endElement = range.endContainer.nodeType === Node.ELEMENT_NODE ? range.endContainer : range.endContainer.parentElement;
            const boundaryBlocks = [startElement?.closest(blockSelector), endElement?.closest(blockSelector)]
                .filter((block) => block && activeEditor.contains(block));
            const intersectingBlocks = [...activeEditor.querySelectorAll(blockSelector)]
                .filter((block) => {
                    try {
                        return range.intersectsNode(block);
                    } catch {
                        return false;
                    }
                });

            return [...new Set([...boundaryBlocks, ...intersectingBlocks])];
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
            builtInKeyboard: builtInKeyboard.checked,
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
            editor.addEventListener('keyup', rememberSelection);
            editor.addEventListener('keyup', (event) => rememberTableCell(event.target));
            editor.addEventListener('mouseup', (event) => {
                rememberSelection();
                rememberTableCell(event.target);
            });
            editor.addEventListener('click', (event) => {
                const cell = event.target.closest?.('td, th');

                if (! cell || ! (event.ctrlKey || event.metaKey)) return;

                event.preventDefault();
                if (selectedTableCells.has(cell)) {
                    selectedTableCells.delete(cell);
                    cell.classList.remove('is-selected');
                } else {
                    selectedTableCells.add(cell);
                    cell.classList.add('is-selected');
                }
            });
            editor.addEventListener('mousedown', (event) => {
                const cell = event.target.closest?.('td, th');
                if (! cell || event.button !== 0) return;

                dragStartCell = cell;
                isDraggingTableCells = false;
            });
            editor.addEventListener('mouseover', (event) => {
                const cell = event.target.closest?.('td, th');
                if (! dragStartCell || ! cell || ! (event.buttons & 1) || cell === dragStartCell) return;

                isDraggingTableCells = true;
                selectTableRectangle(dragStartCell, cell);
            });
            editor.addEventListener('mouseup', (event) => {
                if (! dragStartCell) return;

                if (isDraggingTableCells) {
                    event.preventDefault();
                    window.getSelection()?.removeAllRanges();
                    activeTableCell = event.target.closest?.('td, th') ?? dragStartCell;
                    status.textContent = `${selectedTableCells.size} cells selected`;
                }
                dragStartCell = null;
                isDraggingTableCells = false;
            });
            editor.addEventListener('input', () => {
                normalizeCustomFontSize(editor);
                const isOverflowing = editor.scrollHeight > editor.clientHeight + 2;
                sheet.classList.toggle('has-overflow', isOverflowing);
                if (isOverflowing) status.textContent = 'পৃষ্ঠা পূর্ণ—নতুন পৃষ্ঠা যোগ করুন';
                updateStatistics();
                scheduleSave();
            });
            editor.addEventListener('input', (event) => {
                if (! builtInKeyboard.checked || selectedTypingMode() !== 'unicode' || event.inputType !== 'insertText' || event.data !== ' ') return;
                const selection = window.getSelection();
                const node = selection?.anchorNode;
                if (! node || node.nodeType !== Node.TEXT_NODE) return;
                const caret = selection.anchorOffset;
                const beforeCaret = node.textContent.slice(0, caret);
                const match = beforeCaret.match(/([A-Za-z]+)\s$/);
                if (! match) return;
                const start = caret - match[0].length;
                node.textContent = `${node.textContent.slice(0, start)}${transliteratePhoneticWord(match[1])} ${node.textContent.slice(caret)}`;
                const nextCaret = start + transliteratePhoneticWord(match[1]).length + 1;
                selection.setPosition(node, nextCaret);
                updateStatistics();
                scheduleSave();
            });
            editor.addEventListener('keydown', (event) => {
                const selectionNode = window.getSelection()?.anchorNode;
                const selectionElement = selectionNode?.nodeType === Node.ELEMENT_NODE ? selectionNode : selectionNode?.parentElement;
                const tableCell = selectionElement?.closest('td, th');

                if (event.key === 'Tab' && tableCell && editor.contains(tableCell)) {
                    event.preventDefault();
                    const targetCell = documentTableTabTarget(tableCell, event.shiftKey);
                    if (! targetCell) return;

                    const range = document.createRange();
                    range.selectNodeContents(targetCell);
                    range.collapse(true);
                    const selection = window.getSelection();
                    selection.removeAllRanges();
                    selection.addRange(range);
                    activeTableCell = targetCell;
                    targetCell.focus?.();
                    rememberSelection();
                    scheduleSave();
                    return;
                }

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
                builtInKeyboard.checked = saved.builtInKeyboard ?? true;
            }
            (savedPages?.length ? savedPages : ['<h1>আপনার ডকুমেন্টের শিরোনাম</h1><p>এখানে লেখা শুরু করুন। নতুন পৃষ্ঠা যোগ করতে “পৃষ্ঠা যোগ করুন” বাটনে ক্লিক করুন।</p>'])
                .forEach((content) => addPage(content, false));
            setActiveEditor(editors()[0]);
        };
        const runCommand = (button) => {
            restoreSelection();
            document.execCommand(button.dataset.command, false, button.dataset.value ?? null);
            rememberSelection();
            updateStatistics();
            scheduleSave();
        };
        const clearFormatting = () => {
            restoreSelection();
            document.execCommand('removeFormat', false, null);
            document.execCommand('unlink', false, null);
            document.execCommand('formatBlock', false, 'p');
            rememberSelection();
            updateStatistics();
            scheduleSave();
        };

        restore();
        syncPageDecorations();
        syncTypingMode();
        applyPageSetup();
        updateStatistics();
        updatePageNumbers();

        document.execCommand('styleWithCSS', false, true);
        workspace.querySelectorAll('[data-command]').forEach((button) => {
            button.addEventListener('mousedown', (event) => {
                event.preventDefault();
                rememberSelection();
            });
            button.addEventListener('click', () => runCommand(button));
        });
        workspace.querySelector('[data-clear-format]').addEventListener('mousedown', (event) => {
            event.preventDefault();
            rememberSelection();
        });
        workspace.querySelector('[data-clear-format]').addEventListener('click', clearFormatting);
        workspace.querySelectorAll('[data-format-select]').forEach((select) => select.addEventListener('mousedown', rememberSelection));
        workspace.querySelectorAll('[data-format-select]').forEach((select) => select.addEventListener('change', () => {
            restoreSelection();
            document.execCommand(select.dataset.formatSelect, false, select.value);
            rememberSelection();
            updateStatistics();
            scheduleSave();
        }));
        workspace.querySelectorAll('[data-spacing-property]').forEach((select) => {
            select.addEventListener('mousedown', rememberSelection);
            select.addEventListener('change', () => {
                restoreSelection();
                if (! applyDocumentBlockSpacing(selectedDocumentBlocks(), select.dataset.spacingProperty, select.value)) {
                    status.textContent = 'Spacing প্রয়োগ করতে paragraph-এর মধ্যে cursor রাখুন';
                    return;
                }
                rememberSelection();
                scheduleSave();
            });
        });
        workspace.querySelector('[data-text-color]').addEventListener('mousedown', rememberSelection);
        workspace.querySelector('[data-text-color]').addEventListener('input', (event) => {
            restoreSelection();
            document.execCommand('foreColor', false, event.target.value);
            rememberSelection();
            scheduleSave();
        });
        workspace.querySelector('[data-custom-font-size]').addEventListener('mousedown', rememberSelection);
        workspace.querySelector('[data-custom-font-size]').addEventListener('change', (event) => {
            const fontSize = Math.min(96, Math.max(8, Number(event.target.value) || 12));
            event.target.value = fontSize;
            pendingFontSize = fontSize;
            restoreSelection();
            document.execCommand('styleWithCSS', false, false);
            document.execCommand('fontSize', false, '7');
            normalizeCustomFontSize(activeEditor);
            document.execCommand('styleWithCSS', false, true);
            rememberSelection();
            scheduleSave();
        });
        workspace.querySelector('[data-open-table]').addEventListener('mousedown', (event) => {
            event.preventDefault();
            rememberSelection();
        });
        workspace.querySelector('[data-open-table]').addEventListener('click', () => tableDialog.showModal());
        workspace.querySelector('[data-close-table]').addEventListener('click', () => tableDialog.close());
        workspace.querySelector('[data-insert-table]').addEventListener('click', () => {
            const rows = Math.min(30, Math.max(1, Number(workspace.querySelector('[data-table-rows]').value) || 3));
            const columns = Math.min(12, Math.max(1, Number(workspace.querySelector('[data-table-columns]').value) || 3));
            restoreSelection();
            const selection = window.getSelection();
            const range = selection?.rangeCount ? selection.getRangeAt(0) : null;
            const fragment = range?.createContextualFragment(documentTableMarkup(rows, columns));

            if (range && fragment && activeEditor?.contains(range.commonAncestorContainer)) {
                const lastNode = fragment.lastChild;
                range.deleteContents();
                range.insertNode(fragment);
                range.setStartAfter(lastNode);
                range.collapse(true);
                selection.removeAllRanges();
                selection.addRange(range);
            } else {
                activeEditor?.insertAdjacentHTML('beforeend', documentTableMarkup(rows, columns));
            }
            tableDialog.close();
            updateStatistics();
            scheduleSave();
        });
        workspace.querySelectorAll('[data-table-action]').forEach((button) => {
            button.addEventListener('mousedown', (event) => event.preventDefault());
            button.addEventListener('click', () => {
                if (! activeTableCell?.isConnected || ! activeEditor?.contains(activeTableCell)) {
                    status.textContent = 'আগে টেবিলের একটি ঘরে কার্সর রাখুন';
                    return;
                }

                changeDocumentTable(activeTableCell, button.dataset.tableAction);
                activeTableCell = activeTableCell.isConnected ? activeTableCell : null;
                if (! activeTableCell) {
                    tableContext.hidden = true;
                    setTableToolsVisibility(false);
                    clearTableSelection();
                }
                status.textContent = 'টেবিল আপডেট হয়েছে';
                updateStatistics();
                scheduleSave();
            });
        });
        const tableContext = workspace.querySelector('[data-table-context]');
        const hideTableContext = () => {
            tableContext.hidden = true;
            setTableToolsVisibility(false);
            activeTableCell = null;
        };
        const scheduleTableContextHide = () => {
            window.clearTimeout(tableContextHideTimer);
            tableContextHideTimer = window.setTimeout(hideTableContext, 180);
        };
        const showTableContext = (cell) => {
            window.clearTimeout(tableContextHideTimer);
            activeTableCell = cell;
            const bounds = cell.closest('table').getBoundingClientRect();
            tableContext.hidden = false;
            setTableToolsVisibility(true);
            tableContext.style.left = `${Math.max(8, Math.min(window.innerWidth - tableContext.offsetWidth - 8, bounds.left + (bounds.width - tableContext.offsetWidth) / 2))}px`;
            tableContext.style.top = `${Math.max(8, bounds.top - tableContext.offsetHeight - 8)}px`;
        };
        pages.addEventListener('mouseover', (event) => {
            const cell = event.target.closest?.('td, th');
            if (cell) showTableContext(cell);
        });
        pages.addEventListener('mouseout', (event) => {
            if (event.target.closest?.('td, th') && ! event.relatedTarget?.closest?.('td, th')) scheduleTableContextHide();
        });
        tableContext.addEventListener('mouseenter', () => window.clearTimeout(tableContextHideTimer));
        tableContext.addEventListener('mouseleave', scheduleTableContextHide);
        workspace.querySelectorAll('[data-table-context-action]').forEach((button) => {
            button.addEventListener('mousedown', (event) => event.preventDefault());
            button.addEventListener('click', () => {
                if (! activeTableCell?.isConnected) return;

                const action = button.dataset.tableContextAction;
                const table = activeTableCell.closest('table');
                if (action === 'add-row' || action === 'add-column') {
                    changeDocumentTable(activeTableCell, action);
                } else if (action === 'select-cell') {
                    selectTableCells([activeTableCell]);
                } else if (action === 'select-column') {
                    selectTableCells([...table.rows].map((row) => row.cells[activeTableCell.cellIndex]).filter(Boolean));
                } else if (action === 'merge') {
                    if (! mergeDocumentTableCells(selectedTableCells)) {
                        status.textContent = 'Drag অথবা Ctrl + Click দিয়ে পাশাপাশি থাকা সেল নির্বাচন করুন';
                        return;
                    }
                    activeTableCell = [...selectedTableCells].find((cell) => cell.isConnected) ?? activeTableCell;
                    clearTableSelection();
                } else if (action === 'split') {
                    if (! splitDocumentTableCell(activeTableCell)) {
                        status.textContent = 'Select a merged cell to split';
                        return;
                    }
                } else if (action === 'delete-selected') {
                    deleteDocumentTableCells(selectedTableCells.size ? selectedTableCells : [activeTableCell]);
                    clearTableSelection();
                } else if (action === 'delete-table') {
                    table.remove();
                }
                if (! activeTableCell?.isConnected) hideTableContext();
                status.textContent = 'টেবিল আপডেট হয়েছে';
                updateStatistics();
                scheduleSave();
            });
        });
        workspace.addEventListener('mousedown', (event) => {
            if (! event.target.closest('[data-table-context], td, th, [data-table-action], [data-open-table]')) hideTableContext();
        });
        workspace.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') hideTableContext();
        });
        setTableToolsVisibility(false);
        workspace.querySelector('[data-insert-link]').addEventListener('click', () => {
            rememberSelection();
            const url = window.prompt('লিংকের ঠিকানা লিখুন (যেমন: https://example.com)');
            if (! url) return;
            const normalizedUrl = /^(https?:|mailto:|\/)/i.test(url) ? url : `https://${url}`;
            restoreSelection();
            document.execCommand('createLink', false, normalizedUrl);
            scheduleSave();
        });
        workspace.querySelector('[data-add-page]').addEventListener('click', () => { addPage(); scheduleSave(); });
        [paper, zoom, ...orientationInputs, ...marginInputs].forEach((input) => input.addEventListener('input', () => { applyPageSetup(); scheduleSave(); }));
        [headerInput, footerInput, headerEnabled, footerEnabled].forEach((input) => input.addEventListener('input', () => { syncPageDecorations(); scheduleSave(); }));
        typingModeInputs.forEach((input) => input.addEventListener('change', () => { syncTypingMode(); scheduleSave(); }));
        builtInKeyboard.addEventListener('change', scheduleSave);
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
