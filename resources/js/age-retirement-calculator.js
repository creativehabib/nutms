const parseDate = (value) => {
    const [year, month, day] = value.split('-').map(Number);

    return { year, month, day };
};

const compareDates = (first, second) => {
    if (first.year !== second.year) {
        return first.year - second.year;
    }

    if (first.month !== second.month) {
        return first.month - second.month;
    }

    return first.day - second.day;
};

const daysInMonth = (year, month) => new Date(Date.UTC(year, month, 0)).getUTCDate();

export const calculateCalendarDifference = (startValue, endValue) => {
    const start = parseDate(startValue);
    const end = parseDate(endValue);

    if (compareDates(start, end) > 0) {
        throw new Error('শুরুর তারিখ শেষের তারিখের পরে হতে পারবে না।');
    }

    let years = end.year - start.year;
    let anniversary = parseDate(addCalendarYears(startValue, years));

    if (compareDates(anniversary, end) > 0) {
        years -= 1;
        anniversary = parseDate(addCalendarYears(startValue, years));
    }

    let months = 0;
    let monthAnchor = anniversary;

    while (months < 11) {
        const monthIndex = monthAnchor.month + 1;
        const nextMonthYear = monthAnchor.year + Math.floor((monthIndex - 1) / 12);
        const nextMonth = ((monthIndex - 1) % 12) + 1;
        const nextAnchor = {
            year: nextMonthYear,
            month: nextMonth,
            day: Math.min(start.day, daysInMonth(nextMonthYear, nextMonth)),
        };

        if (compareDates(nextAnchor, end) > 0) {
            break;
        }

        monthAnchor = nextAnchor;
        months += 1;
    }

    const anchorTime = Date.UTC(monthAnchor.year, monthAnchor.month - 1, monthAnchor.day);
    const endTime = Date.UTC(end.year, end.month - 1, end.day);
    const days = Math.round((endTime - anchorTime) / 86400000);

    return { years, months, days };
};

export const addCalendarYears = (dateValue, yearsToAdd) => {
    const date = parseDate(dateValue);
    const retirementYear = date.year + Number(yearsToAdd);
    const retirementDay = Math.min(date.day, daysInMonth(retirementYear, date.month));

    return `${retirementYear}-${String(date.month).padStart(2, '0')}-${String(retirementDay).padStart(2, '0')}`;
};

const formatBanglaNumber = (value) => new Intl.NumberFormat('bn-BD', { useGrouping: false }).format(value);

const formatDuration = ({ years, months, days }) => `${formatBanglaNumber(years)} বছর ${formatBanglaNumber(months)} মাস ${formatBanglaNumber(days)} দিন`;

const formatDate = (dateValue) => new Intl.DateTimeFormat('bn-BD', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    timeZone: 'UTC',
}).format(new Date(`${dateValue}T00:00:00Z`));

export const initializeAgeRetirementCalculators = () => {
    document.querySelectorAll('[data-age-retirement-calculator]').forEach((calculator) => {
        if (calculator.dataset.initialized === 'true') {
            return;
        }

        const form = calculator.querySelector('form');
        const result = calculator.querySelector('[data-calculation-result]');
        const placeholder = calculator.querySelector('[data-result-placeholder]');
        const status = calculator.querySelector('[data-calculation-status]');

        form.addEventListener('submit', (event) => {
            event.preventDefault();

            const birthDate = form.elements.birthDate.value;
            const calculationDate = form.elements.calculationDate.value;
            const joiningDate = form.elements.joiningDate.value;

            try {
                const age = calculateCalendarDifference(birthDate, calculationDate);
                const retirementDate = addCalendarYears(birthDate, form.elements.retirementAge.value);
                const hasRetired = compareDates(parseDate(retirementDate), parseDate(calculationDate)) <= 0;

                result.querySelector('[data-age-result]').textContent = formatDuration(age);
                result.querySelector('[data-retirement-date]').textContent = formatDate(retirementDate);

                const remainingContainer = result.querySelector('[data-retirement-remaining-container]');
                if (hasRetired) {
                    remainingContainer.hidden = true;
                    status.textContent = 'নির্বাচিত হিসাবের তারিখটি সম্ভাব্য অবসরের তারিখের পরে।';
                } else {
                    remainingContainer.hidden = false;
                    result.querySelector('[data-retirement-remaining]').textContent = formatDuration(calculateCalendarDifference(calculationDate, retirementDate));
                    status.textContent = 'হিসাব সম্পন্ন হয়েছে।';
                }

                const serviceContainer = result.querySelector('[data-service-container]');
                if (joiningDate) {
                    serviceContainer.hidden = false;
                    result.querySelector('[data-service-result]').textContent = formatDuration(calculateCalendarDifference(joiningDate, calculationDate));
                } else {
                    serviceContainer.hidden = true;
                }

                result.hidden = false;
                placeholder.hidden = true;
            } catch (error) {
                result.hidden = true;
                placeholder.hidden = false;
                status.textContent = error.message;
            }
        });

        calculator.dataset.initialized = 'true';
    });
};
