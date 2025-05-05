document.addEventListener('DOMContentLoaded', function () {
    const viewDetailsLinks = document.querySelectorAll('.popular-houses-section .btn-link');
    viewDetailsLinks.forEach(link => {
        link.addEventListener('mouseenter', () => {
            link.style.transition = 'transform 0.2s ease-in-out, color 0.2s ease-in-out';
            link.style.transform = 'scale(1.1)';
            link.style.color = '#0056b3'; // Darker blue on hover
            link.style.cursor = 'pointer';
        });
        link.addEventListener('mouseleave', () => {
            link.style.transform = 'scale(1)';
            link.style.color = ''; // Reset to original color
        });
    });
});
