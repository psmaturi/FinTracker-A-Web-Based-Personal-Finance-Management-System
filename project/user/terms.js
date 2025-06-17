document.querySelectorAll('.feature-section').forEach(section => {
    section.addEventListener('mouseover', () => {
        section.style.boxShadow = '0 0 15px rgba(0, 0, 0, 0.2)';
    });

    section.addEventListener('mouseout', () => {
        section.style.boxShadow = '0 0 10px rgba(0, 0, 0, 0.1)';
    });
});
