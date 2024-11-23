const calendar = document.querySelector('.calendar');
const currentMonthYear = document.getElementById('currentMonthYear');
const prevMonthBtn = document.getElementById('prevMonth');
const nextMonthBtn = document.getElementById('nextMonth');

let currentDate = new Date();
let selectedDate = currentDate.getDate(); // Set selectedDate to today's date
let selectedYear = currentDate.getFullYear(); // Set selectedYear to the current year
let selectedMonth = currentDate.getMonth(); // Set selectedMonth to the current month


function renderCalendar(date) {
    calendar.querySelectorAll('.day').forEach(day => day.remove());
    const year = date.getFullYear();
    const month = date.getMonth();
    currentMonthYear.textContent = `${date.toLocaleString('default', { month: 'long' })} ${year}`;

    const firstDayOfMonth = new Date(year, month, 1).getDay();
    const adjustedFirstDay = (firstDayOfMonth === 0 ? 6 : firstDayOfMonth - 1); // Adjust to start from Monday

    const daysInMonth = new Date(year, month + 1, 0).getDate();

    for (let i = 0; i < adjustedFirstDay; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.classList.add('day');
        calendar.appendChild(emptyCell);
    }

    for (let day = 1; day <= daysInMonth; day++) {
        const dayElement = document.createElement('div');
        dayElement.textContent = day;
        dayElement.classList.add('day', 'date-cell');
        
        // Apply the selected class if it matches the stored date
        if (selectedDate === day && selectedYear === year && selectedMonth === month) {
            dayElement.classList.add('selected');
        }

        dayElement.addEventListener('click', () => selectDate(year, month, day));
        calendar.appendChild(dayElement);
    }
}

function selectDate(year, month, day) {
    if (selectedDate) {
        document.querySelector('.day.selected')?.classList.remove('selected');
    }
    selectedDate = day;
    selectedYear = year;
    selectedMonth = month;
    event.target.classList.add('selected');
    const dateString = `${year}-${month + 1}-${day}`;
    console.log('Date selected:', dateString);
    getWorkSessionsByDate(dateString);
}

prevMonthBtn.addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar(currentDate);
});

nextMonthBtn.addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar(currentDate);
});

renderCalendar(currentDate);