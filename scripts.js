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

document.addEventListener('DOMContentLoaded', () => {
    const themeCheckbox = document.querySelector('.theme-checkbox'); // Select the checkbox

    // Check the saved theme in cookies and apply it
    const savedTheme = getCookie('theme');

    // Apply the theme and update the checkbox state
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        themeCheckbox.checked = true; // Set checkbox to checked for dark mode
    } else {
        document.body.classList.remove('dark-mode');
        themeCheckbox.checked = false; // Set checkbox to unchecked for light mode
    }

    // Listen for checkbox clicks to toggle theme
    themeCheckbox.addEventListener('click', () => {
        toggleTheme();
    });
});

// Function to toggle the theme and store the choice in a cookie
function toggleTheme() {
    const themeCheckbox = document.querySelector('.theme-checkbox');
    const isDarkMode = themeCheckbox.checked; // Check if checkbox is checked

    // Toggle dark-mode class on the body
    document.body.classList.toggle('dark-mode', isDarkMode);

    // Set the cookie with the current theme
    document.cookie = `theme=${isDarkMode ? 'dark' : 'light'};path=/;expires=${getCookieExpirationDate()}`;
}

// Function to get a cookie value by its name
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

// Function to set the cookie expiration date (1 year)
function getCookieExpirationDate() {
    const date = new Date();
    date.setFullYear(date.getFullYear() + 1); // Set the cookie to expire in 1 year
    return date.toUTCString();
}
