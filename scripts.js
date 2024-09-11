document.addEventListener('DOMContentLoaded', () => {
    const accordions = document.querySelectorAll('.accordion-button');
    accordions.forEach(button => {
        button.addEventListener('click', () => {
            const content = button.nextElementSibling;
            content.style.display = content.style.display === 'block' ? 'none' : 'block';
        });
    });

    if (document.cookie.includes('theme=dark')) {
        document.body.classList.add('dark-mode');
    }
});

function toggleTheme() {
    document.body.classList.toggle('dark-mode');
    document.cookie = `theme=${document.body.classList.contains('dark-mode') ? 'dark' : 'light'}`;
}
