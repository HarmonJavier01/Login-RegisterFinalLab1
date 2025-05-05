document.addEventListener('DOMContentLoaded', function () {
    const darkModeToggle = document.getElementById('dark_mode');
    if (darkModeToggle) {
        // Load saved preference
        const darkModeEnabled = localStorage.getItem('darkModeEnabled') === 'true';
        if (darkModeEnabled) {
            document.body.classList.add('dark-mode');
            darkModeToggle.checked = true;
        }

        darkModeToggle.addEventListener('change', function () {
            if (this.checked) {
                document.body.classList.add('dark-mode');
                localStorage.setItem('darkModeEnabled', 'true');
            } else {
                document.body.classList.remove('dark-mode');
                localStorage.setItem('darkModeEnabled', 'false');
            }
        });
    }
});
