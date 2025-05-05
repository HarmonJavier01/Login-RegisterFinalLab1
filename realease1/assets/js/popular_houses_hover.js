document.addEventListener('DOMContentLoaded', function () {
    const popularHouseImages = document.querySelectorAll('.popular-houses-section .card-img-top');
    popularHouseImages.forEach(img => {
        img.addEventListener('mouseenter', () => {
            img.style.transition = 'transform 0.3s ease-in-out';
            img.style.transform = 'scale(1.05)';
            img.style.cursor = 'pointer';
        });
        img.addEventListener('mouseleave', () => {
            img.style.transform = 'scale(1)';
        });
    });
});
