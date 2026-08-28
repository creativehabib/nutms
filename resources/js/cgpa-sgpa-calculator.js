export const gradeFromMarks = (marks) => {
    if (marks === '') {
        return null;
    }

    const numericMarks = Number(marks);

    if (! Number.isFinite(numericMarks) || numericMarks < 0 || numericMarks > 100) {
        return null;
    }

    const scale = [
        [80, 'A+', 4],
        [75, 'A', 3.75],
        [70, 'A-', 3.5],
        [65, 'B+', 3.25],
        [60, 'B', 3],
        [55, 'B-', 2.75],
        [50, 'C+', 2.5],
        [45, 'C', 2.25],
        [40, 'D', 2],
        [0, 'F', 0],
    ];
    const [, letter, point] = scale.find(([minimum]) => numericMarks >= minimum);

    return { letter, point };
};

export const calculateWeightedGpa = (entries) => {
    const validEntries = entries.filter(({ point, credits }) => Number.isFinite(point)
        && Number.isFinite(credits)
        && credits > 0);
    const credits = validEntries.reduce((total, entry) => total + entry.credits, 0);
    const qualityPoints = validEntries.reduce((total, entry) => total + (entry.point * entry.credits), 0);

    return {
        credits,
        qualityPoints,
        gpa: credits > 0 ? qualityPoints / credits : 0,
    };
};

const gradeOptions = [
    ['A+', 4], ['A', 3.75], ['A-', 3.5], ['B+', 3.25], ['B', 3],
    ['B-', 2.75], ['C+', 2.5], ['C', 2.25], ['D', 2], ['F', 0],
];

const courseRow = (index) => `
    <div data-course-row class="grid items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900 sm:grid-cols-[1fr_110px_130px_90px_42px]">
        <label class="text-xs font-bold text-slate-500 dark:text-slate-400">কোর্সের নাম
            <input data-course-name value="কোর্স ${index}" class="mt-1.5 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-normal text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
        </label>
        <label class="text-xs font-bold text-slate-500 dark:text-slate-400">ক্রেডিট
            <input data-course-credit type="number" min="0.5" max="20" step="0.5" value="3" class="mt-1.5 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-normal text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
        </label>
        <label data-marks-field class="text-xs font-bold text-slate-500 dark:text-slate-400">প্রাপ্ত নম্বর
            <input data-course-marks type="number" min="0" max="100" step="0.01" placeholder="০–১০০" class="mt-1.5 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-normal text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
        </label>
        <label data-grade-field hidden class="text-xs font-bold text-slate-500 dark:text-slate-400">গ্রেড
            <select data-course-grade class="mt-1.5 w-full rounded-lg border border-slate-200 bg-slate-50 px-2 py-2 text-sm font-normal text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                <option value="">—</option>${gradeOptions.map(([letter, point]) => `<option value="${point}">${letter}</option>`).join('')}
            </select>
        </label>
        <div class="rounded-lg bg-slate-100 px-2 py-2 text-center dark:bg-slate-800">
            <span data-row-grade class="block text-xs font-extrabold text-slate-700 dark:text-slate-200">—</span>
            <span data-row-point class="block text-[10px] text-slate-500">০.০০</span>
        </div>
        <button type="button" data-remove-course class="flex size-10 items-center justify-center rounded-lg text-red-500 transition hover:bg-red-50 dark:hover:bg-red-950/30" aria-label="কোর্স মুছুন">×</button>
    </div>`;

const semesterRow = (index) => `
    <div data-semester-row class="grid items-end gap-3 rounded-xl border border-slate-200 p-3 dark:border-slate-700 sm:grid-cols-[1fr_130px_130px_42px]">
        <label class="text-xs font-bold text-slate-500">সেমিস্টার/বর্ষ<input value="পর্ব ${index}" class="mt-1.5 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-normal dark:border-slate-700 dark:bg-slate-950"></label>
        <label class="text-xs font-bold text-slate-500">SGPA<input data-semester-gpa type="number" min="0" max="4" step="0.01" class="mt-1.5 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-normal dark:border-slate-700 dark:bg-slate-950"></label>
        <label class="text-xs font-bold text-slate-500">মোট ক্রেডিট<input data-semester-credits type="number" min="0.5" step="0.5" class="mt-1.5 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-normal dark:border-slate-700 dark:bg-slate-950"></label>
        <button type="button" data-remove-semester class="flex size-10 items-center justify-center rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30" aria-label="পর্ব মুছুন">×</button>
    </div>`;

