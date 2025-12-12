document.addEventListener('DOMContentLoaded', () => {
    const navbar = document.querySelector('.navbar');
    let lastScrollY = window.scrollY;

    window.addEventListener('scroll', () => {
        if (window.scrollY > lastScrollY && window.scrollY > 100) {
            // Scrolling DOWN -> Hide Navbar
            navbar.classList.add('navbar--hidden');
        } else {
            // Scrolling UP -> Show Navbar
            navbar.classList.remove('navbar--hidden');
        }
        lastScrollY = window.scrollY;
    });
});
