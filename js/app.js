let lastScrollY = window.scrollY;
const nav = document.querySelector('.nav');

window.addEventListener('scroll', function () {
  const currentScrollY = window.scrollY;

  // Hide on scroll down, show on scroll up (unchanged from before)
  if (currentScrollY > lastScrollY && currentScrollY > 80) {
    nav.classList.add('nav--hidden');
  } else {
    nav.classList.remove('nav--hidden');
  }

  // New: switch from transparent to solid once scrolled past the hero
  if (currentScrollY > 100) {
    nav.classList.add('nav--scrolled');
  } else {
    nav.classList.remove('nav--scrolled');
  }

  lastScrollY = currentScrollY;
});