document.addEventListener('DOMContentLoaded', function () {
    const favoriteImages = document.querySelectorAll('.card-img-top');
    favoriteImages.forEach(img => {
        img.addEventListener('mouseenter', () => {
            img.style.transition = 'transform 0.3s ease-in-out';
            img.style.transform = 'scale(1.05)';
            img.style.cursor = 'pointer';
        });
        img.addEventListener('mouseleave', () => {
            img.style.transform = 'scale(1)';
        });
    });

    const viewDetailsButtons = document.querySelectorAll('.btn-primary');
    viewDetailsButtons.forEach(btn => {
        btn.addEventListener('mouseenter', () => {
            btn.style.transition = 'transform 0.2s ease-in-out, background-color 0.2s ease-in-out';
            btn.style.transform = 'scale(1.1)';
            btn.style.backgroundColor = '#004085'; // Darker blue on hover
            btn.style.cursor = 'pointer';
        });
        btn.addEventListener('mouseleave', () => {
            btn.style.transform = 'scale(1)';
            btn.style.backgroundColor = ''; // Reset to original
        });
    });

    const removeButtons = document.querySelectorAll('.btn-outline-danger');
    removeButtons.forEach(btn => {
        btn.addEventListener('mouseenter', () => {
            btn.style.transition = 'transform 0.2s ease-in-out, background-color 0.2s ease-in-out, color 0.2s ease-in-out';
            btn.style.transform = 'scale(1.1)';
            btn.style.backgroundColor = '#dc3545'; // Bootstrap danger color
            btn.style.color = '#fff';
            btn.style.cursor = 'pointer';
        });
        btn.addEventListener('mouseleave', () => {
            btn.style.transform = 'scale(1)';
            btn.style.backgroundColor = '';
            btn.style.color = '';
        });
    });
});
