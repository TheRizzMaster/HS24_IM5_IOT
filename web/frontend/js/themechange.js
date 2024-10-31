const themeToggle = document.querySelector('.input');
const body = document.body;

// Gemerktes Theme aus dem localStorage laden
const savedTheme = localStorage.getItem('theme');
if (savedTheme === 'dark') {
    body.classList.add('dark-mode');
    themeToggle.checked = true;
}

// Theme wechseln und im localStorage speichern
themeToggle.addEventListener('change', () => {
    body.classList.toggle('dark-mode');
    const theme = body.classList.contains('dark-mode') ? 'dark' : 'light';
    localStorage.setItem('theme', theme);
});
