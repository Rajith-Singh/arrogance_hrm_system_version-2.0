const darkModeToggle = document.getElementById('dark-mode-toggle');
const body = document.body;

darkModeToggle.addEventListener('change', () => {
    body.classList.toggle('dark-mode');
    document.querySelector('header').classList.toggle('dark-mode');
    document.querySelector('main').classList.toggle('dark-mode');
    document.querySelector('footer').classList.toggle('dark-mode');
    
    // Toggle dark mode for the features section
    const featuresSection = document.querySelector('.features');
    if (featuresSection) {
        featuresSection.classList.toggle('dark-mode');
        document.querySelectorAll('.feature-item').forEach(item => {
            item.classList.toggle('dark-mode');
        });
    }

    // Toggle dark mode for the CTA section
    const ctaSection = document.querySelector('.cta');
    if (ctaSection) {
        ctaSection.classList.toggle('dark-mode');
    }

    // Change background image based on mode
    if (darkModeToggle.checked) {
        body.style.backgroundImage = "url('background_dark.jpg')";
        document.querySelector('.slider').style.backgroundColor = '#EC1F27';
    } else {
        body.style.backgroundImage = "url('background.jpg')";
        document.querySelector('.slider').style.backgroundColor = '#ccc';
    }
});
