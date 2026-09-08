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


document.querySelectorAll('.cart-list .qty-form').forEach(form => {
  form.addEventListener('submit', function (e) {
    e.preventDefault(); // stop the normal page-reloading submission

    const formData = new FormData(form);
    formData.append('ajax', '1'); // tell PHP this is a background request

    fetch('create_cart.php', {
      method: 'POST',
      body: formData
    })
      .then(response => response.json())
      .then(data => {
        const row = form.closest('.cart-row');
        const isRemove = form.classList.contains('remove-form');

        if (isRemove || data.newQty === 0) {
          row.remove(); // item fully removed, delete its row from the page
        } else {
          const qtySpan = row.querySelector('.qty-value');
          qtySpan.textContent = data.newQty;

          const priceElement = row.querySelector('.cart-row-total');
          const unitPrice = parseFloat(priceElement.dataset.price);
          const newTotal = unitPrice * data.newQty;
          priceElement.textContent = '₱' + newTotal.toLocaleString(undefined, { minimumFractionDigits: 2 });
        }

        recalculateSubtotal();
        updateCartBadge(data.cartCount);
      });
  });
});

function recalculateSubtotal() {
  let subtotal = 0;
  document.querySelectorAll('.cart-row-total').forEach(el => {
    subtotal += parseFloat(el.textContent.replace(/[₱,]/g, ''));
  });

  const subtotalElement = document.getElementById('cartSubtotal');
  if (subtotalElement) {
    subtotalElement.textContent = '₱' + subtotal.toLocaleString(undefined, { minimumFractionDigits: 2 });
  }
}

function updateCartBadge(count) {
  const badge = document.querySelector('.nav-cart span');
  if (badge) {
    badge.textContent = count;
  }
}

document.querySelectorAll('.toggle-password').forEach(button => {
  button.addEventListener('click', function () {
    const targetId = button.getAttribute('data-target');
    const input = document.getElementById(targetId);
    const icon = button.querySelector('i');

    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    } else {
      input.type = 'password';
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
    }
  });
});

const searchToggle = document.getElementById('search-toggle');
const searchContainer = document.getElementById('search-container');
const searchInput = document.getElementById('search-input');

if (searchToggle && searchContainer) {
  searchToggle.addEventListener('click', () => {
    searchContainer.classList.toggle('active');

    if (searchContainer.classList.contains('active') && searchInput) {
      searchInput.focus();
    }
  });
}

const lightbox = document.getElementById('lightbox');

if (lightbox) {
  const lightboxImage = document.getElementById('lightboxImage');
  const lightboxClose = document.getElementById('lightboxClose');

  document.querySelectorAll('.lookbook-photo').forEach(photo => {
    photo.addEventListener('click', () => {
      lightboxImage.src = photo.src;
      lightboxImage.alt = photo.alt;
      lightbox.classList.add('active');
    });
  });

  lightboxClose.addEventListener('click', () => {
    lightbox.classList.remove('active');
  });

  lightbox.addEventListener('click', (e) => {
    // Close if clicking the dark background itself, not the image
    if (e.target === lightbox) {
      lightbox.classList.remove('active');
    }
  });
}

// Buy Now modal only exists on shop.php — same defensive guard.
const buyNowOverlay = document.getElementById('buyNowOverlay');

if (buyNowOverlay) {
  const buyNowClose = document.getElementById('buyNowClose');
  const buyNowImage = document.getElementById('buyNowImage');
  const buyNowName = document.getElementById('buyNowName');
  const buyNowPrice = document.getElementById('buyNowPrice');
  const buyNowQtyInput = document.getElementById('buyNowQtyInput');
  const buyNowMinus = document.getElementById('buyNowMinus');
  const buyNowPlus = document.getElementById('buyNowPlus');
  const buyNowProductId = document.getElementById('buyNowProductId');
  const buyNowQtyField = document.getElementById('buyNowQtyField');

  let maxStock = 1;

  document.querySelectorAll('.buy-now-btn').forEach(button => {
    button.addEventListener('click', () => {
      buyNowImage.src = button.dataset.image;
      buyNowName.textContent = button.dataset.name;
      buyNowPrice.textContent = button.dataset.price;
      buyNowProductId.value = button.dataset.id;
      maxStock = parseInt(button.dataset.stock, 10);

      buyNowQtyInput.value = 1;
      buyNowQtyField.value = 1;

      buyNowOverlay.classList.add('active');
    });
  });

  buyNowClose.addEventListener('click', () => {
    buyNowOverlay.classList.remove('active');
  });

  buyNowOverlay.addEventListener('click', (e) => {
    if (e.target === buyNowOverlay) {
      buyNowOverlay.classList.remove('active');
    }
  });

  buyNowMinus.addEventListener('click', () => {
    let qty = parseInt(buyNowQtyInput.value, 10);
    if (qty > 1) {
      qty--;
      buyNowQtyInput.value = qty;
      buyNowQtyField.value = qty;
    }
  });

  buyNowPlus.addEventListener('click', () => {
    let qty = parseInt(buyNowQtyInput.value, 10);
    if (qty < maxStock) {
      qty++;
      buyNowQtyInput.value = qty;
      buyNowQtyField.value = qty;
    }
  });
}

const hamburgerBtn = document.getElementById('hamburgerBtn');
const drawer = document.getElementById('drawer');
const drawerOverlay = document.getElementById('drawerOverlay');
const drawerClose = document.getElementById('drawerClose');

function openDrawer() {
  drawer.classList.add('active');
  drawerOverlay.classList.add('active');
}

function closeDrawer() {
  drawer.classList.remove('active');
  drawerOverlay.classList.remove('active');
}

hamburgerBtn.addEventListener('click', openDrawer);
drawerClose.addEventListener('click', closeDrawer);
drawerOverlay.addEventListener('click', closeDrawer);