export const initializeCgpaSgpaCalculators = () => {
    document.querySelectorAll('[data-cgpa-sgpa-calculator]').forEach((calculator) => {
        if (calculator.dataset.initialized === 'true') {
            return;
        }

        const courses = calculator.querySelector('[data-course-list]');
        const semesters = calculator.querySelector('[data-semester-list]');
        const modeInputs = calculator.querySelectorAll('[name="resultInputMode"]');

        const calculate = () => {
            const mode = calculator.querySelector('[name="resultInputMode"]:checked').value;
            const currentEntries = [...courses.querySelectorAll('[data-course-row]')].map((row) => {
                const credits = Number(row.querySelector('[data-course-credit]').value);
                const marks = row.querySelector('[data-course-marks]').value;
                const gradeSelect = row.querySelector('[data-course-grade]');
                const grade = mode === 'marks'
                    ? gradeFromMarks(marks)
                    : gradeSelect.value === '' ? null : gradeOptions.find(([, point]) => point === Number(gradeSelect.value));
                const point = mode === 'marks' ? grade?.point : grade?.[1];
                const letter = mode === 'marks' ? grade?.letter : grade?.[0];

                row.querySelector('[data-row-grade]').textContent = letter ?? '—';
                row.querySelector('[data-row-point]').textContent = Number.isFinite(point) ? point.toFixed(2) : '০.০০';
                return { point, credits };
            });
            const sgpa = calculateWeightedGpa(currentEntries);
            const previousEntries = [...semesters.querySelectorAll('[data-semester-row]')].map((row) => ({
                point: Number(row.querySelector('[data-semester-gpa]').value),
                credits: Number(row.querySelector('[data-semester-credits]').value),
            }));
            const cgpa = calculateWeightedGpa([...previousEntries, ...(sgpa.credits ? [{ point: sgpa.gpa, credits: sgpa.credits }] : [])]);

            calculator.querySelector('[data-sgpa-result]').textContent = sgpa.gpa.toFixed(2);
            calculator.querySelector('[data-cgpa-result]').textContent = cgpa.gpa.toFixed(2);
            calculator.querySelector('[data-current-credits]').textContent = sgpa.credits.toFixed(1);
            calculator.querySelector('[data-total-credits]').textContent = cgpa.credits.toFixed(1);
        };

        const syncMode = () => {
            const usesMarks = calculator.querySelector('[name="resultInputMode"]:checked').value === 'marks';
            calculator.querySelectorAll('[data-marks-field]').forEach((field) => { field.hidden = ! usesMarks; });
            calculator.querySelectorAll('[data-grade-field]').forEach((field) => { field.hidden = usesMarks; });
            calculate();
        };

        const addCourse = () => { courses.insertAdjacentHTML('beforeend', courseRow(courses.children.length + 1)); syncMode(); };
        const addSemester = () => { semesters.insertAdjacentHTML('beforeend', semesterRow(semesters.children.length + 1)); calculate(); };

        calculator.querySelector('[data-add-course]').addEventListener('click', addCourse);
        calculator.querySelector('[data-add-semester]').addEventListener('click', addSemester);
        modeInputs.forEach((input) => input.addEventListener('change', syncMode));
        calculator.addEventListener('input', calculate);
        calculator.addEventListener('change', calculate);
        calculator.addEventListener('click', (event) => {
            if (event.target.closest('[data-remove-course]')) {
                event.target.closest('[data-course-row]').remove();
            }
            if (event.target.closest('[data-remove-semester]')) {
                event.target.closest('[data-semester-row]').remove();
            }
            calculate();
        });

        addCourse();
        addCourse();
        addCourse();
        calculator.dataset.initialized = 'true';
    });
};
